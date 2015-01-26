<?php


namespace teacher;

require_once(__DIR__ . '/../moodle_environment.php');
require_once(__DIR__ . '/../linkable.php');

/**
 * Represents a student instance within a course environment and at a certain point in time
 * @package teacher
 */
class student extends \linkable {
    /**
     * @var $id integer
     * @var $course_id integer
     * @var $name string
     * @var $logins_course_total integer Number of times the student has logged in since enrolled in the course with id $course_id
     * @var $logins_this_week integer Number of times student has logged in during the last seven days
     */
    public $id, $name, $logins_course_total, $logins_this_week;

    function __construct($id, $name, $logins_course_total, $logins_this_week) {
        $this->id = $id;
        $this->name = $name;
        $this->logins_course_total = $logins_course_total;
        $this->logins_this_week = $logins_this_week;
    }

    /**
     * @param $id
     * @param $course_id
     * @return student Load student from database
     */
    static function get($id, $course_id) {
        global $DB;
        $student_row = $DB->get_record_sql('
            SELECT CONCAT_WS(" ", {user}.firstname, {user}.lastname) AS name, COUNT(DISTINCT logins_this_week.id) AS logins_this_week_count,
              COUNT(DISTINCT logins_course_total.id) AS logins_course_total_count FROM {user}
            LEFT JOIN {course} ON {course}.id = :course_id
            LEFT JOIN {logstore_standard_log} AS logins_this_week ON logins_this_week.userid = {user}.id AND
              logins_this_week.action = "loggedin" AND FROM_UNIXTIME(logins_this_week.timecreated) > NOW() - INTERVAL 1 WEEK
            LEFT JOIN {logstore_standard_log} AS logins_course_total ON logins_course_total.userid = {user}.id AND
              logins_course_total.action = "loggedin" AND FROM_UNIXTIME(logins_course_total.timecreated) > FROM_UNIXTIME({course}.startdate)
            WHERE {user}.id = :user_id LIMIT 1;
        ', array('user_id' => $id, 'course_id' => $course_id));
        return new student($id, $student_row->name, intval($student_row->logins_this_week_count), intval($student_row->logins_course_total_count));
    }

    /**
     * @return string A link to this object's page in moodle; Note that a login may be required
     */
    function get_link() {
        global $CFG;
        return $CFG->wwwroot . '/user/profile.php?id=' . $this->id;
    }
}