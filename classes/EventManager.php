<?php

namespace Automation;

defined('ABSPATH') || exit;

class EventManager {

    static $types = [];

    static function init() {
        if (current_user_can('administrator')) {
            add_action('wp_ajax_automation_type_status', [self::class, 'ajax_type_status']);
            add_action('wp_ajax_automation_email_status', [self::class, 'ajax_email_status']);
            add_action('wp_ajax_automation_email_delay', [self::class, 'ajax_email_delay']);
        }
    }

    static function get_event($id) {
        global $wpdb;
        $event = Utils::db_get_row($wpdb->prepare("select * from {$wpdb->prefix}automation_events where id=%d limit 1", (int) $id));
        if ($event) {
            $event->data = json_decode($event->data, true);
        }
        return $event;
    }

    static function get_unprocessed() {
        global $wpdb;
        $events = Utils::db_get_results("select * from {$wpdb->prefix}automation_events where status=0");
        foreach ($events as $event) {
            $event->data = json_decode($event->data, true);
        }
        return $events;
    }

    static function get_events() {
        global $wpdb;
        $events = Utils::db_get_results("select * from {$wpdb->prefix}automation_events order by id desc");
        foreach ($events as $event) {
            $event->data = json_decode($event->data, true);
        }
        return $events;
    }

    static function set_processed($event) {
        global $wpdb;
        Utils::db_update($wpdb->prefix . 'automation_events', ['status' => 1, 'processed_at' => self::mysql_now()], ['id' => (int) $event->id]);
    }

    /**
     * @return EventType[]
     */
    static function get_types() {
        return self::$types;
    }

    /**
     *
     * @param string $id
     * @return EventType
     */
    static function get_type($id) {
        return self::$types[$id] ?? null;
    }

    static function get_event_count($type_id) {
        global $wpdb;
        return $wpdb->get_var($wpdb->prepare("select count(*) from {$wpdb->prefix}automation_events where type=%s", $type_id));
    }

    static function get_last_event_date($type_id) {
        global $wpdb;
        return $wpdb->get_var($wpdb->prepare("select created_at from {$wpdb->prefix}automation_events where type=%s order by id desc limit 1", $type_id));
    }

    /**
     *
     * @global \wpdb $wpdb
     * @param Event $event
     */
    static function save($event) {
        global $wpdb;
        $data = [];
        $data['data'] = \wp_json_encode($event->data);
        if (!empty($event->id)) {
            Utils::db_update($wpdb->prefix . 'automation_events', $data, ['id' => (int) $event->id]);
        } else {
            $data['type'] = $event->type;
            $data['created_at'] = self::mysql_now();
            $event->id = Utils::db_insert($wpdb->prefix . 'automation_events', $data);
        }
    }

    static function get_type_config($event_type_id) {
        return self::get_flow($event_type_id);
    }

    /**
     *
     * @param string $event_type_id
     * @return \Automation\Flow
     */
    static function get_flow($event_type_id) {
        $event_type = EventManager::get_type($event_type_id);
        if (empty($event_type)) {
            return false;
        }

        $flow = (object) get_option('automation_flow_' . $event_type->id);
        if (empty($flow)) {
            $flow = new Flow();
            $flow->email2_delay_um = 'd';
            $flow->email2_delay = 7;
            self::save_flow($event_type->id, $flow);
        }

        // Patch
        if (isset($flow->email_active)) {
            $flow->email1_active = $flow->email_active;
        }

        if (isset($flow->email_id)) {
            $flow->email1_id = $flow->email_id;
        }
        $flow->email1_delay = 0;
        $flow->email1_delay_um = 'd';
        // Patch end

        if (empty($flow->email2_delay_um)) {
            $flow->email2_delay_um = 'd';
            self::save_flow($event_type->id, $flow);
        }

        if (empty($flow->email2_delay)) {
            $flow->email2_delay = 7;
            self::save_flow($event_type->id, $flow);
        }

        return $flow;
    }

    /**
     *
     * @param Flow $flow
     */
    static function save_flow($event_type_id, $flow) {
        $r = update_option('automation_flow_' . $event_type_id, (array) $flow, false);
    }

    static function save_type_config($event_type_id, $config) {
        $r = update_option('automation_flow_' . sanitize_key($event_type_id), (array) $config, false);
    }

    static function ajax_type_status() {
        check_ajax_referer('automation');
        $id = \sanitize_key($_POST['id']);
        $config = self::get_type_config($id);
        if (!$config) {
            Logger::log("Type config not found", Logger::ERROR);
            die('Config not found');
        }
        $config->status = (int) $_POST['status'];
        self::save_type_config($id, $config);
        die();
    }

    static function ajax_email_status() {
        check_ajax_referer('automation');
        $id = \sanitize_key($_POST['id']);
        $email = (int) $_POST['email'];

        $flow = self::get_flow($id);
        if (!$flow) {
            Logger::error("Flow not found");
            die('Config not found');
        }
        switch ($email) {
            case 1:
                $flow->email_active = (int) $_POST['status'];
                break;
            case 2:
                $flow->email2_active = (int) $_POST['status'];
                break;
            default:
                die('Invalid email');
        }
        $flow->email2_active = (int) $_POST['status'];
        self::save_type_config($id, $flow);
        die();
    }

    static function ajax_email_delay() {
        check_ajax_referer('automation');

        $posted = wp_kses_post_deep(wp_unslash($_POST));

        $id = $posted['id'];
        $flow = self::get_flow($id);
        if (!$flow) {
            Logger::error("Flow not found");
            die('Flow not found');
        }

        $flow->email2_delay = (int) $posted['options']['delay'] ?? 1;
        $flow->email2_delay_um = Utils::sanitize_um($posted['options']['delay_um'] ?? 'd');
        self::save_flow($flow);
        die();
    }

    static function mysql_now() {
        return date('Y-m-d H:i:s');
    }
}
