<?php

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../../teacher/student.php');

class studentTest extends PHPUnit_Framework_TestCase {
    // Make sure that the correct number of stars are awarded for a student's score
    public function test_get_stars() {
        $student = new \teacher\student(0, '', '', 0, 0);

        // The edge case where a student has done
        // NOTHING in the course results in no stars
        $student->score_percentage = 0;
        $this->assertEquals(0, strlen(utf8_decode($student->get_stars())));

        // If a student has done anything in the course
        // they automatically get at least one star
        $student->score_percentage = 0.00001;
        $this->assertEquals(1, strlen(utf8_decode($student->get_stars())));

        // The score percentage should be rounded up (ceil)
        $student->score_percentage = 0.5;
        $this->assertEquals(3, strlen(utf8_decode($student->get_stars())));

        // A student with the target score should get the
        // full number of stars
        $student->score_percentage = 1;
        $this->assertEquals(5, strlen(utf8_decode($student->get_stars())));

        // A student with a score above the target should
        // still only get 5 stars
        $student->score_percentage = 1.4;
        $this->assertEquals(5, strlen(utf8_decode($student->get_stars())));
    }

    public function test_get_action_reward_sql() {
        $actions = array('action1' => 10, 'action2' => 3);

        $action_reward_sql = teacher\student::get_action_reward_sql($actions);

        preg_match_all('/[\.\d]+/', $action_reward_sql, $matches);

        // The even matches are match ids while the odd ones are the actual values
        $action1_reward = $matches[0][1];
        $action2_reward = $matches[0][3];

        $this->assertEquals(1 / $actions['action1'] / count($actions), $action1_reward);
        $this->assertEquals(1 / $actions['action2'] / count($actions), $action2_reward);

        // All of the points times their target occurrences should add up to 1
        $this->assertEquals(1, $action1_reward * $actions['action1'] + $action2_reward * $actions['action2']);
    }
}
