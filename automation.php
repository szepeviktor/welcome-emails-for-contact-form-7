<?php

/*
  Plugin Name: Welcome Email for CF7
  Plugin URI: https://www.satollo.net/plugins/automation
  Description: Welcome email series for Contact Form 7
  Version: 1.0.1
  Requires at least: 5.1
  Requires PHP: 7.4
  Author: Stefano Lissa
  Disclaimer: Use at your own risk. No warranty expressed or implied is provided.
  License: GPLv2 or later
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
  Text Domain: automation
 */

namespace Automation;

use Automation\Composer\Composer as Composer;

defined('ABSPATH') || exit;

define('AUTOMATION_DIR', __DIR__);
define('AUTOMATION_VERSION', '1.0.1');

if (!defined('AUTOMATION_ENGINE_INTERVAL')) {
    define('AUTOMATION_ENGINE_INTERVAL', 60 * 15);
}

if (!defined('AUTOMATION_DEBUG')) {
    define('AUTOMATION_DEBUG', false);
}

spl_autoload_register(function ($class) {

    if (strncmp('Automation\\', $class, 11) !== 0) {
        return;
    }

    $relative_class = str_replace('..', '.', substr($class, 11)); // Could happen?
    $file = __DIR__ . '/classes/' . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

require_once __DIR__ . '/includes/api.php';

class Automation {

    static function init() {
        add_action('init', [self::class, 'wp_init']);
        add_action('wp_loaded', [self::class, 'wp_loaded']);
    }

    static function wp_init() {
        $version = get_option('automation_version', '0.0.0');
        if (is_admin() && AUTOMATION_VERSION !== $version) {
            Upgrade::run();
        }

        \AutomationApi::init();

        // Does it make sense to initialize them? Or it's better to create a "getAAAManager()"?
        EventManager::init();
        ActionManager::init();
        Composer::init();
        Engine::init();
        if (is_admin()) {
            Admin::init();
        }
    }

    static function wp_loaded() {
        Integrations::init();
    }
}

Automation::init();

