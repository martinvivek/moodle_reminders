<?php

namespace teacher;

if (!in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1')))
    die('Must view from localhost');

error_reporting(E_ALL);
ini_set('display_errors', 1);// Must be localhost to view this

require_once(__DIR__ . '/../template_renderer.php');
require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../teacher/teacher.php');

$teachers = teacher::get_all();

$renderer = new \template_renderer(false);
if (sizeof($teachers) == 0) {
    echo $renderer->twig->render('no_subscribed_teachers.twig');
} else {
    $data = (array)array_values($teachers)[0];
    // Don't use caching
    echo $renderer->render('teacher_email.twig', 'teacher_email.css', $data);
}

