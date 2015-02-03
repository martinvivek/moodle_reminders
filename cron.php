<?php

require_once(__DIR__ . '/teacher/teacher.php');
require_once(__DIR__ . '/template_renderer.php');

$headers = "From: noreply@unic.ac.cy\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

$renderer = new \template_renderer();

$time_get_teachers = microtime(true);
$teachers = \teacher\teacher::get_all();
echo 'Time to get Teachers: ' . (microtime(true) - $time_get_teachers) * 1000 . " ms\n";

//$time_send_emails = microtime(true);
//foreach($teachers as $teacher) {
//    $message = $renderer->render('teacher_email.twig', (array) $teacher);
//    $message = str_replace('\n.', '\n..', $message);
//    $message = wordwrap($message, 70, '\r\n');
//    mail($teacher->email, 'Weekly Course Report', $message, $headers);
//}
//echo 'Time to send Emails: ' . (microtime(true) - $time_send_emails) * 1000 . ' ms';
