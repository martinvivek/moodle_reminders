<?php

require_once(__DIR__ .'/factory.php');
require_once(__DIR__ .'/../student.php');

class student_factory extends factory {
    /**
     * @var array Maps the name of an action (see mdl_logstore_standard_log.action) to the number of times a week a good student should take that action
     */
    private $weekly_action_targets = array(
        'viewed' => 15
    );

    /**
     * The formula for converting weekly action targets to points rewarded per action event
     * @param $weekly_action_target
     * @param $action_type_count
     * @return float
     */
    private function calculate_action_points($weekly_action_target, $action_type_count) {
        return 1 / $weekly_action_target / $action_type_count;
    }

    /**
     * @return string SQL covering cases of rewarded actions and specifying the amount rewarded
     */
    public function get_action_cases_sql() {
        $sql = '';
        foreach ($this->weekly_action_targets as $action => $weekly_target) {
            $points = $this->calculate_action_points($weekly_target, count($this->weekly_action_targets));
            $sql .= 'WHEN \'' . filter_var($action, FILTER_SANITIZE_STRING) . '\' THEN ' .
                filter_var($points, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) . "\n";
        }
        return $sql;
    }

    protected function construct_record($row, $load_dependencies) {
        return new student($row->id, $row->name, $row->email, $row->last_access_date, $row->score);
    }
}