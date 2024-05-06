<?php

namespace Automation;

defined('ABSPATH') || exit;

use Automation\Admin\Controls;

$event_type_id = \sanitize_key($_GET['id']);
$event_type = EventManager::get_type($event_type_id);
unset($event_type_id);

if (!$event_type) {
    echo 'Event type not found';
    return;
}

$flow = EventManager::get_type_config($event_type->id);

$controls = new Controls(['delay' => $flow->email2_delay, 'delay_um' => $flow->email2_delay_um]);
?>
<script>
    jQuery(function ($) {
        $('.status-checkbox').change(function (event) {
            var data = [];
            data.push({name: 'action', value: 'automation_email_status'});
            data.push({name: 'status', value: this.checked ? '1' : '0'});
            data.push({name: 'id', value: '<?php echo esc_js($event_type->id); ?>'});
            data.push({name: 'email', value: this.dataset.email});
            data.push({name: '_ajax_nonce', value: Automation.nonce});

            jQuery.post(ajaxurl, data, function (response) {
                Automation.toast();
            }).fail(function () {
                alert('An error occurred');
            });

        });

        $('#atm-delay').change(function (event) {
            var data = $(this).serializeArray();
            data.push({name: 'action', value: 'automation_email_delay'});
            data.push({name: 'id', value: '<?php echo esc_js($event_type->id); ?>'});
            data.push({name: '_ajax_nonce', value: Automation.nonce});

            jQuery.post(ajaxurl, data, function (response) {
                Automation.toast();
            }).fail(function () {
                alert('An error occurred');
            });

        });
        
        /*
        $('.flow-status-checkbox').change(function (event) {
            var data = [];
            data.push({name: 'action', value: 'automation_type_status'});
            data.push({name: 'status', value: this.checked ? '1' : '0'});
            data.push({name: 'id', value: this.dataset.id});
            data.push({name: '_ajax_nonce', value: Automation.nonce});

            jQuery.post(ajaxurl, data, function (response) {
                Automation.toast();
            }).fail(function () {
                alert(Automation.error_message);
            });

        });
        */

    });
</script>
<?php include __DIR__ . '/menu.php' ?>
<h2><?php echo esc_html($event_type->name) ?></h2>

<!--
<div class="atm-checkbox-wrapper">
    <input class="atm-checkbox flow-status-checkbox" type="checkbox" data-id="<?php echo esc_attr($event_type->id); ?>" <?php echo empty($flow->status) ? '' : 'checked' ?>>
</div>
-->

<table class="widefat fixed">
    <thead>
        <tr>
            <th><?php esc_html_e('Action', 'automation') ?></th>
            <th><?php esc_html_e('Active', 'automation') ?></th>
            <th><?php esc_html_e('Delay', 'automation') ?>*</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><a href="?page=automation_types_edit_email&id=<?php echo esc_attr(urlencode($event_type->id)); ?>&step=1">Email 1</a></td>
            <td>
                <div class="atm-checkbox-wrapper">
                    <input class="atm-checkbox status-checkbox" type="checkbox" data-email="1" <?php echo empty($flow->email1_active) ? '' : 'checked' ?>>
                </div>
            </td>
            <td><?php esc_html_e('Immediately', 'automation') ?></td>
        </tr>
        <tr>
            <td><a href="?page=automation_types_edit_email&id=<?php echo esc_attr(urlencode($event_type->id)); ?>&step=2">Email 2</a></td>
            <td>
                <div class="atm-checkbox-wrapper">
                    <input class="atm-checkbox status-checkbox" type="checkbox" data-email="2" <?php echo empty($flow->email2_active) ? '' : 'checked' ?>>
                </div>
            </td>
            <td>
                <form id="atm-delay">
                    <?php $controls->text('delay', ['class' => 'delay-select', 'width' => '50']) ?>
                    <?php $controls->select('delay_um', ['h' => __('Hours', 'automation'), 'd' => __('Days', 'automation')], ['class' => 'delay-select']) ?>
                </form>
            </td>
        </tr>
    </tbody>
</table>

<p>
    Delayed delivering is implemented using the WP native scheduler/cron. If it is does not work correctly the delay could not be respected.<br>
    Check your <a href="<?php echo esc_attr(admin_url('site-health.php')) ?>" target="_blank">WP Site Health</a> to see if there are scheduler related warnings.<br>
    Install the plugin <a href="<?php echo esc_attr(admin_url('plugin-install.php')) ?>?s=wp%20crontrol&tab=search&type=term" target="_blank">WP Crontrol</a>
    to check if the scheduler is working and there are not delayed jobs.<br>
    This <a href="https://www.wpbeginner.com/wp-tutorials/how-to-disable-wp-cron-in-wordpress-and-set-up-proper-cron-jobs/" target="_blank">article by
        WPBeginner</a> contains lot of ideas to improve the WP scheduler.

</p>