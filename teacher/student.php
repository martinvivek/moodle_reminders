<?php


namespace teacher;

require_once(__DIR__ . '/../moodle_environment.php');
require_once(__DIR__ . '/../linkable.php');

// How each of these events affect the students' activity score
define('POINTS_FOR_LOGIN', 10);
define('POINTS_FOR_CREATE', 20);
define('POINTS_FOR_VIEW', 3);

/**
 * Represents a student instance within a course environment and at a certain point in time
 * @package teacher
 */
class student extends \linkable {
    /**
     * @var $id integer
     * @var $course_id integer
     * @var $name string
     * @var $score_this_week integer Number of times student has logged in during the last seven days
     * @var $score_course_average integer Number of times the student has logged in since enrolled in the course with id $course_id
     */
    public $id, $name, $score_course_average, $score_this_week;

    function __construct($id, $name, $score_this_week, $score_course_average) {
        $this->id = $id;
        $this->name = $name;
        $this->score_this_week = $score_this_week;
        $this->score_course_average = $score_course_average;
    }

    /**
     * @param $id
     * @param $course_id
     * @return student Load student from database
     */
    static function get($id, $course_id) {
        global $DB;
        $student_row = $DB->get_record_sql('
            SELECT CONCAT_WS(" ", {user}.firstname, {user}.lastname) AS name,
              SUM(
                CASE score_this_week.action
                 WHEN "create" THEN :points_for_create1
                 WHEN "loggedin" THEN :points_for_login1
                 WHEN "view" THEN :points_for_view1
                ELSE 0 END)
              /* Has the same effect of only getting distinct rows */
              * COUNT(DISTINCT score_this_week.id) / COUNT(score_this_week.id) AS score_this_week_count,

              CEIL(
                  SUM(
                    CASE score_course_average.action
                     WHEN "create" THEN :points_for_create2
                     WHEN "loggedin" THEN :points_for_login2
                     WHEN "view" THEN :points_for_view2
                    ELSE 0 END)
                  /* Has the same effect of only getting distinct rows */
                  * COUNT(DISTINCT score_course_average.id) / COUNT(score_course_average.id) /
                  /* Divide by number of weeks */
                  CEIL(DATEDIFF(NOW(), FROM_UNIXTIME({course}.startdate)) / 7)
              )
               AS score_course_average_count
            FROM {user}
            LEFT JOIN {course} ON {course}.id = :course_id

            LEFT JOIN {logstore_standard_log} AS score_this_week ON score_this_week.userid = {user}.id AND FROM_UNIXTIME(score_this_week.timecreated) > NOW() - INTERVAL 1 WEEK
            LEFT JOIN {logstore_standard_log} AS score_course_average ON score_course_average.userid = {user}.id  AND FROM_UNIXTIME(score_course_average.timecreated) > FROM_UNIXTIME({course}.startdate)

            WHERE {user}.id = :user_id LIMIT 1;
        ', array(
                'user_id' => $id,
                'course_id' => $course_id,
                'points_for_create1' => POINTS_FOR_CREATE,
                'points_for_login1' => POINTS_FOR_LOGIN,
                'points_for_view1' => POINTS_FOR_VIEW,
                'points_for_create2' => POINTS_FOR_CREATE,
                'points_for_login2' => POINTS_FOR_LOGIN,
                'points_for_view2' => POINTS_FOR_VIEW,
            )
        );
        return new student($id, $student_row->name, intval($student_row->score_this_week_count), intval($student_row->score_course_average_count));
    }

    /**
     * @return string A link to this object's page in moodle; Note that a login may be required
     */
    function get_link() {
        global $CFG;
        return $CFG->wwwroot . '/user/profile.php?id=' . $this->id;
    }
}