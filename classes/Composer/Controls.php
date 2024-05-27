<?php

namespace Automation\Composer;

defined('ABSPATH') || exit;

class Controls {

    var $data;
    var $prefix;

    function __construct($data) {
        $this->data = $data;
        $this->prefix = 'options';
    }

    public function _open($subclass = '') {
        echo '<div class="cmpf-field ', esc_attr($subclass), '">';
    }

    public function _close() {
        echo '</div>';
    }

    public function _label($text, $for = '') {
        if (empty($text)) {
            return;
        }
        echo '<label class="cmpf-label">', esc_html($text), '</label>';
    }

    public function _description($description) {
        if (empty($description)) {
            return;
        }

        echo '<div class="cmpf-description">', wp_kses_post($description), '</div>';
    }

    public function _id($name) {
        return $this->prefix . '-' . sanitize_key($name);
    }

    public function _name($name) {
        return $this->prefix . '[' . sanitize_key($name) . ']';
    }

    public function _name_id_attrs($name) {
        echo ' id="', esc_attr($this->_id($name)), '" name="', esc_attr($this->_name($name)), '" ';
    }

    public function _name_id_value_attrs($name, $def = '') {
        echo ' id="', esc_attr($this->_id($name)), '" name="', esc_attr($this->_name($name)),
        '" value="', esc_attr($this->_value($name, $def)), '" ';
    }

    public function _value($name, $def = null) {
        if (!isset($this->data[$name])) {
            return $def;
        }
        return $this->data[$name];
    }

    public function _value_attr($name, $def = '') {
        echo ' value="', esc_attr($this->_value($name, $def)), '" ';
    }

    public function _select_options($options, $value) {
        foreach ($options as $key => $text) {
            echo '<option value="', esc_attr($key), '"';
            if ($value == $key) {
                echo ' selected';
            }
            echo '>', esc_html($text), '</option>';
        }
    }

    /**
     * Displays only the input field.
     *
     * @param string $name
     * @param array $attrs
     */
    public function input_only($name, $attrs = []) {

        $attrs = array_merge(['label' => '', 'placeholder' => '', 'width' => 0, 'type' => 'text'], $attrs);

        $value = $this->_value($name);

        echo '<input placeholder="', esc_attr($attrs['placeholder']), '" type="', esc_attr($attrs['type']), '"';

        $this->_name_id_value_attrs($name);

        if (!empty($attrs['width'])) {
            echo ' style="width: ', ((int) $attrs['width']), 'px"';
        }

        if (isset($attrs['min'])) {
            echo ' min="' . ((int) $attrs['min']) . '"';
        }

        if (isset($attrs['max'])) {
            echo ' max="' . ((int) $attrs['max']) . '"';
        }

        echo '>';
    }

    public function input($name, $label = '', $attrs = []) {
        $attrs = array_merge(['description' => '', 'label' => '', 'placeholder' => '', 'width' => 0, 'label_after' => '', 'type' => 'text'], $attrs);
        $this->_open();
        $this->_label($label);

        $this->input_only($name, $attrs);

        if (!empty($attrs['label_after'])) {
            echo wp_kses($attrs['label_after'], null);
        }

        $this->_description($attrs['description']);
        $this->_close();
    }

    public function text($name, $label = '', $attrs = []) {
        $attrs['type'] = 'text';
        $this->input($name, $label, $attrs);
    }

    public function checkbox($name, $label = '', $attrs = []) {
        $attrs = array_merge(['description' => ''], $attrs);
        $this->_open('cmpf-checkbox');
        if ($label != '') {
            echo '<label>';
        }
        echo '<input type="checkbox" value="1"';
        $this->_name_id_attrs($name);
        if (!empty($this->data[$name])) {
            echo ' checked';
        }
        echo '>';
        if ($label != '') {
            echo '&nbsp;' . esc_html($label) . '</label>';
        }
        $this->_description($attrs['description']);
        $this->_close();
    }

    public function textarea($name, $label = '', $attrs = []) {
        $attrs = array_merge(['description' => '', 'width' => '100%', 'height' => '150', 'placeholder' => ''], $attrs);
        $this->_open();
        $this->_label($label);
        echo '<textarea wrap="off" style="width:', esc_attr($attrs['width']), ';height:', esc_attr($attrs['height']), 'px"';
        $this->_name_id_attrs($name);
        echo '>';
        echo esc_html($this->_value($name));
        echo '</textarea>';
        $this->_description($attrs['description']);
        $this->_close();
    }

    public function editor($name, $label = '', $attrs = []) {
        $attrs = array_merge(['description' => '', 'width' => '100%', 'height' => '150', 'placeholder' => ''], $attrs);
        $this->_open();
        $this->_label($label);
        echo '<textarea class="cmpf-tinymce" wrap="off" style="width:', esc_attr($attrs['width']), ';height:', esc_attr($attrs['height']), 'px"';
        $this->_name_id_attrs($name);
        echo '>';
        echo esc_html($this->_value($name));
        echo '</textarea>';
        $this->_description($attrs['description']);
        $this->_close();
    }

    public function select($name, $label = '', $options = [], $attrs = []) {
        $attrs = array_merge(['description' => '', 'class' => ''], $attrs);
        $this->_open();
        $this->_label($label);
        $value = $this->_value($name);

        echo '<select';
        $this->_name_id_attrs($name);
        if ($attrs['class']) {
            echo ' class="', esc_attr($attrs['class']), '"';
        }

        echo '>';

        $this->_select_options($options, $value);

        echo '</select>';

        $this->_description($attrs['description']);
        $this->_close();
    }

    function color($name, $label = '', $attrs = []) {
        $attrs = array_merge(['description' => '', 'label' => '', 'label_after' => ''], $attrs);
        $this->_open();
        $this->_label($label);
        $value = $this->_value($name);

        echo '<input class="cmpf-color" type="text" style="width: 50px"';

        $this->_name_id_value_attrs($name);

        echo '>';

        if (!empty($attrs['label_after'])) {
            echo esc_html($attrs['label_after']);
        }

        $this->_description($attrs['description']);
        $this->_close();
    }

    function color_only($name, $attrs = []) {
        echo '<input class="cmpf-color" type="color" style="width: 50px"';
        $this->_name_id_value_attrs($name);
        echo '>';
    }

    function url($name, $label = '') {
        $this->input($name, $label, ['type' => 'url']);
    }

    function media($name, $label = '', $attrs = []) {
        $attrs = array_merge(['description' => '', 'placeholder' => '', 'label_after' => '', 'type' => 'text'], $attrs);
        $this->_open();
        $this->_label($label);
        $value = $this->_value($name);

        echo '<input type="hidden"';
        $this->_name_id_value_attrs($name);
        echo '>';

        echo '<button onclick="Composer.select_media(\'' .
        esc_attr($this->_id($name)) . '\'); return false;"><span class="dashicons dashicons-format-image"></span></button>';

        if (!empty($attrs['label_after'])) {
            echo esc_html($attrs['label_after']);
        }

        $this->_description($attrs['description']);
        $this->_close();
    }

    public function form_type($name, $label = '', $options = [], $attrs = []) {
        $this->_open('cmpf-form-type');
        $this->_label($label);
        $value = $this->_value($name);

        echo '<select data-reload="true"';
        $this->_name_id_attrs($name);
        echo '>';

        $this->_select_options($options, $value);

        echo '</select>';

        $this->_description($attrs['description'] ?? '');
        $this->_close();
    }

    function font($name = 'font', $label = '', $attrs = []) {
        $this->_open('tnp-font');
        $this->_label($label);
        $this->font_family($name . '_family');

        $this->font_size($name . '_size');

        $this->font_weight($name . '_weight');

        $this->color_only($name . '_color');
        $this->_description($attrs['description'] ?? '');
        $this->_close();
    }

    function font_size($name = 'font_size') {
        $value = $this->_value($name);

        echo '<select class="cmpf-font-size"';
        $this->_name_id_attrs($name);
        echo '>';
        echo '<option value="">-</option>';
        for ($i = 8; $i <= 100; $i++) {
            echo '<option value="', esc_attr($i), '"';
            if ($value == $i) {
                echo ' selected';
            }
            echo '>', esc_html($i), '</option>';
        }
        echo '</select>';
    }

    function font_weight($name = 'font_weight', $show_empty_option = false) {
        $value = $this->_value($name);

        $fonts = ['normal' => 'Normal', 'bold' => 'Bold'];

        echo '<select class="cmpf-font-weight"';
        $this->_name_id_attrs($name);
        echo '>';

        foreach ($fonts as $key => $font) {
            echo '<option value="', esc_attr($key), '"';
            if ($value == $key) {
                echo ' selected';
            }
            echo '>', esc_html($font), '</option>';
        }
        echo '</select>';
    }

    function font_family($name = 'font_family') {
        $value = $this->_value($name);

        $fonts = ['' => 'Default', 'Helvetica, Arial, sans-serif' => 'Helvetica, Arial',
            'Arial Black, Gadget, sans-serif' => 'Arial Black, Gadget',
            'Garamond, serif' => 'Garamond',
            'Courier, monospace' => 'Courier',
            'Comic Sans MS, cursive' => 'Comic Sans MS',
            'Impact, Charcoal, sans-serif' => 'Impact, Charcoal',
            'Tahoma, Geneva, sans-serif' => 'Tahoma, Geneva',
            'Times New Roman, Times, serif' => 'Times New Roman',
            'Verdana, Geneva, sans-serif' => 'Verdana, Geneva'];

        echo '<select class="cmpf-font-family"';
        $this->_name_id_attrs($name);
        echo '>';
        foreach ($fonts as $font => $label) {
            echo '<option value="', esc_attr($font), '"';
            if ($value == $font) {
                echo ' selected';
            }
            echo '>', esc_html($label), '</option>';
        }
        echo '</select>';
    }

    public function commons($name = 'block', $label = '', $attrs = []) {
        $attrs = array_merge(['padding_top' => 0, 'padding_left' => 0, 'padding_right' => 0, 'padding_bottom' => 0], $attrs);

        echo '<div class="cmpf-commons">';

        echo '&larr;';
        $this->input_only('block_padding_left', ['width' => 35]);

        echo '&nbsp;&nbsp;&nbsp;&uarr;';
        $this->input_only('block_padding_top', ['width' => 35]);
        echo '&nbsp;&nbsp;&nbsp;';

        $this->input_only('block_padding_bottom', ['width' => 35]);
        echo '&darr;&nbsp;&nbsp;&nbsp;';

        $this->input_only('block_padding_right', ['width' => 35]);
        echo '&rarr;&nbsp;&nbsp;&nbsp;';

        $this->color_only('block_background');

        echo '</div>';
    }

    public function select_number($name, $label = '', $min = 0, $max = 10, $attrs = []) {
        for ($i = $min; $i <= $max; $i++) {
            $options['' . $i] = $i;
        }
        $this->select($name, $label, $options, $attrs);
    }
}
