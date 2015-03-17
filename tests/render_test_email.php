<?php

namespace teacher;

error_reporting(E_ALL);
ini_set('display_errors', 1);// Must be localhost to view this

if (!in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1')))
    die('Must view from localhost');

define('WEB_CRON_EMULATED_CLI', 'defined'); // ugly ugly hack, do not use elsewhere please
define('NO_OUTPUT_BUFFERING', true);

require_once(__DIR__ . '/../template_renderer.php');
require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../teacher/teacher.php');

$teachers = teacher::get_all();

if (sizeof($teachers) == 0) {
    echo '
    <style>body{text-align: center; font-size: 20px;}</style>
    <h4>You need at least 1 subscribed teacher to view the test email!</h4>
    <a target="_blank" href="https://github.com/Arubaruba/moodle_reminders#how-to-subscribe-all-teachers">Github README: How to Subscribe <b>ALL</b> Teachers</a>
    ';
} else {
    $data = (array)array_values($teachers)[0];
    // Don't use caching
    $renderer = new \template_renderer(false);
    echo $renderer->render('teacher_email.twig', 'teacher_email.css', $data);
}

