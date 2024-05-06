<?php

namespace Automation;

defined('ABSPATH') || exit;

class EventType {

    var $id = '';
    var $name = '';
    var $fields = [];

    public function __construct(string $id, string $name, array $fields = []) {
        $this->id = sanitize_key($id);
        $this->name = sanitize_text_field($name);
        if (is_array($fields)) {
            $this->fields = $fields;
        }
    }

}
