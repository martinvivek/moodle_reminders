<?php

namespace local_moodle_reminders;

require_once(__DIR__ . '/../redis_logstore.php');
require_once(__DIR__ . '/factory.php');
require_once(__DIR__ . '/../student.php');

class student_factory extends factory {
    /**
     * @var array Maps the name of an action (see mdl_logstore_standard_log.action) to the number of times a week a good student should take that action
     */
    private $weekly_action_targets = array(
        'viewed' => 15
    );

    protected function construct_record($row, $load_dependencies) {

        global $redisDB;

        // Get the average number of a certain action type per week then divide by the weekly target
        $current_time = new \DateTime();
        $course_creation_time = new \DateTime();
        $course_creation_time->setTimestamp($row->course_timecreated);
        $weeks_since_course_was_created = $current_time->diff($course_creation_time)->days / 7;

        $action_scores = array();
        foreach ($this->weekly_action_targets as $action_name => $target_action_count) {
            $key = redis_logstore::get_event_key($action_name, $row->id, $row->course_id);
            $action_scores[$action_name] = $redisDB->zcard($key) / $weeks_since_course_was_created / $target_action_count;
        }

        // The total score is the average of the scores of all action types
        $total_score = array_sum($action_scores) / count($action_scores);

        // Get the last time when the student accessed the course
        $last_view_query = $redisDB->zrevrangebyscore(redis_logstore::get_event_key('viewed', $row->id, $row->course_id),
            '+inf', '-inf', array('WITHSCORES' => true, 'LIMIT' => array(0, 1)));
        $last_view_time = array_values($last_view_query)[0];

        return new student($row->id, $row->name, $row->email, $last_view_time, $total_score);
    }
}