<?php

require_once('factory.php');
require_once('assignment.php');

class assignment_factory extends factory {
    /**
     * Maps a row retrieved in an SQL query to an initialized object of the class type
     * @param $row object
     * @return object
     */
    protected function construct_record($row) {
        return new assignment($row->module_instance_id, $row->name, $row->submission_count);
    }
}