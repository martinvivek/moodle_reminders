<?php

require_once(__DIR__ . '/../classes/factory/teacher_factory.php');


/**
 * @return float Microtime to be passed to end_timer function
 */
function start_timer() {
    return microtime(true);
}

/**
 * @param $start_time float Value returned by start_timer
 * @return float Seconds since start_timer
 */
function end_timer($start_time) {
    return (microtime(true) - $start_time) * 1000;
}

$t = start_timer();

$teacher_factory = new teacher_factory();
$teachers = $teacher_factory->load_records('teacher.sql');

echo end_timer($t) . ' ms';
