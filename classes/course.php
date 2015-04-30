<?php

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/discussion.php');
require_once(__DIR__ . '/student.php');
require_once(__DIR__ . '/assignment.php');

/**
 * Stores basic class info along with an array of students enrolled, and assignments that have ungraded submissions
 */
class course {
    /**
     * @var $id integer
     * @var $name string
     * @var $last_accessed \DateTime
     * @var $assignments array(assignment)
     * @var $discussions array(discussion)
     * @var $students array(student)
     */
    public $id, $name, $last_accessed, $assignments, $discussions, $students;

    function __construct($id, $name, $last_accessed, $assignments = null, $discussions = null, $students = null) {
        $this->id = $id;
        $this->name = $name;
        $this->last_accessed = $last_accessed;
        $this->assignments = $assignments;
        $this->discussions = $discussions;
        $this->students = $students;
    }

    function get_link() {
        global $CFG;
        return $CFG->wwwroot . '/course/view.php?id=' . $this->id;
    }

    function get_student_mailer_link() {
        global $CFG;
        return $CFG->wwwroot . '/local/moodle_reminders/student_mailer.php?course_id=' . $this->id;
    }
}