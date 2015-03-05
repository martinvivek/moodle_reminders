<?php


namespace teacher;

require_once(__DIR__ . '/../moodle_environment.php');

// How each of these events affect the students' activity score
define('POINTS_FOR_CREATE', 7);
define('POINTS_FOR_VIEW', 1);
define('WEEKLY_TARGET', 20);

/**
 * Represents a student instance within a course environment and at a certain point in time
 * @package teacher
 */
class student {
    /**
     * @var $id integer
     * @var $course_id integer
     * @var $name string
     * @var $score_percentage number A student's score divided by their weekly target
     */
    public $id, $name, $email, $last_login, $score_percentage;

    function __construct($id, $name, $email, $last_login, $score_percentage) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->last_login;
        $this->score_percentage = $score_percentage;
    }

    /**
     * @param $id
     * @param $course_id
     * @return student Load student from database
     */
    static function get($id, $course_id) {
        global $DB;
        $student_row = $DB->get_record_sql('
            SELECT CONCAT_WS(" ", {user}.firstname, {user}.lastname) AS name, {user}.email AS email, FROM_UNIXTIME({user}.lastlogin) AS last_login,
                  SUM(
                    CASE {logstore_standard_log}.action
                     WHEN "created" THEN :points_for_create
                     WHEN "viewed" THEN :points_for_view
                    ELSE 0 END)
                  /* Has the same effect of only getting distinct rows */
                  * COUNT(DISTINCT {logstore_standard_log}.id) / COUNT({logstore_standard_log}.id) /
                  /* We want the score per week */
                  DATEDIFF(NOW(), FROM_UNIXTIME({course}.startdate)) / 7
               AS score
            FROM {user}
            LEFT JOIN {course} ON {course}.id = :course_id
            LEFT JOIN {logstore_standard_log} ON {logstore_standard_log}.courseid = {course}.id AND {logstore_standard_log}.userid = {user}.id
            WHERE {user}.id = :user_id LIMIT 1;
        ', array(
                'user_id' => $id,
                'course_id' => $course_id,
                'points_for_create' => POINTS_FOR_CREATE,
                'points_for_view' => POINTS_FOR_VIEW,
            )
        );
        return new student($id, $student_row->name, $student_row->email, $student_row->last_login, $student_row->score / WEEKLY_TARGET);
    }
}