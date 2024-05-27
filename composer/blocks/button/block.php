<?php

namespace Automation\Composer;

defined('ABSPATH') || exit;

$url = $options['button_url'];
$label = $options['button_label'];
$color = sanitize_hex_color($options['button_color']);
$label_color = sanitize_hex_color($options['button_label_color']);
?>

<div style="text-align: center">
    <a rel="noopener" target="_blank" href="<?php echo esc_attr($url); ?>" style="background-color: <?php echo esc_attr($color); ?>; font-size: 18px; font-family: Helvetica, Arial, sans-serif; font-weight: bold; text-decoration: none; padding: 14px 20px; color: <?php echo esc_attr($label_color); ?>; border-radius: 5px; display: inline-block; mso-padding-alt: 0;">
        <!--[if mso]>
        <i style="letter-spacing: 25px; mso-font-width: -100%; mso-text-raise: 30pt;">&nbsp;</i>
        <![endif]-->
        <span style="mso-text-raise: 15pt;"><?php echo esc_html($label) ?></span>
        <!--[if mso]>
        <i style="letter-spacing: 25px; mso-font-width: -100%;">&nbsp;</i>
        <![endif]-->
    </a>
</div>