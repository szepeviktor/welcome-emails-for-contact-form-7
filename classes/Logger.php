<?php

namespace Automation;

defined('ABSPATH') || exit;

class Logger {

    const FATAL = 1;
    const ERROR = 2;
    const INFO = 3;
    const DEBUG = 4;

    static $file = '';
    static $key;
    static $level;

    static function debug($text, $group = 'main') {
        self::log($text, self::DEBUG, $group);
    }

    static function info($text, $group = 'main') {
        self::log($text, self::INFO, $group);
    }

    static function error($text, $group = 'main') {
        self::log($text, self::ERROR, $group);
    }

    static function fatal($text, $group = 'main') {
        self::log($text, self::FATAL, $group);
    }

    static function log($text, $level = self::INFO, $group = 'main') {
        global $current_user;

        if (empty(self::$level)) {
            self::$level = Settings::get_log_level();
        }

        if ($level > self::$level) {
            return;
        }

        if (!self::$key) {
            self::$key = get_option('automation_logger_key');
            if (empty(self::$key)) {
                self::$key = sanitize_key(strtolower(wp_generate_password(8, false, false)));
                update_option('automation_logger_key', self::$key, false);
            }
        }

        if (!self::$file) {
            $group = sanitize_key($group);
            self::$file = WP_CONTENT_DIR . '/logs/automation/' . $group . '-' . date('Y-m') . '-' . self::$key . '.txt';
        }

        if (defined('DOING_CRON') && DOING_CRON) {
            $user = '[cron]';
        } else if ($current_user) {
            $user = $current_user->user_login;
        } else {
            $user = '[no user]';
        }

        $time = date('d-m-Y H:i:s ');
        switch ($level) {
            case self::FATAL: $time .= '- FATAL';
                break;
            case self::ERROR: $time .= '- ERROR';
                break;
            case self::INFO: $time .= '- INFO ';
                break;
            case self::DEBUG: $time .= '- DEBUG';
                break;
        }
        if (is_wp_error($text)) {
            /* @var $text WP_Error */
            $text = $text->get_error_message() . ' (' . $text->get_error_code() . ') - ' . print_r($text->get_error_data(), true);
        } else {
            if (is_array($text) || is_object($text)) {
                $text = print_r($text, true);
            }
        }

        $memory_limit = size_format(wp_convert_hr_to_bytes(ini_get('memory_limit')));

        $res = @file_put_contents(self::$file, $time . ' - ' . AUTOMATION_VERSION . ' - ' . size_format(memory_get_usage(), 1) . '/' . $memory_limit . ' - ' . $user . ' > ' . $text . "\n", FILE_APPEND);
    }

}
