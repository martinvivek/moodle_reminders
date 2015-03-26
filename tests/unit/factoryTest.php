<?php

if(!defined('CLI_SCRIPT')) define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../classes/teacher_factory.php');

class factoryTest extends PHPUnit_Framework_TestCase {
    public function test_load_teachers() {
        $teacher_factory = new teacher_factory();
        $a = $teacher_factory->load_records('teacher.sql');
        $b = 1;
    }
}
