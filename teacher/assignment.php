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

    /**
     * Loads the assignment from the moodle database and then loads the __ungraded__ submissions associated with it
     * @param $id
     */
    static function get($id) {
        global $DB;
        // TODO get course_module_instance
        $assignment_row = $DB->get_record_sql('
            SELECT CONCAT_WS(" ", mdl_user.firstname, mdl_user.lastname) AS student_name, {course_modules}.id AS module_instance_id,
              {assign}.name, GROUP_CONCAT(DISTINCT {assign_submission}.id ORDER BY {assign_submission}.timecreated ASC) AS submission_ids FROM {assign}
            LEFT JOIN {course_modules} ON {course_modules}.module = 1 AND {course_modules}.instance = {assign}.id
            LEFT JOIN {assign_submission} ON {assign}.id = {assign_submission}.assignment
            WHERE {assign_submission}.assignment = ? AND {assign_submission}.lastest = 1
        ', array($id));

        $submission_ids = explode(',', $assignment_row->submission_ids, -1);
        $submissions = array_map(function($submission_id) {
            return submission::get($submission_id);
        }, $submission_ids);

        return new assignment($id, $assignment_row->module_instance_id, $assignment_row->name, $submissions);
    }

    /**
     * @return string A link to this object's page in moodle; Note that a login may be required
     */
    function get_link() {
        global $CFG;
        return $CFG->wwwroot . '/user/profile.php?id=' . $this->id;
    }
}