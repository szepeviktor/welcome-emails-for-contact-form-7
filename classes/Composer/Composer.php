<?php

namespace Automation\Composer;

use Automation\Logger;
use Automation\Utils;

defined('ABSPATH') || exit;

/**
 * @todo Add a block type validation method to be used on ajax_block() and refresh()
 * @todo Define a Logger class only for the Composer to decouple
 */
class Composer {

    static function init() {
        // By now we limit the usage to the blog administrator
        if (current_user_can('administrator')) {
            add_action('wp_ajax_composer_save', [self::class, 'ajax_save']);
            add_action('wp_ajax_composer_test', [self::class, 'ajax_test']);
            add_action('wp_ajax_composer_block', [self::class, 'ajax_block']);
            add_action('wp_ajax_composer_form', [self::class, 'ajax_form']);
            add_action('wp_ajax_composer_get', [self::class, 'ajax_get']);
        }
    }

    static function encode_options($options) {
        if (!is_array($options)) {
            $options = [];
        }
        return base64_encode(wp_json_encode($options));
    }

    static function decode_options($options) {
        $o = json_decode(base64_decode($options), true);
        if (!is_array($o)) {
            return [];
        }
        return $o;
    }

    static function get_block_defaults($type) {
        $file = self::get_block_file($type, 'defaults');
        if (file_exists($file)) {
            include $file;
        }
        if (empty($defaults) || !is_array($defaults)) {
            $defaults = [];
        }
        return $defaults;
    }

    static function get_block_file($type, $file) {
        $type = sanitize_key($type);
        $file = sanitize_key($file);
        return wp_normalize_path(realpath(AUTOMATION_DIR . '/composer/blocks/' . $type . '/' . $file . '.php'));
    }

    static function get_request_options() {
        if (!empty($_REQUEST['options']) && is_array($_REQUEST['options'])) {
            // To the review team: this is a "first level" sanitization since different blocks have different options and the
            // type is not defined. Then, when the options are used, every block do specific sanitization.
            $options = wp_kses_post_deep(wp_unslash($_REQUEST['options']));
        }
        if (empty($options) || !is_array($options)) {
            $options = [];
        }

        // When not specified we unset to grant the merge falls back to the default
        if (empty($options['block_background'])) {
            unset($options['block_background']);
        }
        return $options;
    }

    static function inline_css($content) {
        $matches = [];
        // "s" skips line breaks
        $styles = preg_match('|<style>(.*?)</style>|s', $content, $matches);
        if (isset($matches[1])) {
            $style = str_replace(["\n", "\r"], '', $matches[1]);
            $rules = [];
            preg_match_all('|\s*\.(.*?)\{(.*?)\}\s*|s', $style, $rules);
            for ($i = 0; $i < count($rules[1]); $i++) {
                $class = trim($rules[1][$i]);
                $value = trim($rules[2][$i]);
                $value = preg_replace('|\s+|', ' ', $value);
                $content = str_replace(' inline-class="' . $class . '"', ' style="' . esc_attr($value) . '"', $content);
            }
        }
        return trim(preg_replace('|<style>.*?</style>|s', '', $content));
    }

    static function ajax_block() {
        check_ajax_referer('composer');
        header('Content-Type: application/json;charset=UTF-8');
        $block_type = sanitize_key($_REQUEST['type']);

        // Get the email wide settings (todo) which affect single blocks
        $email_options = ['block_background' => '#ffffff'];

        $options = array_merge($email_options, self::get_block_defaults($block_type), self::get_request_options());

        $file = self::get_block_file($block_type, 'block');
        if (!file_exists($file)) {
            echo wp_json_encode(['html' => 'Block not found', 'form' => '']);
            die();
        }

        ob_start();
        include $file;
        $html = trim(ob_get_clean());

        $td_style = '';
        //$td_style = 'text-align: center; ';
        //$td_style .= 'width: 100% !important; ';
        $td_style .= 'line-height: normal !important; ';
        $td_style .= 'letter-spacing: normal; ';

        $td_style .= 'padding: ' . $options['block_padding_top'] . 'px ' . $options['block_padding_right'] . 'px ' . $options['block_padding_bottom'] . 'px ' .
                $options['block_padding_left'] . 'px;';

        $html = '<table border="0" width="100%" style="max-width: 600px" cellpadding="0" cellspacing="0" border="0" align="center" bgcolor="' . esc_attr($options['block_background']) . '"><tr>'
                . '<td style="' . esc_attr($td_style) . '" class="cmpe-block cmpe-block-' . esc_attr($block_type) . '">' . $html . '</td></tr></table>';

        $html = self::inline_css($html);

        // Attention: here internal variables could have been changed by the included block, move the inclusion in
        // a separated method to isolate it!

        $file = self::get_block_file($block_type, 'form');
        $controls = new Controls($options);
        ob_start();
        include $file;
        $form = ob_get_clean();

        $options['block_type'] = $block_type;

        echo wp_json_encode(['html' => $html, 'form' => $form, 'options' => self::encode_options($options)]);
        die();
    }

    static function ajax_form() {

        check_ajax_referer('composer');

        header('Content-Type: text/html;charset=UTF-8');
        $block_type = sanitize_key($_REQUEST['type']);

        $defaults = self::get_block_defaults($block_type);

        $options = json_decode(base64_decode(wp_unslash($_POST['options'])), true);

        if (empty($options) || !is_array($options)) {
            $options = [];
        }

        $options = wp_kses_post_deep($options);

        $options = array_merge($defaults, $options);

        $file = self::get_block_file($block_type, 'form');
        if (!file_exists($file)) {
            echo 'Form not found';
            die();
        }
        $controls = new Controls($options);
        include $file;
        die();
    }

    static function ajax_get() {
        global $wpdb;
        header('Content-Type: application/json;charset=UTF-8');

        $id = (int) $_GET['id'];
        $email = Utils::db_get_row($wpdb->prepare("select * from {$wpdb->prefix}automation_emails where id=%d", $id));

        self::refresh($email);

        echo wp_json_encode(['html' => $email->html, 'subject' => $email->subject]);
        die();
    }

    static function ajax_save() {
        global $wpdb;

        check_ajax_referer('composer');

        $id = (int) $_POST['id'];
        $data['html'] = wp_kses_post(wp_unslash($_POST['html']));

        $subject = wp_strip_all_tags(wp_unslash($_POST['subject']));
        $data['subject'] = Utils::sanitize_subject($subject);

        Utils::db_update($wpdb->prefix . 'automation_emails', $data, ['id' => $id]);
    }

    /**
     * @todo Extract the HTML cleanup in common with build_message()
     */
    static function ajax_test() {

        check_ajax_referer('composer');

        $email = sanitize_email($_POST['email']);

        if (!is_email($email)) {
            Logger::error('Invalid test email: ' . $email);
            die('Invalid email');
        }

        // This block of code creates a FULL HTML to be sent via email, this is not
        // like a post content.
        $html = wp_kses_post(wp_unslash($_POST['html']));
        $html = preg_replace('/data-options=".*?"/is', '', $html);
        $html = preg_replace('/  +/s', ' ', $html);
        $html = str_replace('cmp-block-type ui-draggable ui-draggable-handle', '', $html);
        $html = self::get_html_open() . $html . self::get_html_close();

        $subject = wp_strip_all_tags(wp_unslash($_POST['subject']), true);
        $headers[] = 'Content-Type: text/html;charset=utf8';
        $email = \Automation\Settings::get_sender_email();
        if ($email) {
            $headers[] = 'From: ' . \Automation\Settings::get_sender_name() . ' <' . $email . '>';
        }

        add_action('wp_mail_failed', function ($e) {
            Logger::error($e);
        });

        $r = wp_mail($email, $subject, $html, $headers);

        die($r ? '' : 'Error');
    }

    /**
     *
     * @global \wpdb $wpdb
     * @param \Automation\Email $email
     */
    static function save($email) {
        global $wpdb;
        $data = (array) $email;
        if (!empty($data['id'])) {
            $id = $data['id'];
            unset($data['id']);
            Utils::db_update($wpdb->prefix . 'automation_emails', $data, ['id' => $id]);
        } else {
            unset($data['id']);
            $wpdb->insert($wpdb->prefix . 'automation_emails', $data);
            $email->id = $wpdb->insert_id;
            if ($wpdb->last_error) {
                //die($wpdb->last_error);
                die();
            }
        }
    }

    static function get_email($id) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("select * from {$wpdb->prefix}automation_emails where id=%d limit 1", (int) $id));
    }

    /**
     * @todo Define and use the Message class
     * @param type $id
     * @param type $data
     * @return type
     */
    static function build_message($id, $data) {
        $email = self::get_email($id);
        if (!$email) {
            return null;
        }
        $email->html = preg_replace('/data-options=".*?"/is', '', $email->html);
        $email->html = preg_replace('/  +/s', ' ', $email->html);
        $email->html = str_replace('cmp-block-type ui-draggable ui-draggable-handle', '', $email->html);
        $email->html = self::get_html_open() . $email->html . self::get_html_close();

        foreach ($data as $k => $v) {
            $email->html = str_replace('{' . $k . '}', esc_html($v), $email->html);
            //$email->text = str_replace('{' . $k . '}', $v, $email->text);
            $email->subject = str_replace('{' . $k . '}', $v, $email->subject);
        }

        $email->email_id = $email->id;
        return $email;
    }

    static function get_html_open() {
        $width = 600;

        $open = '<!DOCTYPE html>' . "\n";
        $open .= '<html xmlns="https://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">' . "\n";
        $open .= '<head>' . "\n";
        $open .= '<title></title>' . "\n";
        $open .= '<meta charset="utf-8">' . "\n";
        $open .= '<meta name="viewport" content="width=device-width, initial-scale=1">' . "\n";
        $open .= '<meta http-equiv="X-UA-Compatible" content="IE=edge">' . "\n";
        $open .= '<meta name="format-detection" content="address=no">' . "\n";
        $open .= '<meta name="format-detection" content="telephone=no">' . "\n";
        $open .= '<meta name="format-detection" content="email=no">' . "\n";
        $open .= '<meta name="x-apple-disable-message-reformatting">' . "\n";
        $open .= '<!--[if gte mso 9]><xml><o:OfficeDocumentSettings><o:AllowPNG/><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml><![endif]-->' . "\n";
        $open .= '<style type="text/css">' . "\n";
        $open .= file_get_contents(AUTOMATION_DIR . '/composer/email.css');
        $open .= "\n</style>\n";
        $open .= "</head>\n";
        $open .= '<body style="margin: 0; padding: 0; line-height: normal; word-spacing: normal;" dir="' . (is_rtl() ? 'rtl' : 'ltr') . '">';
        $open .= "\n";
        //$open .= self::get_html_preheader($email);

        $open .= '<!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" align="center" cellspacing="0" width="'
                . ((int) $width) . '"><tr><td width="' . ((int) $width) . '" style="vertical-align:top;width:' . ((int) $width) . 'px;"><![endif]-->';

        return $open;
    }

    static function get_html_close() {
        return "\n<!--[if mso | IE]></td></tr></table><![endif]-->\n</body>\n</html>";
    }

    /**
     * @todo Create method for block rendering to be used here and on ajax_block()
     * @param \Automation\Email $email
     * @return bool
     */
    static function refresh($email) {
        Logger::debug('Refreshing email ' . $email->id);

        preg_match_all('/data-options="(.*?)"/m', $email->html, $matches, PREG_PATTERN_ORDER);

        //Logger::debug('Found ' . count($matches[1]) . ' blocks');

        $result = '';
        foreach ($matches[1] as $match) {
            $options = self::decode_options($match);

            //Logger::debug($options);

            $type = sanitize_key($options['block_type']);

            Logger::debug('Regenerating block ' . $type);

            $file = self::get_block_file($type, 'block');
            if (!file_exists($file)) {
                $html = 'Block not found';
                //Logger::debug('Block not found');
            } else {
                ob_start();
                include $file;
                $html = trim(ob_get_clean());
                $html = self::inline_css($html);
            }

            $wrapper = '<div class="cmp-block-type ui-draggable ui-draggable-handle" data-type="' .
                    esc_attr($type) . '" style="position: relative; height: auto;" data-options="' .
                    esc_attr($match) . '">';

            $td_style = '';
            //$td_style = 'text-align: center; ';
            //$td_style .= 'width: 100% !important; ';
            $td_style .= 'line-height: normal !important; ';
            $td_style .= 'letter-spacing: normal; ';

            $td_style .= 'padding: ' . ((int) $options['block_padding_top']) . 'px ' . ((int) $options['block_padding_right']) . 'px ' . ((int) $options['block_padding_bottom']) . 'px ' .
                    $options['block_padding_left'] . 'px;';

            $html = $wrapper . '<table border="0" width="100%" style="max-width: 600px" cellpadding="0" cellspacing="0" border="0" align="center" bgcolor="' . esc_attr($options['block_background']) . '"><tr>'
                    . '<td style="' . esc_attr($td_style) . '">' . wp_kses_post($html) . '</td></tr></table></div>';

            $result .= $html;
        }
        $email->html = $result;

        //Logger::debug('Refresh completed');
    }
}
