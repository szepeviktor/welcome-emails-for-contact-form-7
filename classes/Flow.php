<?php

namespace Automation;

defined('ABSPATH') || exit;

class Flow {

    var $id;
    var $event_type_id;
    var $status = 0;
    var $actions = [];
    var $created_at;
    var $updated_at;
    var $email_id = 0;
    var $email_active = 0;
    var $email2_id = 0;
    var $email2_active = 0;
    var $email2_delay = 7;
    var $email2_delay_um = 'd';

    public function __construct() {

    }

}
