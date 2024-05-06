<?php

namespace Automation;

defined('ABSPATH') || exit;

class Action {

    var $id = 0;
    var $event_id = 0;
    var $type = '';
    var $options = [];
    var $created_at = null;
    var $scheduled_at = null;
    var $processed_at = null;

    public function __construct() {

    }

}
