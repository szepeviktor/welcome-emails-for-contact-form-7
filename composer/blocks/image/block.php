<?php

namespace Automation\Composer;

defined('ABSPATH') || exit;
?>
<?php if ($options['form_type'] === 'wp') { ?>

    <?php
    $src = plugins_url('composer', AUTOMATION_DIR . '/automation.php') . '/blocks/image/placeholder.png';
    $height = 400;
    $width = 600;
    if ($options['image']) {
        $media = wp_get_attachment_image_src($options['image'], 'large');
        if ($media) {
            $src = $media[0];

            if ($media[1] > 600) {
                $height = (int) (600 / $media[1] * $media[2]);
                $width = 600;
            } else {
                $height = $media[2];
                $width = $media[1];
            }
        }
    }
    ?>
    <center>
        <a href="<?php echo esc_attr($options['url']) ?>" style="display: block; font-size: 14px; text-decoration: none"><img style="display: block; max-width: 100%; width: auto; height: auto" width="<?php echo esc_attr($width); ?>" height="<?php echo esc_attr($height); ?>" src="<?php echo esc_attr($src); ?>" alt="<?php echo esc_attr($options['alt']); ?>"></a>
    </center>
<?php } else if ($options['form_type'] === 'url') { ?>
    <?php
    $src = plugins_url('composer', __FILE__) . '/blocks/image/placeholder.png';
    if (!empty($options['external_url'])) {
        $src = $options['external_url'];
    }
    ?>
    <center>
        <a href="<?php echo esc_attr($options['url']); ?>" style="display: block; font-size: 14px; text-decoration: none"><img style="display: block; max-width: 100%; width: auto; height: auto" src="<?php echo esc_attr($src); ?>" alt="<?php echo esc_attr($options['alt']); ?>"></a>
    </center>

<?php } else { ?>
    <?php
    $custom_logo_id = get_theme_mod('custom_logo');
    if (!$options['width']) {
        $options['width'] = 600;
    }
    $width = min(600, (int) $options['width']);

    if ($custom_logo_id) {
        $media = wp_get_attachment_image_src($custom_logo_id, 'thumbnail');
        if ($media[1] > $width) {
            $media[2] = (int) ($width / $media[1] * $media[2]);
            $media[1] = $width;
        }
    }
    ?>
    <center>
        <a href="<?php echo esc_attr($options['url']); ?>" style="display: block; font-size: 14px; text-decoration: none"><img style="display: block; max-width: 100%; width: <?php echo esc_attr($media[1]); ?>px; height: auto" width="<?php echo esc_attr($media[1]); ?>" height="<?php echo esc_attr($media[2]); ?>" src="<?php echo esc_attr($media[0]); ?>" alt="<?php echo esc_attr($options['alt']); ?>"></a>
    </center>
<?php } ?>

