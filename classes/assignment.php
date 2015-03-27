<?php

require_once(__DIR__ . '/../../../config.php');

/**
 * @package teacher
 */
class assignment {
    /**
     * @var $module_instance_id integer Used in the get_link() method
     * @var $name string
     * @var $submission_count int The number of ungraded submissions for this assignment
     */
    public $module_instance_id, $name, $submission_count;

    function __construct($module_instance_id, $name, $submission_count) {
        $this->module_instance_id = $module_instance_id;
        $this->name = $name;
        $this->submission_count = $submission_count;
    }

    /**
     * @return string A link to this object's page in moodle; Note that a login may be required
     */
    function get_link() {
        global $CFG;
        return $CFG->wwwroot . '/mod/assign/view.php?id=' . $this->module_instance_id . '&action=grading';
    }
}
