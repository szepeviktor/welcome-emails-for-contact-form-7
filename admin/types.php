<?php

namespace Automation;

defined('ABSPATH') || exit;

$types = EventManager::get_types();
?>
<script>
    jQuery(function ($) {
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
    });
</script>

<?php include __DIR__ . '/menu.php' ?>

<?php if (empty($types)) { ?>

    <p>
        No events have been found. Please install Contact Form 7.
    </p>

<?php } else { ?>

    <table class="widefat fixed">
        <thead>
            <tr>
                <th><?php esc_html_e('Event', 'automation') ?></th>
                <th style="text-align: center"><?php esc_html_e('Count', 'automation'); ?></th>
                <th style="text-align: center"><?php esc_html_e('Last', 'automation'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($types as $type) { ?>
                <?php $flow = EventManager::get_type_config($type->id) ?>
                <tr>
                    <td><a href="?page=automation_types_edit&id=<?php echo esc_attr(urlencode($type->id)); ?>"><?php echo esc_html($type->name); ?></a></td>
                    <td style="text-align: center"><?php echo esc_html(EventManager::get_event_count($type->id)); ?></td>
                    <td style="text-align: center"><?php echo esc_html(EventManager::get_last_event_date($type->id)); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

<?php } ?>




