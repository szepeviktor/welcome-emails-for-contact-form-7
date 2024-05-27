<?php
namespace Automation\Composer;

defined('ABSPATH') || exit;

$form_type = $options['form_type'];
?>

<?php $controls->form_type('form_type', '', ['wp' => 'WP Media', 'url' => 'External URL', 'logo'=>'Site logo']) ?>

<?php if ($form_type === 'wp') { ?>
<?php $controls->media('image', '')?>
<?php } else if ($form_type === 'url') {?>
<?php $controls->url('external_url', 'Image URL')?>
<?php } else { ?>
<?php $controls->text('width', __('Width', 'automation') . '&nbsp;(px)')?>
<?php } ?>


<?php $controls->url('url', 'Link')?>
<?php $controls->text('alt', 'Alternative text')?>

<?php $controls->commons() ?>