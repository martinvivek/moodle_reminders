<?php


namespace teacher;

require_once(__DIR__ . '/../moodle_environment.php');
require_once(__DIR__ . '/../linkable.php');

/**
 * A moodle forum discussion
 * @package teacher
 */
class discussion extends \linkable {
    /**
     * @var $id integer
     * @var $name string
     * @var $post_count integer
     * @var $time_created \DateTime
     */
    public $id, $name, $post_count, $time_created;

    function __construct($id, $name, $post_count, $time_created) {
        $this->id = $id;
        $this->name = $name;
        $this->post_count = $post_count;
        $this->time_created = $time_created;
    }

    /**
     * @param $id integer
     * @return discussion
     */
    static function get($id) {
        global $DB;
        $discussion_row = $DB->get_record_sql('
            SELECT {forum_discussions}.name, COUNT(DISTINCT {forum_posts}.id) AS post_count, FROM_UNIXTIME(timemodified) AS time_created FROM {forum_discussions}
            LEFT JOIN {forum_posts} ON {forum_posts}.discussion = {forum_discussions}.id
            WHERE {forum_discussions}.id = ?;
        ', array($id));
        return new discussion($id, $discussion_row->name, $discussion_row->post_count, $discussion_row->time_created);
    }

    /**
     * @return string A link to this object's page in moodle; Note that a login may be required
     */
    function get_link() {
        global $CFG;
        return $CFG->wwwroot . '/mod/forum/discuss.php?d=' . $this->id;
    }
}