<?php
namespace Automation\Composer;

defined('ABSPATH') || exit;

/* @var $controls Controls */
?>

<?php $controls->text('button_label') ?>
<?php $controls->text('button_url', '', ['placeholder'=>'https://...']) ?>
<?php $controls->color('button_color') ?>
<?php $controls->color('button_label_color') ?>

<?php $controls->commons() ?>
