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

global $USER;

$teacher_factory = new teacher_factory();
$teachers = $teacher_factory->load_records('teacher_by_id.sql', array('teacher_id' => $USER->id));

// Set Renderer Options
$PAGE->set_pagelayout('report'); // To add the sidebar
$PAGE->set_title(get_string('pluginname', 'local_moodle_reminders'));
$PAGE->set_heading('Mailer Test');

echo $OUTPUT->header();

$teacher = array_values($teachers)[0];
if (!$teacher->courses) {
    echo '<h2>Error: You are not teaching an active course</h2> ';
} else {
    echo '
<h3>Send your course report as an email to:</h3>
<form action="" method="get">
  <textarea style="width:250px; height:100px" name="emails">' . ($_GET['emails'] ? htmlspecialchars($_GET['emails']) : $teacher->email) . '</textarea>
  <button type="submit">Send Emails!</button>
</form>
';
    $addresses = explode("\n", htmlspecialchars($_GET['emails']));

    if ($addresses) {

        $renderer = new template_renderer(false);
        $email_html = $renderer->render('teacher_email.twig', 'teacher_email.css', (array)$teacher);

        $mail = new PHPMailer();
        $mail->isSendmail();
        $mail->CharSet = 'UTF-8';

        $mail->From = 'noreply@unic.ac.cy';
        $mail->FromName = 'DLIT';
        foreach($addresses as &$address) {
            $mail->addAddress($address, 'Teacher Email Recipient');
        }
        $mail->isHTML(true);

        $mail->Subject = 'Weekly Course Report';
        $mail->Body = $email_html;

        if (!$mail->send()) {
            echo "Message could not be sent.<br>";
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo "Messages Sent: \n \n";
            var_dump($mail->getAllRecipientAddresses());
        }
    }
}

echo $OUTPUT->footer();
