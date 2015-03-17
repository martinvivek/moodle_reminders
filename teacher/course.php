<?php

namespace teacher;

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

    /**
     * Load a courses' info from the moodle database and then load the students associated with that course
     * @param $id integer
     * @param $teacher_id integer
     * @return course
     */
    static function get($id, $teacher_id) {
        global $DB;
        $course_row = $DB->get_record_sql('
            SELECT {course}.fullname AS name, GROUP_CONCAT(DISTINCT {user}.id) as student_ids, GROUP_CONCAT(DISTINCT {assign}.id) AS assignment_ids, FROM_UNIXTIME(MAX(last_login.timecreated)) AS last_login_date, GROUP_CONCAT(DISTINCT
            CASE WHEN {logstore_standard_log}.id IS NOT NULL THEN NULL ELSE {forum_discussions}.id END) as discussion_ids FROM {course}
            LEFT JOIN {context} ON contextlevel = 50 AND {context}.instanceid = {course}.id
            LEFT JOIN {role_assignments} ON roleid = 5 AND {context}.id = {role_assignments}.contextid
            LEFT JOIN {user} ON {role_assignments}.userid = {user}.id
            LEFT JOIN {assign} ON {assign}.course = {course}.id
            LEFT JOIN {forum_discussions} ON {forum_discussions}.course = {course}.id
            LEFT JOIN {logstore_standard_log} ON {logstore_standard_log}.target = "discussion" AND {logstore_standard_log}.objectid = {forum_discussions}.id AND {logstore_standard_log}.userid = :teacher_id1 AND {logstore_standard_log}.timecreated > {forum_discussions}.timemodified
            LEFT JOIN {logstore_standard_log} AS last_login ON last_login.userid = :teacher_id2 AND last_login.target = "loggedin"
            WHERE {course}.id = :course_id LIMIT 1;
        ', array(
            'teacher_id1' => $teacher_id,
            'teacher_id2' => $teacher_id,
            'course_id' => $id
        ));

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
                if ($assignment->submission_count != 0) return $assignment;
            }, $assignment_ids));
        }

        return new course($id, $course_row->name, $course_row->last_login_date, $discussions, $students, $assignments);
    }

    /**
     * @param $on_sort callable($student1, $student2) A function that returns an int based on how any two students should be sorted
     * @return array(student)
     */
    function sort_students_by_score() {
        $students = $this->students;
        usort($students, function($student1, $student2) {
            // Sort by Score
            $difference = $student1->score_percentage - $student2->score_percentage;
            // If the Scores are the same sort by name
            if ($difference == 0) $difference = intval($student1->name) - intval($student2->name);
            return $difference;
        });

        // We need to arrayify each student object so twig can use it
        return array_map(function($student) {
            return (array) $student;
        }, $students);
    }

    function get_link() {
        global $CFG;
        return $CFG->wwwroot . '/course/view.php?id=' . $this->id;
    }
}