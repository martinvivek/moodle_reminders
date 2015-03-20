<?php

/**
 * This script allows individuals to view their course report in
 * the browser instead of inside an email client
 */

require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../teacher/teacher.php');
require_once(__DIR__ . '/../template_renderer.php');

$url = new moodle_url('/local/moodle_reminders/web_view.php');
$PAGE->set_url($url);

global $DB;
$teacher_ids = $DB->get_record_sql('
    SELECT {user}.id FROM {user}
    INNER JOIN {role_assignments} ON {role_assignments}.userid = {user}.id AND ({role_assignments}.roleid =  3 OR {role_assignments}.roleid = 4)
');

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
    $teacher = array_values($teachers)[0];

    $renderer = new template_renderer(false);
    $email_html = $renderer->render('teacher_email.twig', 'teacher_email.css', (array)$teacher);

    $mail = new PHPMailer();
    $mail->isSendmail();
    $mail->CharSet = 'UTF-8';

    $mail->From = 'noreply@unic.ac.cy';
    $mail->FromName = 'DLIT';
    $mail->addAddress('andreas_stocker@outlook.com', 'Teacher Email Recipient');
    $mail->addAddress('alexander.c@unic.ac.cy', 'Teacher Email Recipient');
    $mail->isHTML(true);

    $mail->Subject = 'Weekly Course Report';
    $mail->Body = $email_html;

    if (!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo "Message has been sent (" . $teacher->email . ")\n";
    }
}
