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
     * @var $submissions array(submission)
     */
    public $id, $module_instance_id, $name, $submissions;

    function __construct($id, $module_instance_id, $name, $submissions) {
        $this->id = $id;
        $this->module_instance_id = $module_instance_id;
        $this->name = $name;
        $this->submissions = $submissions;
    }

    /**
     * Loads the assignment from the moodle database and then loads the __ungraded__ submissions associated with it
     * @param $id
     * @return assignment
     */
    static function get($id) {
        global $DB;
        $assignment_row = $DB->get_record_sql('
          SELECT {assign}.name, GROUP_CONCAT(DISTINCT CASE WHEN {assign_grades}.id IS NOT NULL THEN NULL ELSE {assign_submission}.id END) AS submission_ids, {course_modules}.id AS module_instance_id FROM {assign}
          LEFT JOIN {assign_submission} ON {assign_submission}.assignment = {assign}.id AND {assign_submission}.status = "submitted"
          LEFT JOIN {assign_grades} ON {assign_grades}.assignment = {assign}.id AND {assign_grades}.userid = {assign_submission}.userid AND {assign_grades}.attemptnumber >= {assign_submission}.attemptnumber
          LEFT JOIN {course_modules} ON {course_modules}.module = 1 AND {course_modules}.instance = {assign}.id
          WHERE {assign}.id = ? LIMIT 1;', array($id));

        $submissions = null;
        if ($assignment_row->submission_ids) {
            $submission_ids = explode(',', $assignment_row->submission_ids);
            $submissions = array_map(function ($submission_id) {
                return submission::get(intval($submission_id));
            }, $submission_ids);
        }

        return new assignment($id, $assignment_row->module_instance_id, $assignment_row->name, $submissions);
    }

    /**
     * @return string A link to this object's page in moodle; Note that a login may be required
     */
    function get_link() {
        global $CFG;
        return $CFG->wwwroot . '/mod/assign/view.php?id=' . $this->module_instance_id;
    }
}
