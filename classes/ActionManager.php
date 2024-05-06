<?php

namespace Automation;

defined('ABSPATH') || exit;

class ActionManager {

    static function init() {

    }

    static function get_unprocessed() {
        global $wpdb;
        $actions = Utils::db_get_results("select * from {$wpdb->prefix}automation_actions where status=0 and scheduled_at<=now()");
        foreach ($actions as $action) {
            $action->options = json_decode($action->options, true);
        }
        return $actions;
    }

    static function get_by_event($event_id) {
        global $wpdb;
        $actions = Utils::db_get_results($wpdb->prepare("select * from {$wpdb->prefix}automation_actions where event_id=%d", $event_id));

        foreach ($actions as $action) {
            $action->options = json_decode($action->options, true);
        }
        return $actions;
    }

    static function set_processed($action) {
        self::set_status($action, 1);
    }

    static function set_status($action, $status) {
        global $wpdb;
        Utils::db_update($wpdb->prefix . 'automation_actions', ['status' => (int) $status, 'processed_at' => self::mysql_now()], ['id' => (int)$action->id]);
    }

    /**
     * @todo Introduce caching
     * @return ActionType[]
     */
    static function get_types() {
        $list = [
            new ActionType('email', 'Send email')
        ];
        return $list;
        //return apply_filters('automation_actions', $list);
    }

    /**
     *
     * @param string $id
     * @return ActionType
     */
    static function get_type($type) {
        foreach (self::get_types() as $action_type) {
            if ($action_type->type === $type)
                return $action_type;
        }
        return null;
    }

    /**
     *
     * @param Action $action
     */
    static function run($action) {
        // Get the action type
        //$action_type = self::get_type($action->type);
        // Call the callback
        //call_user_func_array($action_type->callback, [$action]);
        //$action_type->run($action);
    }

    /**
     *
     * @global \wpdb $wpdb
     * @param Action $action
     */
    static function save($action) {
        global $wpdb;
        $data = (array) $action;
        $data['options'] = wp_json_encode($data['options']);
        unset($data['id']);
        if (!empty($action->id)) {
            Utils::db_update($wpdb->prefix . 'automation_actions', $data, ['id' => (int)$action->id]);
        } else {
            $action->id = Utils::db_insert($wpdb->prefix . 'automation_actions', $data);
        }
    }

    static function mysql_now() {
        return date('Y-m-d H:i:s');
    }

}
