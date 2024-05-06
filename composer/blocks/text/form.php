<?php
namespace Automation\Composer;

defined('ABSPATH') || exit;

$form_type = $options['form_type'];
?>
<?php $controls->form_type('form_type', '', ['plain' => 'Simple', 'editor' => 'Rich', 'html' => 'Raw HTML']) ?>
<?php if ($form_type === 'editor') { ?>
    <?php $controls->editor('text', '') ?>
<?php } else { ?>
    <?php $controls->textarea('text', '') ?>
<?php } ?>

<?php if ($form_type === 'plain') { ?>
    <?php $controls->select('align', '', ['left' => 'Left', 'right' => 'Right', 'center' => 'Center']) ?>
    <?php $controls->font() ?>
<?php } ?>

<?php $controls->commons() ?>

<?php if ($form_type === 'html') { ?>
    <p>Email readers have limits with modern HTML, please test it carefully.</p>
<?php } ?>
