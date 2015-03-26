<?php

require_once('factory.php');
require_once(__DIR__ .'/../teacher.php');
require_once('course_factory.php');

class teacher_factory extends factory {
    private $course_factory;

    public function __construct() {
        $this->course_factory = new course_factory();
    }

    protected function construct_record($row) {
        return new teacher(
            $row->id,
            $row->email,
            $row->last_login,
            $this->course_factory->load_records('course.sql', array('teacher_id' => $row->id))
        );
    }
}