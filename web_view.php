<?php
/**
 * This script allows individuals to view their course report in
 * the browser instead of inside an email client
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/teacher/teacher.php');
require_once(__DIR__ . '/template_renderer.php');

$url = new moodle_url('/local/moodle_reminders/web_view.php');
$PAGE->set_url($url);

require_login();

global $USER;

$teachers = teacher\teacher::get_all($USER->id);

if (sizeof($teachers) == 0) {
// Set Renderer Options
    $PAGE->set_pagelayout('report'); // To add the sidebar
    $PAGE->set_title(get_string('pluginname', 'local_moodle_reminders'));
    $PAGE->set_heading(get_string('pluginname', 'local_moodle_reminders'));
    echo $OUTPUT->header();

    echo get_string('not_teacher', 'local_moodle_reminders');

    echo $OUTPUT->footer();
} else {
    $data = (array)array_values($teachers)[0];
    $renderer = new template_renderer(false);
    echo $renderer->render('teacher_email.twig', 'teacher_email.css', $data);
}
