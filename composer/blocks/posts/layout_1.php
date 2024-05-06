<?php

namespace Automation\Composer;

defined('ABSPATH') || exit;

$font = Font::build($options, [], 'title_font_');
$align_left = is_rtl() ? 'right' : 'left';
$title_padding_left = $show_image ? 20 : 0;
?>
<style>
    .title {
        <?php $font->echo_css() ?>;
        text-decoration: none;
    }
    .title-td {
        padding-bottom: 20px;
        padding-left: <?php echo (int) $title_padding_left ?>px;
    }
    .image {
        max-width: 100%;
    }
    .image-td {
        padding-bottom: 20px;
    }
    .meta {
        font-style: italic;
        font-size: 14px;
        text-decoration: none;
    }
</style>
<table role="presentation">
    <?php foreach ($posts as $p) { ?>
        <tr>
            <?php if ($show_image) { ?>
                <td inline-class="image-td" valign="top">
                    <a href="<?php echo esc_attr($p->url) ?>">
                        <img src="<?php echo esc_attr($p->image) ?>" inline-class="image" width="100" border="0">
                    </a>
                </td>
            <?php } ?>
            <td inline-class="title-td" valign="top" align="<?php echo esc_attr($align_left) ?>">
                <a href="<?php echo esc_attr($p->url) ?>" inline-class="title"><?php echo esc_html($p->title) ?></a>
                <br><br>
                <span inline-class="meta"><?php echo esc_html(implode(' - ', $p->meta)) ?></span>
            </td>
        </tr>
    <?php } ?>
</table>
