<?php

require_once(__DIR__ . '/../../../config.php');

class discussion {
    /**
     * @var $id integer
     * @var $name string
     * @var $post_count integer
     */
    public $id, $name, $post_count;

    function __construct($id, $name, $post_count) {
        $this->id = $id;
        $this->name = $name;
        $this->post_count = $post_count;
    }

    /**
     * @return string A link to this object's page in moodle; Note that a login may be required
     */
    function get_link() {
        global $CFG;
        return $CFG->wwwroot . '/mod/forum/discuss.php?d=' . $this->id;
    }
}