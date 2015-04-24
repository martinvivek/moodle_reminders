<?php

/**
 * Allows teachers to select students from a list and send them emails
 */

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/classes/factory/student_factory.php');
require_once(__DIR__ . '/classes/factory/course_factory.php');
require_once(__DIR__ . '/template_renderer.php');

if ($_POST['message']) send_mail();
else mailer_options_page();

function mailer_options_page() {
    global $DB, $USER, $PAGE, $OUTPUT;

    // Set the url in case the user is not logged in so they will be redirected to this page upon login
    $url = new moodle_url('/local/moodle_reminders/student_mailer.php?course_id=' .
        filter_var($_GET['course_id'], FILTER_SANITIZE_NUMBER_INT));
    $PAGE->set_url($url);
    require_login();

    // Render the first part of the default Moodle layout
    $PAGE->set_pagelayout('report'); // To add the sidebar
    $PAGE->set_title('Send Emails to Students');
    $PAGE->set_heading('Send Emails to Students');
    echo $OUTPUT->header();

    // Load the teacher's name and put it into the default message
    $teacher = $DB->get_record('user', array('id' => $USER->id));
    $default_message = "
Dear [STUDENT_NAME]

It seems that you have not been very active in your online course [COURSE_LINK].

This is a kind reminder from your teacher to encourage you to get back on track.

Kind regards

" . $teacher->firstname . " " . $teacher->lastname;

    // Attempt to retrieve the course by the id given in the url
    $course_factory = new course_factory();
    $courses = array_values($course_factory->load_records('course_if_authorized.sql', array(
        'course_id' => $_GET['course_id'],
        'teacher_id' => $USER->id
    )));

    // If the user is an authorized teacher of the course then load a list of the students and render the page
    // template with it
    if (!$courses) {
        echo '<h3>Error: You are not authorized to view a student list for this course</h3> (or no course with this id exists)';
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

    echo $OUTPUT->footer();
}

function send_mail() {
    global $USER, $PAGE, $OUTPUT;

    // Render the first part of the default Moodle layout
    $PAGE->set_title('Send Emails to Students');
    $PAGE->set_heading('Send Emails to Students');
    echo $OUTPUT->header();

    // Attempt to retrieve the course by the id given in the url
    $course_factory = new course_factory();
    $courses = array_values($course_factory->load_records('course_if_authorized.sql', array(
        'course_id' => $_GET['course_id'],
        'teacher_id' => $USER->id
    )));

    if (!$courses) {
        echo '<h3>Error: You are not authorized to send these emails</h3>';
    } else {
        $student_factory = new student_factory();
        $students = $student_factory->load_records('student.sql',
            array('course_id' => $_GET['course_id']),
            array('action_cases' => $student_factory->get_action_cases_sql()));

        $student_ids = explode(',', $_POST['student_ids']);

        foreach ($students as &$student) {
            if (in_array($student->id, $student_ids)) {
                // Initialize PHPMailer
                $mail = new PHPMailer();
                $mail->CharSet = 'UTF-8';
                $mail->From = 'noreply@unic.ac.cy';
                $mail->FromName = 'UNIC Moodle';

                // Get a list of students to make sure every email address actually belongs to a student of that course
                $mail->addAddress($student->email, $student->name);

                $mail->isHTML(true);
                $mail->Subject = 'Participation Reminder';

                // Set message text
                $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
                $message = str_replace("\n", '<br>', $message);
                $message = '<div style="color: #333; font-size: 15px;">' . $message . '</div>';
                $course = $courses[0];
                $message = str_replace('[COURSE_LINK]', '<a target="_blank" href="' . $course->get_link() . '">' . $course->name . '</a>', $message);
                $message = str_replace('[STUDENT_NAME]', $student->name, $message);
                $mail->Body = $message;

                if (!$mail->send()) {
                    echo 'Could not send emails to student ' . $student->name . ' ';
                    echo 'Mailer Error: ' . $mail->ErrorInfo . '<br>';
                } else {
                    echo 'Email sent to student ' . $student->name . '<br>';
                }
            }
        }
        echo '<br><br><p><a href="">Back to Student List</a></p>';
    }

    echo $OUTPUT->footer();
}

