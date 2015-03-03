<?php

namespace teacher;

require_once(__DIR__ . '/../moodle_environment.php');

/**
 * A submission that has not yet been graded
 * @package teacher
 */
class submission {
    /**
     * @var $id integer
     * @var $student_name string
     * @var $time_created \DateTime
     */
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
            SELECT CONCAT_WS(" ", {user}.firstname, {user}.lastname) AS student_name,
              FROM_UNIXTIME({assign_submission}.timecreated) AS time_created FROM {assign_submission}
            LEFT JOIN {user} ON {user}.id = {assign_submission}.userid
            WHERE {assign_submission}.id = ? AND {assign_submission}.latest = 1 LIMIT 1
        ', array($id));

        return new submission($id, $submission_row->student_name, $submission_row->time_created);
    }
}