<?php

//require_once(__DIR__ . '/TeacherReminders.php');
//
//new TeacherReminders();

require_once(__DIR__ . '/teacher/teacher.php');

$a = \teacher\teacher::get_all();

echo 'ok';
