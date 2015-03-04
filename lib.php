<?php

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/teacher/teacher.php');
require_once(__DIR__ . '/template_renderer.php');

define('IMG_DIR', __DIR__ . '/templates/images/');

function local_moodle_reminders_cron($test_override = null) {
    $renderer = new \template_renderer();

    echo 'Gathering data ... ';

    $teachers = \teacher\teacher::get_all();

    echo 'Sending Emails ... ';

    foreach ($teachers as $teacher) {
        $message = str_replace('images/', 'cid:',$renderer->render('teacher_email.twig', 'teacher_email.css', (array)$teacher));
        $emailAddress = ($test_override) ? $test_override : $teacher->email;

        $mail = new PHPMailer();
        $mail->isSendmail();                                      // Set mailer to use SMTP

        $mail->From = 'noreply@unic.ac.cy';
        $mail->FromName = 'DLIT';
        $mail->addAddress($emailAddress, 'Andy');     // Add a recipient

        echo "IMAGES:\n\n";
        foreach (scandir(IMG_DIR) as $image) {
            echo $image;
            $mail->addEmbeddedImage(IMG_DIR . $image, $image);    // Optional name
        }
        $mail->isHTML(true);                                  // Set email format to HTML
        echo 'IMAGES: END';

        $mail->Subject = 'Weekly Course Report';
        $mail->Body = $message;
        $mail->AltBody = $message;

        if (!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message has been sent';
        }

        if ($test_override) break; // We only want to send on email if testing
    }

    echo 'Sent Emails';
}
