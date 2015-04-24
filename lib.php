<?php

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/classes/factory/teacher_factory.php');
require_once(__DIR__ . '/template_renderer.php');
require_once(__DIR__ . '/logstore_cache.php');

/**
 * @return float Microtime to be passed to end_timer function
 */
function start_timer() {
    return microtime(true);
}

/**
 * @param $start_time float Value returned by start_timer
 * @return float Seconds since start_timer
 */
function end_timer($start_time) {
    return round(microtime(true) - $start_time, 2);
}

function local_moodle_reminders_cron() {

    // Update the logstore cache
    update_cache();

    $renderer = new template_renderer();

    echo 'MOODLE REMINDERS<br>';
    echo "Data gathering ... ";
    $time_data_gathering_started = start_timer();

    $teacher_factory = new teacher_factory();
    $teachers = $teacher_factory->load_records('teacher.sql');

    echo 'done (' . end_timer($time_data_gathering_started) . ' sec)<br>';

    if (sizeof($teachers) == 0) {
        echo "No subscribed Teachers Found!<br>See <a target='_blank' href='https://github.com/Arubaruba/moodle_reminders#enabling-weekly-emails'>Readme</a>";
    } else {
        echo "Sending ". count($teachers) ." Emails ... <br>";
        $time_sending_emails_started = start_timer();

        foreach ($teachers as $teacher) {
            $email_html = $renderer->render_email('teacher_email.twig', 'teacher_email.css', (array)$teacher);

            $mail = new PHPMailer();
            $mail->CharSet = 'UTF-8';

            $mail->From = 'noreply@unic.ac.cy';
            $mail->FromName = 'DLIT';
            $mail->addAddress($teacher->email, $teacher->name);
            $mail->isHTML(true);

            $mail->Subject = 'Weekly Course Report';
            $mail->Body = $email_html;

            if (!$mail->send()) {
                echo 'Message could not be sent. ';
                echo 'Mailer Error: ' . $mail->ErrorInfo . '<br>';
            } else {
                echo " - <i>" . $teacher->email . "</i><br>";
            }
        }

        echo 'Sending Emails done (' . end_timer($time_sending_emails_started) . ' sec)<br>';
    }
}
