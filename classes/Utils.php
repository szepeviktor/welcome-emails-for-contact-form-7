<?php

namespace Automation;

defined('ABSPATH') || exit;

class Utils {

    /**
     * Sanitize the subject, needs unslashed value.
     *
     * @param string $subject
     * @return string
     */
    static function sanitize_subject($subject) {
        $subject = trim($subject);
        $subject = str_replace(['<', '>'], '', $subject); // To avoid to have &lt;
        $subject = sanitize_text_field($subject);
        return $subject;
    }

    static function sanitize_sender_name($name) {
        $name = trim($name);
        $name = str_replace(['<', '>'], '', $name); // To avoid to have &lt;
        $name = sanitize_text_field($name);
        return $name;
    }

    static function sanitize_text($text) {
        return sanitize_text_field(str_replace(['<', '>'], '', $text));
    }

    static function sanitize_um($um) {
        if (!in_array($um, ['h', 'd'])) {
            return 'd';
        }
        return $um;
    }

    static function date($time = null, $left = false) {
        $now = false;
        if (is_null($time)) {
            $time = time();
        }
        if ($time === false) {
            $buffer = 'none';
        } else {
            $buffer = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $time + get_option('gmt_offset') * 3600);

            if ($now) {
                $buffer .= ' (now: ' . gmdate(get_option('date_format') . ' ' .
                                get_option('time_format'), time() + get_option('gmt_offset') * 3600);
                $buffer .= ')';
            }
            if ($left) {
                if ($time - time() < 0) {
                    $buffer .= ', ' . (time() - $time) . ' seconds late';
                } else {
                    $buffer .= ', ' . gmdate('H:i:s', $time - time()) . ' left';
                }
            }
        }
        return $buffer;
    }

    static function get_option_array($key, array $def = []) {
        $value = get_option($key);
        if (!is_array($value)) {
            return $def;
        }
        return $value;
    }

    static function db_update($table, array $data, array $where) {
        global $wpdb;

        $r = $wpdb->update($table, $data, $where);

        if ($r === false) {
            Logger::fatal($wpdb->last_error);
            return false;
        }

        return $r;
    }

    /**
     *
     * @global \wpdb $wpdb
     * @param type $table
     * @param array $data
     * @return bool
     */
    static function db_insert($table, array $data) {
        global $wpdb;

        $r = $wpdb->insert($table, $data);

        if ($r === false) {
            Logger::fatal($wpdb->last_error);
            return false;
        }

        return $wpdb->insert_id;
    }

    static function db_get_results($query) {
        global $wpdb;
        $r = $wpdb->get_results($query);

        if ($r === false) {
            Logger::fatal($wpdb->last_error);
            return false;
        }
        return $r;
    }

    static function db_get_row($query) {
        global $wpdb;

        $r = $wpdb->get_row($query);
        if ($r === false) {
            Logger::fatal($wpdb->last_error);
            return false;
        }
        return $r;
    }

    static function db_get_var($query) {
        global $wpdb;

        $r = $wpdb->get_var($query);
        if ($r == null) {
            Logger::fatal($wpdb->last_error);
            return false;
        }
        return $r;
    }
}
