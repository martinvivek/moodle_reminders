<?php

namespace teacher;

require_once(__DIR__ . '/../moodle_environment.php');
require_once(__DIR__ . '/course.php');

/**
 * Stores basic teacher data, loads teachers from the moodle database, sends teachers emails
 * @package teacher
 */
class teacher {
    /**
     * @var $id integer
     * @var $email string
     * @var $courses array(course)
     */
    public $id, $email, $courses;

    function __construct($id, $email, $courses = array()) {
        $this->id = $id;
        $this->email = $email;
        $this->courses = $courses;
    }

    /**
     * @return array(teacher) users who are teaching active courses from the moodle database
     */

    static function get_all() {
        global $DB;
        $teacher_rows = $DB->get_records_sql('
          SELECT {user}.id, email, GROUP_CONCAT(DISTINCT {course}.id ORDER BY {course}.fullname) as course_ids FROM {user}
          LEFT JOIN {role_assignments} ON (roleid = 3 OR roleid = 4) AND {role_assignments}.userid = {user}.id
          LEFT JOIN {context} ON contextlevel = 50 AND {context}.id = {role_assignments}.contextid
          LEFT JOIN {course} ON {course}.id = {context}.instanceid AND {course}.visible = 1 AND {course}.format != "site"
          WHERE {course}.id IS NOT NULL
          GROUP BY {user}.id
        ');

        return array_map(function ($teacher_row) {
            $course_ids = explode(',', $teacher_row->course_ids);

            $courses = array_map(function ($course_id) {
                return course::get(intval($course_id));
            }, $course_ids);
            return new teacher($teacher_row->id, $teacher_row->email, $courses);
        }, $teacher_rows);
    }
}