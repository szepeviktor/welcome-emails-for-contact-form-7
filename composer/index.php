<?php

namespace Automation\Composer;

defined('ABSPATH') || exit;

wp_enqueue_script('jquery-ui-sortable');
wp_enqueue_script('jquery-ui-draggable');
wp_enqueue_script('jquery-ui-dialog');
wp_enqueue_style('wp-jquery-ui-dialog');
wp_enqueue_media();
?>
<script>

    const Composer = {
        id: 0,
        block_tools: null,
        block_tools_popper: null,
        $current_block: null,
        $cmp_form: null,
        nonce: '<?php echo esc_js(wp_create_nonce('composer')) ?>',

        init: function (id) {
            this.id = id;
            this.$cmp_form = jQuery('#cmp-form');
            this.init_drag_and_drop();

            data = [{name: 'id', value: id}, {name: 'action', value: 'composer_get'}];
            jQuery.get(ajaxurl, data, function (response) {
                jQuery("#cmp-content").html(response.html);
                document.getElementById('cmp-subject').value = response.subject;
                // @todo Settings
            }).fail(function () {
                alert('An error occurred without a valid reason');
            });

            this.block_tools = document.getElementById('cmp-block-tools');
        },

        init_drag_and_drop: function () {

            jQuery(".cmp-block-type").draggable({
                helper: 'clone',
                connectToSortable: '#cmp-content'
            });

            jQuery("#cmp-content").sortable({
                receive: function (event, ui) {
                    Composer.hide_block_tools();

                    ui.helper.css({
                        width: '100%',
                        height: 'auto'
                    });

                    var type = ui.item[0].dataset.type;
                    jQuery.post(ajaxurl, {'type': type, 'action': 'composer_block', '_ajax_nonce': Composer.nonce}, function (response) {
                        ui.helper.html(response.html);
                        ui.helper.attr('data-options', response.options);
                        Composer.$current_block = ui.helper;
                        jQuery('#cmp-form').html(response.form);
                        Composer.init_form();
                    });

                }
            });

            jQuery("#cmp-content").on('click', '.cmp-block-type', function (e) {
                // "this" is NOT "Composer" but the clicked element
                e.preventDefault();
                Composer.$current_block = jQuery(this);
                var type = Composer.$current_block.attr('data-type');
                var options = Composer.$current_block.attr('data-options');

                Composer.$cmp_form.html('<div class="cmp-ellipsis"><div></div><div></div><div></div><div></div></div>');

                jQuery.post(ajaxurl, {'type': type, 'options': options, 'action': 'composer_form', '_ajax_nonce': Composer.nonce}, function (response) {
                    Composer.$cmp_form.html(response);
                    Composer.init_form();
                });

                Composer.show_block_tools();

            });

            jQuery('#cmp-form').change(function (event) {

                var data = Composer.$cmp_form.serializeArray();
                // Fix the checkboxes
                jQuery('#cmp-form input[type="checkbox"]').each(function () {
                    if (!this.checked) {
                        data.push({name: this.name, value: '0'});
                    }
                });
                data = data.concat(jQuery("#cmp-email-form").serializeArray());
                data.push({name: 'action', value: 'composer_block'});
                data.push({name: 'type', value: Composer.$current_block.attr('data-type')});
                data.push({name: '_ajax_nonce', value: Composer.nonce});

                jQuery.post(ajaxurl, data, function (response) {
                    Composer.$current_block.html(response.html);
                    Composer.$current_block.attr('data-options', response.options);
                    if (event.target.dataset.reload === 'true') {
                        Composer.$cmp_form.html(response.form);
                        Composer.init_form();
                    }
                }).fail(function () {
                    alert('An error occurred without a valid reason');
                });

            });
        },

        // The "completed" argument ia a callback
        save: function (completed) {
            var content = jQuery("#cmp-content").html();
            var data = [];
            data.push({name: 'subject', value: document.getElementById('cmp-subject').value});
            data.push({name: 'html', value: content});
            data.push({name: 'id', value: this.id});
            data.push({name: 'action', value: 'composer_save'});
            data.push({name: '_ajax_nonce', value: this.nonce});

            jQuery.post(ajaxurl, data, function (response) {
                completed();
            }).fail(function () {
                alert('An error occurred without a valid reason');
            });
        },

        send_test: function () {
            var content = jQuery("#cmp-content").html();
            var data = [];
            data.push({name: 'subject', value: document.getElementById('cmp-subject').value});
            data.push({name: 'html', value: content});
            data.push({name: 'id', value: this.id});
            data.push({name: 'action', value: 'composer_test'});
            data.push({name: 'email', value: document.getElementById('cmp-test-email').value});
            data.push({name: '_ajax_nonce', value: this.nonce});

            jQuery.post(ajaxurl, data, function (response) {
                Automation.toast('<?php echo esc_js(__('Sent', 'automation')) ?>');
            }).fail(function () {
                alert('An error occurred without a valid reason');
            });
        },

        init_form: function () {
            jQuery('#cmp-form .cmpf-color').spectrum({
                type: 'color',
                allowEmpty: true,
                showAlpha: false,
                showInput: true,
                preferredFormat: 'hex'
            });

            this.init_tinymce();
        },

        select_media: function (id) {
            var tnp_uploader = wp.media({
                title: "Select an image",
                button: {
                    text: "Select"
                },
                multiple: false
            }).on("select", function () {
                var media = tnp_uploader.state().get("selection").first();
                document.getElementById(id).value = media.id;
                jQuery("#" + id).trigger("change");

            }).open();

        },

        delete_current_block: function () {
            if (this.$current_block) {
                this.$current_block.remove();
                this.$current_block = null;
                this.hide_block_tools();
            }
        },

        show_block_tools: function () {
            if (this.block_tools_popper) {
                this.block_tools_popper.destroy();
            }

            this.block_tools_popper = Popper.createPopper(this.$current_block.get(0), this.block_tools, {
                placement: 'top-end',
                strategy: 'fixed',
                modifiers: [
                    {
                        name: 'offset',
                        options: {
                            offset: [0, -15]
                        }
                    }
                ]

            });
            this.block_tools.style.display = 'block';
        },

        hide_block_tools: function () {
            if (this.block_tools_popper) {
                this.block_tools_popper.destroy();
                this.block_tools_popper = null;
            }
            this.block_tools.style.display = 'none';
        },

        init_tinymce: function () {
            tinymce.remove();
            tinymce.init({
                selector: '#cmp-form textarea.cmpf-tinymce',
                height: 300,
                menubar: false,
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table paste code help wordcount'
                ],
                toolbar: 'undo redo | formatselect | ' +
                        'bold italic backcolor | alignleft aligncenter ' +
                        'alignright alignjustify | bullist numlist outdent indent | ' +
                        'removeformat | help',
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
                init_instance_callback: function (editor) {
                    editor.on('blur', function (e) {
                        tinymce.triggerSave();
                        jQuery(editor.getElement()).trigger('change');
                    });
                }
            });
        }

    }
</script>

<div id="cmp-placeholders" title="Placeholders">
    <?php echo implode('<br>', $cmp_placeholders) ?>
</div>

<div id="cmp-test" title="Test" style="display: none">
    <input type="email" name="cmp-email" id="cmp-test-email" placeholder="Email address"> <input type="button" class="button-primary" value="Send" onclick="Composer.send_test()">
</div>

<div id="cmp-block-tools" role="tooltip"><span class="dashicons dashicons-no" onclick="Composer.delete_current_block()"></span></div>

<form id="cmp-email-form" style="display: none;">

</form>

<div id="cmp-wrapper">
    <div id="cmp-topbar">
        <div>
            <input type="text" id="cmp-subject">
        </div>
        <div id="cmp-actions">
            <span class="dashicons dashicons-email" onclick="jQuery('#cmp-test').dialog({width: 500})"></span>
            <!--<span class="dashicons dashicons-dashboard" onclick="jQuery('#cmp-templates').dialog()"></span>-->
            <!--<span class="dashicons dashicons-shortcode" onclick="jQuery('#cmp-placeholders').dialog({width: 500})"></span>-->
        </div>
    </div>
    <div id="cmp-composer">
        <div id="cmp-blocks">
            <div class="cmp-block-type" data-type="image"><span class="dashicons dashicons-format-image"></span></div>
            <div class="cmp-block-type" data-type="text"><span class="dashicons dashicons-editor-alignleft"></span></div>
            <div class="cmp-block-type" data-type="posts"><span class="dashicons dashicons-admin-page"></span></div>
            <div class="cmp-block-type" data-type="button"><span class="dashicons dashicons-share-alt2"></span></div>
        </div>
        <div id="cmp-content">
            <div class="cmp-ellipsis"><div></div><div></div><div></div><div></div></div>
        </div>

        <div id="cmp-options">
            <form id="cmp-form">
            </form>
        </div>

    </div>
</div>

