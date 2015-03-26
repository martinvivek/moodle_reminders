<?php

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/classes/factory/teacher_factory.php');
require_once(__DIR__ . '/template_renderer.php');

function local_moodle_reminders_cron() {

    $renderer = new template_renderer();

    echo "Gathering data ... \n";

    $teacher_factory = new teacher_factory();
    $teachers = $teacher_factory->load_records('teacher.sql');

    if (sizeof($teachers) == 0) {
        echo "No subscribed Teachers Found! See: https://github.com/Arubaruba/moodle_reminders#how-to-subscribe-all-teachers\n";
    } else {
        echo "Sending Emails ... \n";

        foreach ($teachers as $teacher) {
            $email_html = $renderer->render('teacher_email.twig', 'teacher_email.css', (array)$teacher);

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
                echo "Message has been sent (" . $teacher->email . ")\n";
            }
        }

        echo 'Sent Emails';
    }
}
