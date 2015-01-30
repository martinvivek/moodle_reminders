<?php

namespace teacher;

require_once(__DIR__ . '/../moodle_environment.php');
require_once(__DIR__ . '/../linkable.php');
require_once(__DIR__ . '/discussion.php');
require_once(__DIR__ . '/student.php');
require_once(__DIR__ . '/assignment.php');

/**
 * Stores basic class info along with an array of students enrolled, and assignments that have ungraded submissions
 * @package teacher
 */
class course extends \linkable {
    /**
     * @var $id integer
     * @var $name string
     * @var $discussions array(discussion)
     * @var $students array(student)
     * @var $assignments array(assignment)
     */
    public $id, $name, $discussions, $students, $assignments;

    function __construct($id, $name, $discussions = array(), $students = array(), $assignments = array()) {
        $this->id = $id;
        $this->name = $name;
        $this->discussions = $discussions;
        $this->students = $students;
        $this->assignments = $assignments;
    }

    /**
     * Load a courses' info from the moodle database and then load the students associated with that course
     * @param $id integer
     * @param $teacher_id integer
     * @return course
     */
    static function get($id, $teacher_id) {
        global $DB;
        $course_row = $DB->get_record_sql('
            SELECT {course}.fullname AS name, GROUP_CONCAT(DISTINCT {user}.id) as student_ids, GROUP_CONCAT(DISTINCT {assign}.id) AS assignment_ids, GROUP_CONCAT(DISTINCT CASE WHEN {logstore_standard_log}.id IS NOT NULL THEN NULL ELSE {forum_discussions}.id END) as discussion_ids FROM {course}
            LEFT JOIN {context} ON contextlevel = 50 AND {context}.instanceid = {course}.id
            LEFT JOIN {role_assignments} ON roleid = 5 AND {context}.id = {role_assignments}.contextid
            LEFT JOIN {user} ON {role_assignments}.userid = {user}.id
            LEFT JOIN {assign} ON {assign}.course = {course}.id
            LEFT JOIN {forum_discussions} ON {forum_discussions}.course = {course}.id
            LEFT JOIN {logstore_standard_log} ON {logstore_standard_log}.eventname = "\\mod_forum\\event\\discussion_viewed" AND {logstore_standard_log}.objectid = {forum_discussions}.id AND {logstore_standard_log}.userid = :teacher_id
            WHERE {course}.id = :course_id LIMIT 1;
        ', array('teacher_id' => $teacher_id, 'course_id' => $id));

        $discussions = null;
        if ($course_row->discussion_ids) {
            $discussion_ids = explode(',', $course_row->discussion_ids);
            $discussions = array_map(function ($discussion_id) use ($id) {
                return discussion::get(intval($discussion_id));
            }, $discussion_ids);
        }

        $students = null;
        if($course_row->student_ids) {
            $student_ids = explode(',', $course_row->student_ids);
            $students = array_map(function ($student_id) use ($id) {
                return student::get(intval($student_id), $id);
            }, $student_ids);
        }

        $assignments = null;
        if($course_row->assignment_ids) {
            $assignment_ids = explode(',', $course_row->assignment_ids);
            $assignments = array_filter(array_map(function ($assignment_id) use ($id) {
                $assignment = assignment::get(intval($assignment_id));
                if ($assignment->submissions != null) return $assignment;
            }, $assignment_ids));
        }

        return new course($id, $course_row->name, $discussions, $students, $assignments);
    }

    /**
     * @return array Returns array containing two arrays of the students sorted by _logins_this_week_
     * and _logins_course_total_ respectively; Meant for displaying the students in a table
     */
    function students_in_table_format() {
        $students_by_logins_this_week = $this->students;
        usort($students_by_logins_this_week, function ($student1, $student2) {
            return $student1->logins_this_week - $student2->logins_this_week;
        });

        $students_by_logins_course_total = $this->students;
        usort($students_by_logins_course_total, function ($student1, $student2) {
            return $student1->logins_course_total - $student2->logins_course_total;
        });

        $students_in_table_format = array();
        for($i = 0; $i < count($this->students); $i++) {
            $students_in_table_format[$i] = array(
                'this_week' => array(
                    'name' => $students_by_logins_this_week[$i]->name,
                    'logins' => $students_by_logins_this_week[$i]->logins_this_week),
                'course_total' => array(
                    'name' => $students_by_logins_course_total[$i]->name,
                    'logins' => $students_by_logins_course_total[$i]->logins_course_total)
            );
        }
        return $students_in_table_format;
    }

    function get_link() {
        global $CFG;
        return $CFG->wwwroot . '/course/view.php?id=' . $this->id;
    }
}