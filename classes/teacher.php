<?php

namespace local_moodle_reminders;

require_once(__DIR__ . '/../../../config.php');

global $CFG;
define('UNSUBSCRIBE_LINK', $CFG->wwwroot . '/local/moodle_reminders/unsubscribe.php');
define('BROWSER_VIEW_LINK', $CFG->wwwroot . '/local/moodle_reminders/web_view.php');

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
    public $browser_view_link = BROWSER_VIEW_LINK;

    function __construct($id, $email, $last_login, $courses = null) {
        $this->id = $id;
        $this->email = $email;
        $this->last_login = $last_login;
        $this->courses = $courses;
    }
}