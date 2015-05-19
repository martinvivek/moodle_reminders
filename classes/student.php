<?php

namespace local_moodle_reminders;

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

    /**
     * @var String $score_indication_char This character is repeated to create a progress bar
     * @var int $max_score_chars The number of indication characters a student with a 100% score has
     */
    private static $max_score_chars = 8;

    function __construct($id, $name, $email, $last_accessed, $score) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->last_accessed = $last_accessed;
        # Score must be between 0 and 1
        $this->score = max(min($score, 1), 0);
    }

    /**
     * @return string Progress bar composed of block characters
     */
    function get_score_string() {
        $score_string = '';
        $char_count = $this->score * student::$max_score_chars;
        for ($i = 0; $i < floor($char_count); $i++) {
            $score_string .= '█';
        }
        if ($char_count - floor($char_count) >= 0.5) $score_string .= '▌';

        // empty spaces
        for ($i = 0; $i < student::$max_score_chars - floor($char_count + 0.5); $i++) {
            $score_string .= '&#x2004';
        }
        return $score_string;
    }
}