<?php

namespace Automation;

defined('ABSPATH') || exit;

$events = EventManager::get_events();
?>

<?php include AUTOMATION_DIR . '/admin/menu.php' ?>

<table class="widefat fixed">
    <thead>
        <tr>
            <th><?php esc_html_e('ID', 'automation') ?></th>
            <th><?php esc_html_e('Event type', 'automation') ?></th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th style="text-align: center"><?php esc_html_e('Actions', 'automation') ?></th>
        </tr>
    </thead>
    <tbody>

        <?php foreach ($events as $event) { ?>
            <?php
            $actions = ActionManager::get_by_event($event->id);
            $type = EventManager::get_type($event->type);

            // No more registered type
            if (!$type) {
                $type = new EventType($event->type, $event->type);
            }
            ?>
            <tr>
                <td><?php echo esc_html($event->id) ?></td>
                <td><?php echo esc_html($type->name) ?></td>
                <td><span class="atm-status <?php echo $event->status?'atm-status--processed':'atm-status--unprocessed'?>"><?php echo $event->status?'Processed':'Unprocessed'?></span></td>
                <td><?php echo esc_html($event->created_at) ?></td>
                <td>
                    <?php foreach ($actions as $action) { ?>

                        <?php if ($action->status == 0) { ?>
                        <span class="dashicons dashicons-clock"></span>
                        <?php }else if ($action->status == 1) { ?>
                        <span class="dashicons dashicons-saved"></span>
                        <?php } else if ($action->status == 2) { ?>
                        <span class="dashicons dashicons-no-alt"></span>
                        <?php } else { ?>
                        <span class="dashicons dashicons-thumbs-down" title="Error <?php echo esc_attr($action->status)?>"></span>
                        <?php } ?>
                        <?php esc_html_e('Send email', 'automation') ?> (<?php echo esc_html($action->scheduled_at) ?>)
                        <br>
                    <?php } ?>

                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<p>
    <span class="dashicons dashicons-clock"></span> - <?php esc_html_e('Queued', 'automation') ?><br>
    <span class="dashicons dashicons-saved"></span> - <?php esc_html_e('Sent', 'automation') ?><br>
    <span class="dashicons dashicons-no-alt"></span> - <?php esc_html_e('Failed', 'automation') ?> (the mailing function of WP reported a failure. If you have an SMTP plugin, test the email sending from it. If you set
    specific sender name or email, try to unset them)<br>
    <span class="dashicons dashicons-thumbs-down"></span> - <?php esc_html_e('Unknown error', 'automation') ?> (probably logged, see the main settings)
</p>


