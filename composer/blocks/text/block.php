<?php

namespace Automation\Composer;

defined('ABSPATH') || exit;

$text = trim($options['text']);

if ($options['form_type'] === 'plain') {
    $text = wpautop($text);
    $text = str_replace('<p>', '<p style="margin: 0; line-height: inherit; font-family: inherit; font-size: inherit; font-weight: inherit; color: inherit">', $text);
}
$font = Font::build($options, [], 'font_');
?>
<?php if ($options['form_type'] === 'plain') { ?>
    <style>
        .text {
            <?php $font->echo_css() ?>;
            text-align: <?php echo esc_html($options['align']) ?>;
            line-height: 1.5;
        }
    </style>
<?php } else { ?>
    <style>
        .text {
            font-size: 16px;
            line-height: 1.5;
        }
    </style>
<?php } ?>
<div inline-class="text"><?php echo wp_kses_post($text) ?></div>