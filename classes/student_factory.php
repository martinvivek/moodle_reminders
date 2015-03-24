<?php

require_once('factory.php');
require_once('student.php');

class student_factory extends factory {

    private $target_action_occurrences = array(
        'submitted' => 2,
        'viewed' => 10
    );

    /**
     * @param array (action => weekly_occurrence_count)
     * @return string SQL covering cases of rewarded actions and specifying the amount rewarded
     */
    function get_action_points() {
        $action_points = array();
        foreach ($this->target_action_occurrences as $action => $occurrences) {
            $action_points[$action] = 1 / $occurrences / count($this->target_action_occurrences);
        }
        return $action_points;
    }

    protected function construct_record($row) {
        return new student($row->name, $row->email, $row->last_access_date, $row->score);
    }
}