<?php

namespace Automation;

use Automation\Composer\Composer;

defined('ABSPATH') || exit;

class Engine {

    static function init() {

        // With very low priority to reduce the possibilities to get scrambled by other plugins
        add_filter('cron_schedules', function ($schedules) {
            $schedules['automation'] = [
                'interval' => AUTOMATION_ENGINE_INTERVAL,
                'display' => 'Automation: every ' . AUTOMATION_ENGINE_INTERVAL . ' seconds'
            ];
            return $schedules;
        }, 1000);

        add_action('automation_run', [self::class, 'run'], 1);

        // Out of the cron context, if the scheduler is not set, restart it
        // @todo To be moved on activation or version change
        if (!defined('DOING_CRON') || !DOING_CRON) {
            if (!wp_next_scheduled('automation_run')) {
                wp_schedule_event(time() + 30, 'automation', 'automation_run');
            }
        }
    }

    static function next_run() {
        return wp_next_scheduled('automation_run');
    }

    static function run() {
        Logger::debug("Engine started");
        self::process_events();
        self::process_actions();
        Logger::debug("Engine stopped");
    }

    static function process_events() {
        global $wpdb;
        //Logger::info('Processing events');
        $events = EventManager::get_unprocessed();
        //Logger::debug('Events: ' . count($events));

        foreach ($events as $event) {
            EventManager::set_processed($event);
            //Logger::info('Event ' . $event->id . ' - ' . $event->type);
            $flow = EventManager::get_flow($event->type);

            //Logger::debug($flow);

//            if (empty($flow->status)) {
//                Logger::info("Flow not active");
//                continue;
//            }

            if (!empty($flow->email_active) && !empty($flow->email_id)) {
                $action = new Action();
                $action->type = 'email';
                $action->event_id = $event->id;
                $action->options = ['email_id' => $flow->email_id];
                $action->created_at = self::mysql_now();
                // Compute the scheduling time
                $action->scheduled_at = self::mysql_now();
                ActionManager::save($action);
            }

            if (!empty($flow->email2_active) && !empty($flow->email2_id)) {
                $action = new Action();
                $action->type = 'email';
                $action->options = ['email_id' => $flow->email2_id];
                $action->event_id = $event->id;
                $action->created_at = self::mysql_now();
                // Compute the scheduling time
                $factor = 60;
                switch ($flow->email2_delay_um) {
                    case 'h': $factor = 3600;
                        break;
                    case 'd': $factor = 86400;
                        break;
                }
                $action->scheduled_at = self::mysql_now(time() + $factor * $flow->email2_delay);
                ActionManager::save($action);
            }
        }

        //Logger::info('Event processing completed');
    }

    static function process_actions() {
        global $wpdb;
        //Logger::info('Processing actions');

        $actions = ActionManager::get_unprocessed();
        //Logger::debug('Actions found: ' . count($actions));

        foreach ($actions as $action) {
            //Logger::info('Processing action ' . $action->id . ' - ' . $action->type);
            //Logger::info($action->options);
            $event = EventManager::get_event($action->event_id);
            if (!$event) {
                //Logger::error('Linked event ' . $event->id . ' not found');
                ActionManager::set_status($action, 3);
                continue;
            }

            /** @todo Move elsewhere */
            if ($action->type === 'email') {
                $to = sanitize_email($event->data['email']);
                if (!$to) {
                    //Logger::error('Email address missing or invalid on event data');
                    //Logger::error($event);
                    continue;
                }
                $message = Composer::build_message($action->options['email_id'], $event->data);
                if (!$message) {
                    ActionManager::set_status($action, 3); // Action internal error
                    Logger::error('Cannot create a message form email ' . $message->email_id);
                    continue;
                }

//                if (Settings::get_tracking()) {
//                    Tracking::rewrite($message, $event);
//                }

                foreach ($event->data as $k => $v) {
                    $message->html = str_replace('{' . $k . '}', esc_html($v), $message->html);
                }

                /** @todo Add listener for wp_mail() errors */
                $headers = ['Content-Type: text/html;charset=utf8'];
                $email = Settings::get_sender_email();
                if ($email) {
                    $headers[] = 'From: ' . Settings::get_sender_name() . ' <' . $email . '>';
                }
                $r = wp_mail($to, $message->subject, $message->html, $headers);
                if (!$r) {
                    ActionManager::set_status($action, 2); // Action internal error
                    //Logger::error('Cannot send message');
                    //Logger::error(error_get_last());
                    continue;
                }
                //Logger::debug('Action processed');
                ActionManager::set_processed($action);
                continue;
            }

            // Should never reach this point...
            //Logger::error('Unknown action type');
            ActionManager::set_status($action, 3);
        }

        //Logger::info('Action processing completed');
    }

    static function mysql_now($time = false) {
        return date('Y-m-d H:i:s', $time ? $time : time());
    }

}
