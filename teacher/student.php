<?php

namespace teacher;

require_once(__DIR__ . '/../../../config.php');

define('STAR_CHARACTER', '★');

// The optimal number of weekly occurrences of rewarded student actions
define('SUBMITTED_WEEKLY_TARGET', 2);
define('VIEWED_WEEKLY_TARGET', 10);

/**
 * Stores information about a student which is helpful for teachers
 * such as their name and course performance
 * @package teacher
 */
class student {
    /**
     * @var $id integer
     * @var $course_id integer
     * @var $name string
     * @var $score_percentage number Percentage of the average student's score (100% = 1)
     */
    public $id, $name, $email, $last_accessed, $score_percentage;

    /**
     * @param array(action => weekly_occurrence_count)
     * @return string SQL covering cases of rewarded actions and specifying the amount rewarded
     */
    static function get_action_reward_sql($target_action_occurrences) {
        $sql = '';
        foreach ($target_action_occurrences as $action => $occurrences) {
            $points = 1 / $occurrences / count($target_action_occurrences);
            $sql .='WHEN "' . $action . '" THEN ' . $points . "\n";
        }
        return $sql;
    }

    /**
     * @return string This student's score as a string of stars
     */
    function get_stars() {
        // A score of 100% (1) should have 5 stars
        $number_of_stars = min($this->score_percentage, 1) * 5;
        $stars = '';
        for ($i = 0; $i < $number_of_stars; $i++) {
            $stars .= STAR_CHARACTER;
        }
        return $stars;
    }

    function __construct($id, $name, $email, $last_accessed, $score_percentage) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->last_accessed = $last_accessed;
        $this->score_percentage = $score_percentage;
    }

    /**
     * @param $id
     * @param $course_id
     * @return student Load student from database
     */
    static function get($id, $course_id) {
        $target_action_occurrences = array(
            'submitted' => SUBMITTED_WEEKLY_TARGET,
            'viewed' => VIEWED_WEEKLY_TARGET
        );

        global $DB;
        $student_row = $DB->get_record_sql('
            SELECT CONCAT_WS(" ", {user}.firstname, {user}.lastname) AS name, {user}.email AS email, FROM_UNIXTIME(MAX(last_accessed.timecreated)) AS last_access_date,
                  SUM(
                    CASE {logstore_standard_log}.action
                      ' . student::get_action_reward_sql($target_action_occurrences) . '
                    ELSE 0 END)
                  /* Has the same effect of only getting distinct rows */
                  * COUNT(DISTINCT {logstore_standard_log}.id) / COUNT({logstore_standard_log}.id) /
                  /* We want the score per week */
                  (DATEDIFF(NOW(), FROM_UNIXTIME({course}.startdate)) / 7)
               AS score
            FROM {user}
            LEFT JOIN {course} ON {course}.id = :course_id
            LEFT JOIN {logstore_standard_log} ON {logstore_standard_log}.courseid = {course}.id AND {logstore_standard_log}.userid = {user}.id
            LEFT JOIN {logstore_standard_log} AS last_accessed ON last_accessed.userid = {user}.id AND last_accessed.courseid = {course}.id
            WHERE {user}.id = :user_id LIMIT 1;
        ', array(
                'user_id' => $id,
                'course_id' => $course_id
            )
        );
        $score = $student_row->score ? abs($student_row->score) : 0;
        return new student($id, $student_row->name, $student_row->email, $student_row->last_access_date, $score);
    }
}