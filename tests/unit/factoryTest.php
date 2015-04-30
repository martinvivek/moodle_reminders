<?php

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/timer.php');
require_once(__DIR__ . '/../../classes/factory/teacher_factory.php');

/**
 * IMPORTANT !!!
 *
 * This script WILL NOT WORK if another test that uses the database is run
 */

class factoryTest extends PHPUnit_Framework_TestCase {

    protected $maximum_query_time = 20; # Milliseconds
    protected $teacher_id = 5, $course_id = 11;

    public function test_factories() {
//        $this->factory_test_helper(new teacher_factory(), 'teacher.sql');
        $this->factory_test_helper(new course_factory(), 'course.sql', array('teacher_id' => $this->teacher_id));
        $this->factory_test_helper(new discussion_factory(), 'discussion.sql', array('teacher_id' => $this->teacher_id, 'course_id' => $this->course_id), array('action_cases' => (new student_factory())->get_action_cases_sql()));
        $this->factory_test_helper(new student_factory(), 'student.sql', array('teacher_id' => $this->teacher_id, 'course_id' => $this->course_id), array('action_cases' => (new student_factory())->get_action_cases_sql()));
    }

    /**
     * @param $factory Factory Class that extends the factory class
     * @param $sql_file String Passed directly to factory->load_records
     * @param $vars Array Passed directly to factory->load_records
     * @param $unescaped_vars Array Passed directly to factory->load_records
     * @return Array Records returned by load_records(... , $constructor = false)
     */
    public function factory_test_helper($factory, $sql_file, $vars = array(), $unescaped_vars = array()) {
        $timer = new timer();

        $timer->start();

        $records = $factory->load_records($sql_file, $vars, $unescaped_vars, false);

        $this->assertNotEmpty($records);

        $assert_time_message = 'Factory "' . get_class($factory) . '" took ' . $timer->time_passed() . ' milliseconds to load records';
        $this->assertLessThan($this->maximum_query_time, $timer->time_passed(), $assert_time_message);
    }
}
