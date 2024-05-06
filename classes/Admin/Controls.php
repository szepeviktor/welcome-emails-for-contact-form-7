<?php

namespace Automation\Admin;

defined('ABSPATH') || exit;

class Controls {

    var $data = [];

    function __construct($data) {
        if (is_array($data)) {
            $this->data = $data;
        }
    }

    /**
     * Unescaped id for control fields.
     * @param string $name
     * @return string
     */
    public function _id($name) {
        return 'options-' . $name;
    }

    /**
     * Unescaped name for control fields.
     * @param string $name
     * @return string
     */
    public function _name($name) {
        return 'options[' . $name . ']';
    }

    /**
     * Return the value by the name or the default values if not found.
     *
     * @param string $name
     * @param mixed $def
     * @return mixed
     */
    function _value($name, $def = null) {
        return $this->data[$name] ?? $def;
    }

    /**
     * Echoes the id and name escaped attributes for a HTML control
     * @param string $name
     */
    public function _name_id_attrs($name) {
        echo ' id="', esc_attr($this->_id($name)), '" name="', esc_attr($this->_name($name)), '" ';
    }

    /**
     * Echoes the id, name and value escaped attributes for an "input" control
     * @param string $name
     * @param mixed $def
     */
    public function _name_id_value_attrs($name, $def = '') {
        echo ' id="', esc_attr($this->_id($name)), '" name="', esc_attr($this->_name($name)),
        '" value="', esc_attr($this->_value($name, $def)), '" ';
    }

    function _select_options($options, $value) {
        foreach ($options as $key => $text) {
            echo '<option value="', esc_attr($key), '"';
            if ($value == $key) {
                echo ' selected';
            }
            echo '>', esc_html($text), '</option>';
        }
    }

    function checkbox($name, $label = '', $attrs = []) {
        echo '<div class="atm-checkbox-wrapper">';
        if (!empty($label)) {
            echo '<label>';
        }
        echo '<input class="atm-checkbox" type="checkbox" value="1"';
        $this->_name_id_attrs($name);
        if (!empty($this->data[$name])) {
            echo ' checked';
        }
        echo '>';
        if (!empty($label)) {
            echo '&nbsp;' . esc_html($label) . '</label>';
        }
        echo '</div>';
    }

    public function input($name, $attrs = []) {
        $attrs = array_merge(['placeholder' => '', 'width' => '', 'label_after' => '', 'type' => 'text', 'class' => ''], $attrs);

        echo '<input placeholder="', esc_attr($attrs['placeholder']), '" type="', esc_attr($attrs['type']), '"';

        $this->_name_id_value_attrs($name);

        echo ' class="atmf-input', ' ', esc_attr($attrs['class']), '"';

        if (!empty($attrs['width'])) {
            echo ' style="width: ', (int) $attrs['width'], 'px"';
        }

        echo '>';

        if (!empty($attrs['label_after'])) {
            echo esc_attr($attrs['label_after']);
        }
    }

    public function text($name, $attrs = []) {
        $this->input($name, $attrs);
    }

    public function textarea($name, $attrs = []) {
        $attrs = array_merge(['width' => '100%', 'height' => '150', 'placeholder' => ''], $attrs);

        echo '<textarea class="atmf-field atmf-textarea" wrap="off" style="width:', esc_attr($attrs['width']),
                ';height:', esc_attr($attrs['height']), 'px"';
        $this->_name_id_attrs($name);
        echo '>';
        echo esc_html($this->_value($name));
        echo '</textarea>';
    }

    public function select($name, $options = [], $attrs = []) {
        echo '<select class="atmf-field atmf-select"';
        $this->_name_id_attrs($name);
        echo '>';

        $this->_select_options($options, $this->_value($name));

        echo '</select>';
    }

    function color($name) {
        $this->input($name, ['type' => 'color']);
    }

    function url($name) {
        $this->input($name, ['type' => 'url']);
    }
}
