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
    public static $star_character = '★';

    /**
     * @return string This student's score as a string of stars
     */
    function get_stars() {
        // A score of 100% (1) should have 5 stars
        $number_of_stars = min($this->score, 1) * 5;
        $stars = '';
        for ($i = 0; $i < $number_of_stars; $i++) {
            $stars .= student::$star_character;
        }
        return $stars;
    }

    function __construct($id, $name, $email, $last_accessed, $score) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->last_accessed = $last_accessed;
        $this->score = $score;
    }
}