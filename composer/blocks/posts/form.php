<?php

namespace Automation\Composer;

defined('ABSPATH') || exit;
?>

<?php //$controls->select('layout', '', ['1'=>'Layout 1', '2'=>'Layout 2'])  ?>

<?php $controls->checkbox('show_image', 'Show image') ?>
<?php $controls->select_number('max', __('Max posts', 'automation'), 1, 30) ?>
<?php $controls->font('title_font') ?>
<?php $controls->commons() ?>