<?php

require_once(__DIR__ . '/../../../config.php');

/**
 * Stores information about a student which is helpful for teachers
 * such as their name and course performance
 */
class student {
    /**
     * @var $id integer
     * @var $course_id integer
     * @var $name string
     * @var $score float Indicates how active a student has been
     */
    public $id, $name, $email, $last_accessed, $score;

    function __construct($id, $name, $email, $last_accessed, $score) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->last_accessed = $last_accessed;
        # Score must be between 0 and 1
        $this->score = max(min($score, 1), 0);
    }
}