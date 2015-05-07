<?php

namespace local_moodle_reminders;

require_once('factory.php');
require_once(__DIR__ .'/../assignment.php');

class assignment_factory extends factory {
    /**
     * Maps a row retrieved in an SQL query to an initialized object of the class type
     * @param $row object
     * @param bool $load_dependencies
     * @return object
     */
    protected function construct_record($row, $load_dependencies) {
        return new assignment($row->module_instance_id, $row->name, $row->submission_count);
    }
}