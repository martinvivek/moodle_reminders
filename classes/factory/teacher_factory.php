<?php

namespace local_moodle_reminders;

require_once(__DIR__ . '/factory.php');
require_once(__DIR__ . '/../teacher.php');
require_once(__DIR__ . '/course_factory.php');

class teacher_factory extends factory {
    private $course_factory;

    public function __construct() {
        $this->course_factory = new course_factory();
    }

    protected function construct_record($row, $load_dependencies) {
        $teacher = new teacher(
            $row->id,
            $row->email,
            $row->last_login
        );

        if ($load_dependencies) {
            $teacher->courses = $this->course_factory->load_records('course.sql', array('teacher_id' => $row->id));
        }

        return $teacher;
    }
}