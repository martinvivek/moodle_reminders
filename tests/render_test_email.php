<?php

namespace teacher;

define('WEB_CRON_EMULATED_CLI', 'defined'); // ugly ugly hack, do not use elsewhere please
define('NO_OUTPUT_BUFFERING', true);

require_once(__DIR__ . '/../template_renderer.php');
require_once(__DIR__ . '/../teacher/teacher.php');

$teachers = teacher::get_all();

// Don't use caching
$renderer = new \template_renderer(false);

$data = (array) array_values($teachers)[0];

echo $renderer->render('teacher_email.twig', $data);

