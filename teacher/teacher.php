<?php

namespace teacher;

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/course.php');

global $CFG;

define('UNSUBSCRIBE_LINK', $CFG->wwwroot . '/local/moodle_reminders/unsubscribe.php');

/**
 * Stores basic teacher data, loads teachers from the moodle database
 * @package teacher
 */
class teacher {
    /**
     * @var $id integer
     * @var $email string
     * @var $courses array(course)
     * @var $last_login /Date
     * @var $unsubscribe_link string Complete moodle url to message settings page
     */
    public $id, $email, $courses;
    public $unsubscribe_link = UNSUBSCRIBE_LINK;

    function __construct($id, $email, $last_login, $courses = array()) {
        $this->id = $id;
        $this->email = $email;
        $this->last_login = $last_login;
        $this->courses = $courses;
    }

    /**
     * @return array(teacher) users who are teaching active courses from the moodle database
     */

    static function get_all() {
        global $DB;
        $teacher_rows = $DB->get_records_sql('
          SELECT {user}.id, email, GROUP_CONCAT(DISTINCT {course}.id ORDER BY {course}.fullname) as course_ids, FROM_UNIXTIME({user}.lastaccess) AS last_login FROM {user}
          LEFT JOIN {role_assignments} ON (roleid = 3 OR roleid = 4) AND {role_assignments}.userid = {user}.id
          LEFT JOIN {context} ON contextlevel = 50 AND {context}.id = {role_assignments}.contextid
          LEFT JOIN {course} ON {course}.id = {context}.instanceid AND {course}.visible = 1 AND {course}.format != "site"
          LEFT JOIN {user_preferences} ON {user_preferences}.userid = {user}.id AND {user_preferences}.name = "message_provider_local_moodle_reminders_course_reports_loggedoff"
          WHERE {course}.id IS NOT NULL AND {user_preferences}.value LIKE "email"
          GROUP BY {user}.id
        ');

        return array_map(function ($teacher_row) {
            // We don't need to check if the course_ids are null because someone doesn't have
            // active courses they are not a teacher
            $teacher_id = $teacher_row->id;
            $course_ids = explode(',', $teacher_row->course_ids);
            $courses = array_map(function ($course_id) use ($teacher_id) {
                return course::get(intval($course_id), $teacher_id);
            }, $course_ids);
            return new teacher($teacher_row->id, $teacher_row->email, $teacher_row->last_login, $courses);
        }, $teacher_rows);
    }
}