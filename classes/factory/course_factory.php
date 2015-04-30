<?php

require_once(__DIR__ . '/factory.php');
require_once(__DIR__ . '/../course.php');
require_once(__DIR__ . '/assignment_factory.php');
require_once(__DIR__ . '/discussion_factory.php');
require_once(__DIR__ . '/student_factory.php');

class course_factory extends factory {
    private $assignment_factory, $discussion_factory, $student_factory;

    function __construct() {
        $this->assignment_factory = new assignment_factory();
        $this->discussion_factory = new discussion_factory();
        $this->student_factory = new student_factory();
    }

    protected function construct_record($row, $load_dependencies) {
        $course = new course(
            $row->id,
            $row->name,
            $row->last_accessed_date
        );

        if ($load_dependencies) {
            $course->assignments = $this->assignment_factory->load_records('assignment.sql',
                array('course_id' => $row->id));
            $course->discussions = $this->discussion_factory->load_records('discussion.sql',
                array('course_id' => $row->id, 'teacher_id' => $row->teacher_id));
            $course->students = $this->student_factory->load_records('student.sql',
                array('course_id' => $row->id), array('action_cases' => $this->student_factory->get_action_cases_sql()));
        }

        return $course;
    }
}