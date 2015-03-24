<?php

require_once('factory.php');
require_once('course.php');
require_once('student_factory.php');

class course_factory extends factory {
    private $student_factory;

    function __construct() {
        $this->student_factory = new student_factory();
    }

    protected function construct_record($row) {
        return new course(
            $row->id,
            $row->name,
            $row->last_accessed_date,
            $this->student_factory->load_records('student.sql', array_merge(array('course_id' => $row->id), $this->student_factory->get_action_points()))
        );
    }
}