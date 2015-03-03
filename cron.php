<?php

require_once(__DIR__ . '/teacher/teacher.php');
require_once(__DIR__ . '/template_renderer.php');

$headers = "From: noreply@unic.ac.cy\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

$renderer = new \template_renderer();

echo 'Gathering data ... ';

$teachers = \teacher\teacher::get_all();

echo 'Sending Emails ... ';

foreach($teachers as $teacher) {
    $message = $renderer->render('teacher_email.twig', 'teacher_email.css', (array) $teacher);
    $message = str_replace('\n.', '\n..', $message);
    $message = wordwrap($message, 70, '\r\n');
    mail($teacher->email, 'Weekly Course Report', $message, $headers);
}

echo 'Sent Emails';


