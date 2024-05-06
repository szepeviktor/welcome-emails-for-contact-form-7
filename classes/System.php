<?php

namespace Automation;

defined('ABSPATH') || exit;

class System {

    const JOB_OK = 0;
    const JOB_LATE = 1;
    const JOB_MISSING = 2;

    static function get_job_status() {

        $x = wp_next_scheduled('automation_run');
        if ($x === false) {
            return self::JOB_MISSING;
        }

        if (time() - $x > 900) {
            return self::JOB_LATE;
        }
        return self::JOB_OK;
    }

}
