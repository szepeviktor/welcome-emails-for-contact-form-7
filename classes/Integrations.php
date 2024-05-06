<?php

namespace Automation;

defined('ABSPATH') || exit;

use AutomationApi;

/**
 * Code below is NOT official, do not use to add your own integration until the
 * specifications have been published on our site.
 */
class Integrations {

    static function init() {
        //add_action('automation_register_event_types', [self::class, 'register']);

        self::init_cf7();

    }


    /**
     * Initialize the CF7 integration.
     */
    static function init_cf7() {
        if (!class_exists('WPCF7_ContactForm')) {
            return;
        }
        $forms = get_posts(['post_type' => 'wpcf7_contact_form', 'posts_per_page' => 100]);

        foreach ($forms as $f) {
            $form = \WPCF7_ContactForm::get_instance($f->ID);
            if (method_exists($form, 'scan_form_tags')) {
                $form_fields = $form->scan_form_tags();
            } else {
                $form_fields = $form->scan_form_tags();
            }
            $fields = [];
            foreach ($form_fields as $form_field) {
                $field_name = str_replace('[]', '', $form_field['name']);
                if (empty($field_name)) {
                    continue;
                }
                $fields[$field_name] = $field_name;
            }

            $fields['email'] = 'Email';

            AutomationApi::register_event_type('cf7_' . sanitize_key($form->id()), 'CF7 Form "' . $form->title() . '" has been submitted', $fields);
        }

        // Reset the CF7 instance otherwise CF7 does not work correctly
        \WPCF7_ContactForm::get_instance(null);

        // Maybe to be replaced with "wpcf7_submit"
        add_action('wpcf7_before_send_mail', function ($form, &$abort, $submission) {

            // To review team: data is from CF7 plugin, we store it on a extra table to send
            // the notification emails so it can be included with special tags on emails.
            $data = wp_strip_all_tags(wp_unslash($_POST), true);

            self::prepare($data);

            AutomationApi::fire_event('cf7_' . $form->id(), $data);
        }, 10, 3);
    }

    /**
     * Artificial Intelligence... :-)
     *
     * @param array $data
     */
    static function prepare(&$data) {
        foreach ($data as $k => $v) {
            if (is_string($v) && is_email($v)) {
                $data['email'] = sanitize_email($v);
            }
        }
    }

}
