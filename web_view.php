<?php
/**
 * This script allows individuals to view their course report in
 * the browser instead of inside an email client
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/classes/factory/teacher_factory.php');
require_once(__DIR__ . '/template_renderer.php');

$url = new moodle_url('/local/moodle_reminders/web_view.php');
$PAGE->set_url($url);

require_login();

global $USER;

$teacher_factory = new teacher_factory();
$teachers = $teacher_factory->load_records('teacher_by_id.sql', array('teacher_id' => $USER->id));

$teacher = array_values($teachers)[0];
if (!$teacher->courses) {
// Set Renderer Options
    $PAGE->set_pagelayout('report'); // To add the sidebar
    $PAGE->set_title(get_string('pluginname', 'local_moodle_reminders'));
    $PAGE->set_heading(get_string('pluginname', 'local_moodle_reminders'));
    echo $OUTPUT->header();

    echo 'You are not currently teaching any active courses';

    echo $OUTPUT->footer();
} else {
    $teacher['web_view'] = true;
    $renderer = new template_renderer(false);
    echo $renderer->render('teacher_email.twig', 'teacher_email.css', (array) $teacher);
}