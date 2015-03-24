<?php

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/discussion.php');
require_once(__DIR__ . '/student.php');
require_once(__DIR__ . '/assignment.php');

/**
 * Stores basic class info along with an array of students enrolled, and assignments that have ungraded submissions
 * @package teacher
 */
class course {
    /**
     * @var $id integer
     * @var $name string
     * @var $last_accessed \DateTime
     * @var $discussions array(discussion)
     * @var $students array(student)
     * @var $assignments array(assignment)
     */
    public $id, $name, $last_accessed, $discussions, $students, $assignments;

    function __construct($id, $name, $last_accessed, $discussions = array(), $students = array(), $assignments = array()) {
        $this->id = $id;
        $this->name = $name;
        $this->last_accessed = $last_accessed;
        $this->discussions = $discussions;
        $this->students = $students;
        $this->assignments = $assignments;
    }

    function get_link() {
        global $CFG;
        return $CFG->wwwroot . '/course/view.php?id=' . $this->id;
    }
}