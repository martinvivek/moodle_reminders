<?php

/**
 * This script allows individuals to view their course report in
 * the browser instead of inside an email client
 */

require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../classes/factory/teacher_factory.php');
require_once(__DIR__ . '/../template_renderer.php');

$url = new moodle_url('/local/moodle_reminders/web_view.php');
$PAGE->set_url($url);

$teacher_factory = new teacher_factory();
$teachers = $teacher_factory->load_records('teacher.sql');

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
    $mail->addAddress('shadowstep7@gmail.com', 'Teacher Email Recipient');
    $mail->isHTML(true);

    $mail->Subject = 'Weekly Course Report';
    $mail->Body = $email_html;

    if (!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo "Message has been sent \n \n";
        var_dump($mail->getAllRecipientAddresses());
    }
}
