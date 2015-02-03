<?php

$headers = "From: noreply@unic.ac.cy\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

$message = 'this is the message';
$message = str_replace('\n.', '\n..', $message);
$message = wordwrap($message, 70, '\r\n');

$a = mail('shadowstep7@gmail.com', 'My Subject', $message, $headers);

echo 'done';