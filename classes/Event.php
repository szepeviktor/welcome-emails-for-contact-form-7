<?php

namespace Automation;

defined('ABSPATH') || exit;

class Event {

    var $id = 0;
    var $type = '';
    var $created_at = null;
    var $data = [];

    public function __construct() {

    }

}
