<?php


namespace teacher;

require_once(__DIR__ . '/../moodle_environment.php');
require_once(__DIR__ . '/../linkable.php');
require_once(__DIR__ . '/submission.php');

/**
 * @package teacher
 */
class assignment extends \linkable {
    /**
     * @var $id string
     * @var $module_instance_id integer Used in the _get_link()_ method
     * @var $name string
     * @var $time_due /Date
     * @var $submission_count int
     */
    public $id, $module_instance_id, $name, $time_due, $submission_count;

    function __construct($id, $module_instance_id, $name, $time_due, $submission_count) {
        $this->id = $id;
        $this->module_instance_id = $module_instance_id;
        $this->name = $name;
        $this->time_due = $time_due;
        $this->submission_count = $submission_count;
    }

    /**
     * Loads the assignment from the moodle database and then loads the __ungraded__ submissions associated with it
     * @param $id
     * @return assignment
     */
    static function get($id) {
        global $DB;
        $assignment_row = $DB->get_record_sql('
          SELECT {assign}.name, COUNT(DISTINCT CASE WHEN {assign_grades}.id IS NOT NULL THEN NULL ELSE {assign_submission}.id END) AS submission_count, FROM_UNIXTIME({assign}.duedate) AS time_due, {course_modules}.id AS module_instance_id FROM {assign}
          LEFT JOIN {assign_submission} ON {assign_submission}.assignment = {assign}.id AND {assign_submission}.status = "submitted"
          LEFT JOIN {assign_grades} ON {assign_grades}.assignment = {assign}.id AND {assign_grades}.userid = {assign_submission}.userid AND {assign_grades}.attemptnumber >= {assign_submission}.attemptnumber
          LEFT JOIN {course_modules} ON {course_modules}.module = 1 AND {course_modules}.instance = {assign}.id
          WHERE {assign}.id = ? LIMIT 1;', array($id));

        return new assignment($id, $assignment_row->module_instance_id, $assignment_row->name, $assignment_row->time_due, $assignment_row->submission_count);
    }

    /**
     * @return string A link to this object's page in moodle; Note that a login may be required
     */
    function get_link() {
        global $CFG;
        return $CFG->wwwroot . '/mod/assign/view.php?id=' . $this->module_instance_id . '&action=grading';
    }
}
