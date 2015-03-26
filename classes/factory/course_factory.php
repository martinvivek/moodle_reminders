<?php

require_once('factory.php');
require_once(__DIR__ .'/../course.php');
require_once('assignment_factory.php');
require_once('discussion_factory.php');
require_once('student_factory.php');

class course_factory extends factory {
    private $assignment_factory, $discussion_factory, $student_factory;

    function __construct() {
        $this->assignment_factory = new assignment_factory();
        $this->discussion_factory = new discussion_factory();
        $this->student_factory = new student_factory();
    }

    protected function construct_record($row) {
        return new course(
            $row->id,
            $row->name,
            $row->last_accessed_date,
            $this->assignment_factory->load_records('assignment.sql', array('course_id' => $row->id)),
            $this->discussion_factory->load_records('discussion.sql', array('course_id' => $row->id, 'teacher_id' => $row->teacher_id)),
            $this->student_factory->load_records('student.sql',
                array('course_id' => $row->id), array('action_cases' => $this->student_factory->get_action_cases_sql()))
        );
    }
}