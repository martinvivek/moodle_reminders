<?php

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/teacher/teacher.php');
require_once(__DIR__ . '/template_renderer.php');

function local_moodle_reminders_cron() {
    $renderer = new \template_renderer();

    echo 'Gathering data ... ';

    $teachers = \teacher\teacher::get_all();

    echo 'Sending Emails ... ';

    global $CFG;
    $image_url = $CFG->wwwroot . '/local/moodle_reminders/templates/images/';

    foreach ($teachers as $teacher) {
        $rendered_template = $renderer->render('teacher_email.twig', 'teacher_email.css', (array)$teacher);
        $email_html = str_replace('images/', $image_url, $rendered_template);

        $mail = new PHPMailer();
        $mail->isSendmail();
        $mail->CharSet = 'UTF-8';

        $mail->From = 'noreply@unic.ac.cy';
        $mail->FromName = 'DLIT';
        $mail->addAddress($teacher->email, $teacher->name);
        $mail->isHTML(true);

        $mail->Subject = 'Weekly Course Report';
        $mail->Body = $email_html;

        if (!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message has been sent (' . $teacher->email . ')';
        }
    }

    echo 'Sent Emails';
}
