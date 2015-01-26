<?php

namespace teacher;

require_once(__DIR__ . '/../moodle_environment.php');
require_once(__DIR__ . '/../linkable.php');
require_once(__DIR__ . '/student.php');

/**
 * Stores basic class info along with an array of students enrolled, and assignments that have ungraded submissions
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
     * Load a courses' info from the moodle database and then load the students associated with that course
     * @param $id integer
     * @return course
     */
    static function get($id) {
        global $DB;
        $course_row = $DB->get_record_sql('
            SELECT {course}.fullname AS name, GROUP_CONCAT(DISTINCT {user}.id) as student_ids,
              GROUP_CONCAT(DISTINCT {assign_submission}.assignment) AS assignment_ids  FROM {course}
            LEFT JOIN {context} ON contextlevel = 50 AND {context}.instanceid = {course}.id
            LEFT JOIN {role_assignments} ON roleid = 5 AND {context}.id = {role_assignments}.contextid
            LEFT JOIN {user} ON {role_assignments}.userid = {user}.id
            LEFT JOIN {assign} ON {assign}.course = {course}.id
            LEFT JOIN {assign_grades} ON {assign_grades}.assignment = {assign}.id
            LEFT JOIN {assign_submission} ON {assign_submission}.latest = 1 AND
              {assign_submission}.id != {assign_grades}.assignment AND {assign_submission}.assignment = {assign}.id
            WHERE {course}.id = ? LIMIT 1;
        ', array($id));

        $student_ids = explode(',', $course_row->student_ids, -1);
        $students = array_map(function($student_id) use ($id) {
            return student::get(intval($student_id), $id);
        }, $student_ids);

        $assignment_ids = explode(',', $course_row->assignment_ids, -1);
        $assignments = array_map(function($assignment_id) use ($id) {
            return assignment::get(intval($assignment_id), $id);
        }, $assignment_ids);

        return new course($id, $course_row->name, $students,  $assignments);
    }

    function get_link() {
        global $CFG;
        return $CFG->wwwroot . '/course/view.php?id=' . $this->id;
    }
}