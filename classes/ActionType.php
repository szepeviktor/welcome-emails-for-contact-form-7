<?php

namespace Automation;

defined('ABSPATH') || exit;

class ActionType {

    var $id;
    var $name;
    var $fields;
    var $callback;

    public function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }

    public function run($action) {

    }

}
