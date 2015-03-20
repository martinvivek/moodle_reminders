<?php

namespace teacher;

require_once(__DIR__ . '/../../../config.php');

// Average Occurrences per week of actions that give points
// see file: tests/event_analysis.php
$average_weekly_action_occurrences = array(
//    'created' => 0.18,
    'submitted' => 2,
    'viewed' => 10,
);

// Calculate action rewards
$cases = array_map(function($action) use (&$average_weekly_action_occurrences) {
    $points =
        (1 / $average_weekly_action_occurrences[$action])
        / sizeof($average_weekly_action_occurrences)
        // Weekly occurrences represent the average student, who should have a medium score
    ;
    return "WHEN \"" . $action . "\" THEN ".$points;
}, array_keys($average_weekly_action_occurrences));
define('ACTION_REWARD_CASES', implode("\n", $cases));

define('STAR_CHARACTER', '★');
/**
 * Represents a student instance within a course environment and at a certain point in time
 * @package teacher
 */
class student {
    /**
     * @var $id integer
     * @var $course_id integer
     * @var $name string
     * @var $score_percentage number Percentage of the average student's score (100% = 1)
     */
    public $id, $name, $email, $last_login, $score_percentage;

    /**
     * @return string This student's score as a string of stars
     */
    function get_stars() {
        // Halve the score so the average student would have 2.5 stars
        // A score of 100% (1) should have 5 stars
        $number_of_stars = min($this->score_percentage / 2, 1) * 5;
        $stars = '';
        for ($i = 0; $i < $number_of_stars; $i++) {
            $stars .= STAR_CHARACTER;
        }
        return $stars;
    }

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
                      '. ACTION_REWARD_CASES.'
                    ELSE 0 END)
                  /* Has the same effect of only getting distinct rows */
                  * COUNT(DISTINCT {logstore_standard_log}.id) / COUNT({logstore_standard_log}.id) /
                  /* We want the score per week */
                  (DATEDIFF(NOW(), FROM_UNIXTIME({course}.startdate)) / 7)
               AS score
            FROM {user}
            LEFT JOIN {course} ON {course}.id = :course_id
            LEFT JOIN {logstore_standard_log} ON {logstore_standard_log}.courseid = {course}.id AND {logstore_standard_log}.userid = {user}.id
            WHERE {user}.id = :user_id LIMIT 1;
        ', array(
                'user_id' => $id,
                'course_id' => $course_id
            )
        );
        return new student($id, $student_row->name, $student_row->email, $student_row->last_login,abs($student_row->score));
    }
}