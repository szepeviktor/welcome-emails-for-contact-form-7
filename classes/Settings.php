<?php

namespace Automation;

defined('ABSPATH') || exit;

class Settings {

    static $options;

    static function init() {
        if (!self::$options) {
            self::$options = Utils::get_option_array('automation_settings');
        }
    }

    static function get_sender_email() {
        self::init();
        return self::$options['sender_email'] ?? '';
    }

    static function get_sender_name() {
        self::init();
        return self::$options['sender_name'] ?? '';
    }

    static function get_tracking() {
        self::init();
        return !empty(self::$options['tracking']);
    }

    static function get_log_level() {
        self::init();
        if (AUTOMATION_DEBUG) {
            return Logger::DEBUG;
        }
        return (int)empty(self::$options['log_level']) ? Logger::ERROR : self::$options['log_level'];
    }

}
