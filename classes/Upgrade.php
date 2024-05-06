<?php

namespace Automation;

defined('ABSPATH') || exit;

class Upgrade {

    static function run() {
        global $wpdb, $charset_collate;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql = "CREATE TABLE `" . $wpdb->prefix . "automation_events` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `type` varchar(100) NOT NULL DEFAULT '',
            `status` int(11) NOT NULL DEFAULT 0,
            `created_at` datetime,
            `processed_at` datetime,
            `data` longtext,
            PRIMARY KEY (`id`)
            ) $charset_collate;";

        self::db_delta($sql);

        $sql = "CREATE TABLE `" . $wpdb->prefix . "automation_actions` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `event_id` int(11) NOT NULL DEFAULT 0,
            `type` varchar(100) NOT NULL DEFAULT '',
            `status` int(11) NOT NULL DEFAULT 0,
            `created_at` datetime,
            `scheduled_at` datetime,
            `processed_at` datetime,
            `options` longtext,
            PRIMARY KEY (`id`)
            ) $charset_collate;";

        self::db_delta($sql);

        $sql = "CREATE TABLE `" . $wpdb->prefix . "automation_emails` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `created_at` datetime,
            `updated_at` datetime,
            `refreshed_at` datetime,
            `subject` varchar(250),
            `html` longtext,
            `text` longtext,
            PRIMARY KEY (`id`)
            ) $charset_collate;";

        self::db_delta($sql);

        wp_mkdir_p(WP_CONTENT_DIR . '/logs/automation');

        update_option('automation_version', AUTOMATION_VERSION, false);
    }

    static function db_delta($sql) {
        $res = dbDelta($sql);
    }
}
