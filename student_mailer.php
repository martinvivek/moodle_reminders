<?php

/**
 * Allows teachers to select students from a list and send them emails
 */

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/classes/factory/student_factory.php');
require_once(__DIR__ . '/classes/factory/course_factory.php');
require_once(__DIR__ . '/template_renderer.php');

global $USER;
global $DB;
$teacher = $DB->get_record('user', array('id' => $USER->id));

$default_message = "
Hello,

it seems that you have not been very active in the course [COURSE_LINK].

This is a kind reminder from your teacher to encourage you to get back on track.

Regards,

" . $teacher->firstname . " " . $teacher->lastname . "

";

$url = new moodle_url('/local/moodle_reminders/student_mailer.php?course_id=' .
    filter_var($_GET['course_id'], FILTER_SANITIZE_NUMBER_INT));
$PAGE->set_url($url);

require_login();

$PAGE->set_pagelayout('report'); // To add the sidebar
$PAGE->set_title('Send Emails to Students');
$PAGE->set_heading('Send Emails to Students');
echo $OUTPUT->header();

if ($_POST['message']) {
    $mail = new PHPMailer();
    $mail->CharSet = 'UTF-8';

    $mail->From = 'noreply@unic.ac.cy';
    $mail->FromName = 'DLIT';
    foreach (explode($_POST['email_addresses'], ',') as $email_address) {
        $filtered_email = filter_var($email_address, FILTER_SANITIZE_EMAIL);
//        $mail->addAddress($filtered_email, $filtered_email);
    }
    $mail->addAddress('shadowstep7@gmail.com', 'test');
    $mail->isHTML(true);

    $courses = array_values($course_factory->load_records('course_if_authorized.sql', array(
        'course_id' => $_POST['course_id'],
        'teacher_id' => $USER->id
    )));

    if (!$courses) {
        echo '<h3>Error: You are not authorized to send these emails</h3>';
    } else {
        $course = $courses[0];
        $sanitized_message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
        $message_with_link = str_replace('[COURSE_LINK]', '<a target="_blank" href=""></a>', $sanitized_message);
        $mail->Subject = 'Participation Reminder';
        $mail->Body = $message_with_link;

        if (!$mail->send()) {
            echo '<h3>Could not Send Emails</h3>';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo '<h3>Emails sent</h3>';
        }
        echo '<p><a href="">Back to Student List</a></p>';
    }
} else {
    if (!$_GET['course_id']) {
        echo '<h2>Error: Course id missing in URL</h2>';
    } else {
        $course_factory = new course_factory();
        $courses = array_values($course_factory->load_records('course_if_authorized.sql', array(
            'course_id' => $_GET['course_id'],
            'teacher_id' => $USER->id
        )));

        if (!$courses) {
            echo '<h3>Error: You are not authorized to view a student list for this course</h3>';
        } else {
            $template_renderer = new template_renderer(false);
            $student_factory = new student_factory();
            $students = $student_factory->load_records('student.sql',
                array('course_id' => $_GET['course_id']),
                array('action_cases' => $student_factory->get_action_cases_sql()));

            if (!$students) {
                echo '<h3>Error: This course has no students</h3>';
            } else {
                echo $template_renderer->render('student_mailer.twig', array('students' => $students, 'course' => $courses[0], 'default_message' => $default_message));
            }
        }
    }
}
echo $OUTPUT->footer();
