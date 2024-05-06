<?php

namespace Automation;

defined('ABSPATH') || exit;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_admin_referer('automation_save');
    $options = wp_kses_post_deep(wp_unslash($_POST['options']));

    $options['sender_email'] = sanitize_email($options['sender_email']);
    $options['sender_name'] = Utils::sanitize_sender_name($options['sender_name']);
    $options['log_level'] = (int) $options['log_level'];

    update_option('automation_settings', $options);
}
$controls = new Admin\Controls(get_option('automation_settings'));
?>

<?php include AUTOMATION_DIR . '/admin/menu.php' ?>

<h2><?php esc_html_e('Settings', 'automation') ?></h2>

<form method="post">
    <?php wp_nonce_field('automation_save') ?>
    <table class="form-table">
        <tr>
            <th><?php esc_html_e('Sender name', 'automation') ?></th>
            <td>
                <?php $controls->text('sender_name', ['placeholder' => __('Default', 'automation')]) ?>
                <p class="description">
                    Even if set can be overridden by other plugins (for example an SMTP plugin)
                </p>
            </td>
        </tr>
        <tr>
            <th><?php esc_html_e('Sender email', 'automation') ?></th>
            <td>
                <?php $controls->text('sender_email', ['placeholder' => __('Default', 'automation')]) ?>
                <p class="description">
                    Even if set can be overridden by other plugins (for example an SMTP plugin).<br>
                    Email addresses with a different domain than your site
                    could not work.
                </p>
            </td>
        </tr>
        <tr>
            <th><?php esc_html_e('Log level', 'automation') ?></th>
            <td>
                <?php $controls->select('log_level', [Logger::ERROR => __('Error', 'automation'), Logger::INFO => __('Info', 'automation'), Logger::DEBUG => __('Debug', 'automation')]) ?>
                <p class="description">
                    <?php esc_html_e('Logs stored in', 'automation') ?>: <code>wp-content/logs/automation</code>.
                </p>
            </td>
        </tr>
    </table>
    <input type="submit" class="button-primary" value="<?php esc_attr_e('Update', 'automation') ?>">
</form>

<h3><?php esc_html_e('Parameters', 'automation') ?></h3>
<table class="widefat">
    <thead>
        <tr>
            <th><?php esc_html_e('Parameter', 'automation') ?></th>
            <th><?php esc_html_e('Value', 'automation') ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?php esc_html_e('Version', 'automation') ?></td>
            <td>
                <?php echo esc_html(AUTOMATION_VERSION) ?>
            </td>
        </tr>
        <tr>
            <td><?php esc_html_e('Next run', 'automation') ?></td>
            <td>
                <?php echo esc_html(Utils::date(Engine::next_run(), true)) ?>
            </td>
        </tr>
        <tr>
            <td><?php esc_html_e('Engine status', 'automation') ?></td>
            <td>
                <?php
                switch (System::get_job_status()) {
                    case System::JOB_LATE: echo '<strong style="color: red">', esc_html__('Late', 'automation'), '</strong>';
                        echo '<br>';
                        echo 'Please check the <a href="', esc_attr(admin_url('site-health.php')), '" target="_blank"><strong>Tools/Site Health</strong></a> panel and<br> if "a scheduled event has failed" is reported<br> ask for help to your provider.<br>';
                        echo 'Read <a href="https://www.wpbeginner.com/wp-tutorials/how-to-disable-wp-cron-in-wordpress-and-set-up-proper-cron-jobs/" target="_blank"><strong>this article</strong></a> for more about this common issue.<br>';
                        echo 'You can install <a href="', esc_attr(admin_url('plugin-install.php')), '?s=wp%20crontrol&tab=search&type=term" target="_blank"><strong>WP Crontrol</strong></a> to monito the WP scheduler';

                        break;
                    case System::JOB_OK: esc_html__('Ok', 'automation');
                        break;
                }
                ?>
            </td>
        </tr>
        <tr>
            <td><?php esc_html_e('Run interval', 'automation') ?></td>
            <td>
                <?php echo esc_html(AUTOMATION_ENGINE_INTERVAL) ?>
            </td>
        </tr>
    </tbody>
</table>