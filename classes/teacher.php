<?php

require_once(__DIR__ . '/../../../config.php');

global $CFG;
define('UNSUBSCRIBE_LINK', $CFG->wwwroot . '/local/moodle_reminders/unsubscribe.php');

/**
 * Stores basic teacher data, loads teachers from the moodle database
 * @package teacher
 */
class teacher {
    /**
     * @var $id integer
     * @var $email string
     * @var $courses array(course)
     * @var $last_login /Date
     * @var $unsubscribe_link string Complete moodle url to message settings page
     */
    public $id, $email, $courses;
    public $unsubscribe_link = UNSUBSCRIBE_LINK;

    function __construct($id, $email, $last_login, $courses = array()) {
        $this->id = $id;
        $this->email = $email;
        $this->last_login = $last_login;
        $this->courses = $courses;
    }
}