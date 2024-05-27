<?php

namespace Automation;

defined('ABSPATH') || exit;

use Automation\Composer\Composer;

$controls = new \Automation\Admin\Controls([]);

$event_type_id = \sanitize_key($_GET['id']);
$event_type = EventManager::get_type($event_type_id);
unset($event_type_id);

if (!$event_type) {
    echo 'Event type not found';
    return;
}

$flow = EventManager::get_type_config($event_type->id);

if ($_GET['step'] === '1') {

    if (empty($flow->email_id) || !Composer::get_email($flow->email_id)) {
        $email = new Email();
        $email->subject = __('Your request has been received', 'automation');
        $email->html = '<div class="cmp-block-type ui-draggable ui-draggable-handle" data-type="text" style="position: relative; height: auto;" data-options="eyJibG9ja19iYWNrZ3JvdW5kIjoiI2ZmZmZmZiIsInRleHQiOiJUaGFuayB5b3UgZm9yIGNvbnRhY3RpbmcgdXMuIE91ciBzdGFmZiB3aWxsIHRha2UgY2FyZSBvZiB5b3VyIHJlcXVlc3QgaW4gMjQgaG91cnMuIiwiZm9ybV90eXBlIjoicGxhaW4iLCJibG9ja19wYWRkaW5nX3RvcCI6IjE2IiwiYmxvY2tfcGFkZGluZ19ib3R0b20iOiIxNiIsImJsb2NrX3BhZGRpbmdfbGVmdCI6IjE2IiwiYmxvY2tfcGFkZGluZ19yaWdodCI6IjE2IiwiYWxpZ24iOiJsZWZ0IiwiZm9udF9mYW1pbHkiOiIiLCJmb250X3NpemUiOiIyMCIsImZvbnRfd2VpZ2h0Ijoibm9ybWFsIiwiZm9udF9jb2xvciI6IiIsImJsb2NrX3R5cGUiOiJ0ZXh0In0="><table border="0" width="100%" style="max-width: 600px" cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff"><tbody><tr><td style="line-height: normal !important; letter-spacing: normal; padding: 16px 16px 16px 16px;">
<div style="font-size: 20px;font-family: Verdana, Geneva, sans-serif;font-weight: normal;color: #444444;line-height: normal;; text-align: left; line-height: 1.5;"><p style="margin: 0; line-height: inherit; font-family: inherit; font-size: inherit; font-weight: inherit; color: inherit">Thank you for contacting us. Our staff will take care of your request in 24 hours.</p>
</div></td></tr></tbody></table></div>';
        Composer::save($email);
        $flow->email_id = $email->id;
        EventManager::save_flow($event_type->id, $flow);
    }
    $email_id = $flow->email_id;
} else {
    if (empty($flow->email2_id) || !Composer::get_email($flow->email2_id)) {
        $email = new Email();
        $email->subject = __('Your request has been received', 'automation');
        $email->html = '<div class="cmp-block-type ui-draggable ui-draggable-handle" data-type="text" style="position: relative; height: auto;" data-options="eyJibG9ja19iYWNrZ3JvdW5kIjoiI2ZmZmZmZiIsInRleHQiOiJUaGFuayB5b3UgZm9yIGNvbnRhY3RpbmcgdXMuIE91ciBzdGFmZiB3aWxsIHRha2UgY2FyZSBvZiB5b3VyIHJlcXVlc3QgaW4gMjQgaG91cnMuIiwiZm9ybV90eXBlIjoicGxhaW4iLCJibG9ja19wYWRkaW5nX3RvcCI6IjE2IiwiYmxvY2tfcGFkZGluZ19ib3R0b20iOiIxNiIsImJsb2NrX3BhZGRpbmdfbGVmdCI6IjE2IiwiYmxvY2tfcGFkZGluZ19yaWdodCI6IjE2IiwiYWxpZ24iOiJsZWZ0IiwiZm9udF9mYW1pbHkiOiIiLCJmb250X3NpemUiOiIyMCIsImZvbnRfd2VpZ2h0Ijoibm9ybWFsIiwiZm9udF9jb2xvciI6IiIsImJsb2NrX3R5cGUiOiJ0ZXh0In0="><table border="0" width="100%" style="max-width: 600px" cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff"><tbody><tr><td style="line-height: normal !important; letter-spacing: normal; padding: 16px 16px 16px 16px;">
<div style="font-size: 20px;font-family: Verdana, Geneva, sans-serif;font-weight: normal;color: #444444;line-height: normal;; text-align: left; line-height: 1.5;"><p style="margin: 0; line-height: inherit; font-family: inherit; font-size: inherit; font-weight: inherit; color: inherit">Thank you for contacting us. Our staff will take care of your request in 24 hours.</p>
</div></td></tr></tbody></table></div>';
        Composer::save($email);
        $flow->email2_id = $email->id;
        EventManager::save_flow($event_type->id, $flow);
    }
    $email_id = $flow->email2_id;
}

$cmp_placeholders = [];
foreach ($type->fields as $key => $label) {
    $cmp_placeholders[] = '<span><code>{' . esc_html($key) . '}</code> -> ' . esc_html($label) . '</span>';
}
?>

<?php include __DIR__ . '/menu.php' ?>

<script>
    jQuery(function () {
        Composer.init(<?php echo (int) $email_id; ?>);
        jQuery('#atm-save-button').click(function () {
            Composer.save(function () {
                Automation.toast();
            });
        });
    });
</script>

<h2><?php echo esc_html($event_type->name) ?></h2>
<p>
    <a href="?page=automation_types_edit&id=<?php echo esc_attr(urlencode($event_type->id))?>" class="button-secondary">&laquo;</a>
    <input type="button" class="button-primary" id="atm-save-button" value="Save">
</p>

<?php include AUTOMATION_DIR . '/composer/index.php'; ?>
<br>



