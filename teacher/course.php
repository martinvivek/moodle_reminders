<?php

namespace teacher;

require_once(__DIR__ . '/../moodle_environment.php');
require_once(__DIR__ . '/../linkable.php');

/**
 * Stores basic class info along with an array of students enrolled
 * @package teacher
 */
class course extends \linkable {
    /**
     * @var $id integer
     * @var $name string
     * @var $students array(student)
     * @var $assignments array(assignment)
     */
    public $id, $name, $students, $assignments;

    function __construct($id, $name, $students = array(), $assignments = array()) {
        $this->id = $id;
        $this->name = $name;
        $this->students = $students;
        $this->assignments = $assignments;
    }

    /**
     * @param $id integer
     * @return course Load course from moodle database
     */
    static function get_by_id($id) {
        global $DB;
        $course_row = $DB->get_record_sql('
            SELECT {course}.fullname AS name, GROUP_CONCAT(DISTINCT {user}.id) as students FROM {course}
            LEFT JOIN {context} ON contextlevel = 50 AND {context}.instanceid = {course}.id
            LEFT JOIN {role_assignments} ON roleid = 5 AND {context}.id = {role_assignments}.contextid
            LEFT JOIN {user} ON {role_assignments}.userid = {user}.id
            WHERE {course}.id = ? LIMIT 1;
        ', array($id));
        // TODO students
        // TODO assignments
        return new course($id, $course_row->name /* TODO */);
    }

    function get_link() {
        global $CFG;
        return $CFG->wwwroot . '/course/view.php?id=' . $this->id;
    }
}