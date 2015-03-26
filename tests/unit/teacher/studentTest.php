<?php

require_once(__DIR__ . '/../../../classes/student.php');

class studentTest extends PHPUnit_Framework_TestCase {
    // Make sure that the correct number of stars are awarded for a student's score
    public function test_get_stars() {
        $student = new student(0, '', '', 0, 0);

        // The edge case where a student has done
        // NOTHING in the course results in no stars
        $student->score = 0;
        $this->assertEquals(0, strlen(utf8_decode($student->get_stars())));

        // If a student has done anything in the course
        // they automatically get at least one star
        $student->score = 0.00001;
        $this->assertEquals(1, strlen(utf8_decode($student->get_stars())));

        // The score percentage should be rounded up (ceil)
        $student->score = 0.5;
        $this->assertEquals(3, strlen(utf8_decode($student->get_stars())));

        // A student with the target score should get the
        // full number of stars
        $student->score = 1;
        $this->assertEquals(5, strlen(utf8_decode($student->get_stars())));

        // A student with a score above the target should
        // still only get 5 stars
        $student->score = 1.4;
        $this->assertEquals(5, strlen(utf8_decode($student->get_stars())));
    }
}
