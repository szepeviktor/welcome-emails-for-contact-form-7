<?php

use Automation\EventManager as EventManager;
use Automation\EventType as EventType;
use Automation\Engine;
use Automation\Utils;

defined('ABSPATH') || exit;

class AutomationApi {

    static function init() {
        add_action('automation_fire_event', function ($type, $data) {
            self::fire_event($type, $data);
        });

//        add_action('automation_register_event_type', function ($id, $name) {
//            self::register_event_type($id, $name);
//        });
    }

    static function register_event_type($id, $name, $fields = []) {
        $id = sanitize_key($id);
        if (empty($id)) {
            return;
        }
        $name = Utils::sanitize_text($name);
        if (empty($name)) {
            return;
        }
        if (!is_array($fields)) {
            return;
        }
        EventManager::$types[$id] = new EventType($id, $name, $fields);
    }

    static function fire_event($type, $data) {
        /** @todo Check if the event type exists? */
        $event = new \Automation\Event();
        $event->type = sanitize_key($type);
        $event->data = $data;
        EventManager::save($event);
        //Engine::process_events();
        Engine::run();
        return $event->id;
    }

}
