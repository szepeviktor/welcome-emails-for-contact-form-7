<?php

namespace Automation;

defined('ABSPATH') || exit;

class Admin {

    static function init() {

        if (current_user_can('administrator')) {
            add_action('admin_menu', [self::class, 'admin_menu']);
            if (isset($_GET['page'])) {
                $page = \sanitize_key($_GET['page']);
                if (strpos($page, 'automation_') === 0) {
                    add_action('admin_enqueue_scripts', [self::class, 'admin_enqueue_scripts']);
                }
            }
        }
    }

    static function admin_menu() {
        $icon = 'dashicons-editor-expand';
        add_menu_page('Welcome Emails', 'Welcome Emails', 'administrator', 'automation_dashboard', function () {
            include AUTOMATION_DIR . '/admin/dashboard.php';
        }, $icon, 10);

        add_submenu_page('admin.php', __('When...', 'automation'), __('When...', 'automation'), 'administrator', 'automation_types', function () {
            include AUTOMATION_DIR . '/admin/types.php';
        });
        add_submenu_page('admin.php', 'Type', 'Type Edit', 'administrator', 'automation_types_edit', function () {
            include AUTOMATION_DIR . '/admin/types-edit.php';
        });
        add_submenu_page('admin.php', __('Email', 'automation'), __('Email', 'automation'), 'administrator', 'automation_types_edit_email', function () {
            include AUTOMATION_DIR . '/admin/types-edit-email.php';
        });
//        if (AUTOMATION_DEBUG) {
//            add_submenu_page('admin.php', 'Test', 'Test', 'administrator', 'automation_test', function () {
//                include AUTOMATION_DIR . '/admin/test.php';
//            });
//        }
        add_submenu_page('admin.php', __('Settings', 'automation'), __('Settings', 'automation'), 'administrator', 'automation_settings', function () {
            include AUTOMATION_DIR . '/admin/settings.php';
        });
        add_submenu_page('admin.php', __('History', 'automation'), __('History', 'automation'), 'administrator', 'automation_events', function () {
            include AUTOMATION_DIR . '/admin/events.php';
        });
    }

    static function admin_enqueue_scripts() {
        $base_url = plugins_url('', AUTOMATION_DIR . '/automation.php');

        wp_enqueue_script('automation-toastify', $base_url . '/vendor/toastify/toastify.js', [], AUTOMATION_VERSION, false);
        wp_enqueue_style('automation-toastify', $base_url . '/vendor/toastify/toastify.css', [], AUTOMATION_VERSION);
        wp_enqueue_style('automation-admin', $base_url . '/admin/style.css', [], AUTOMATION_VERSION);

        // Composer components
        wp_enqueue_style('automation-composer', $base_url . '/composer/style.css', [], AUTOMATION_VERSION);
        wp_enqueue_style('automation-composer-controls', $base_url . '/composer/controls.css', [], AUTOMATION_VERSION);
        wp_enqueue_style('automation-spectrum', $base_url . '/vendor/spectrum/spectrum.min.css', [], AUTOMATION_VERSION);
        wp_enqueue_script('automation-spectrum', $base_url . '/vendor/spectrum/spectrum.min.js', [], AUTOMATION_VERSION, false);
        wp_enqueue_script('automation-tinymce', $base_url . '/vendor/tinymce/js/tinymce/tinymce.min.js', [], AUTOMATION_VERSION, false);
        wp_enqueue_script('automation-popper', $base_url . '/vendor/popper/popper.min.js', [], AUTOMATION_VERSION, false);
    }
}
