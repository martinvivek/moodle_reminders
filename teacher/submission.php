<?php

namespace teacher;

require_once(__DIR__ . '/../moodle_environment.php');

class submission {
    public $id, $student_name, $time_created;

    function __construct($id, $student_name, $time_created) {
        $this->id = $id;
        $this->student_name = $student_name;
        $this->time_created = $time_created;
    }

    /**
     * @param $id integer
     * @return submission
     */
    static function get($id) {
        global $DB;
        $submission_row = $DB->get_record_sql('
            SELECT CONCAT_WS(" ", mdl_user.firstname, mdl_user.lastname) AS student_name,
              FROM_UNIXTIME({assign_submission}.timecreated) AS {assign_submission}.time_created FROM {assign_submission}
            LEFT JOIN {user} ON {user}.id = {assign_submission}.userid
            WHERE {assign_submission}.id = ? AND {assign_submission}.lastest = 1
            ORDER BY time_created ASC
        ', array($id));

        return new submission($id, $submission_row->student_name, $submission_row->time_created);
    }
}