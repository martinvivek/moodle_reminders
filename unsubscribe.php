<?php

require_once('../../config.php');

$url_unsubscribe = '/local/moodle_reminders/unsubscribe.php';
$url_resubscribe = '/local/moodle_reminders/unsubscribe.php?resubscribe=1';

$url = new moodle_url(($_GET['resubscribe']) ? $url_resubscribe : $url_unsubscribe);
$PAGE->set_url($url);

require_login();

global $USER, $DB;

// Set Renderer Options
$PAGE->set_pagelayout('report'); // To add the sidebar
$PAGE->set_title(get_string('pluginname', 'local_moodle_reminders'));
$PAGE->set_heading(get_string('pluginname', 'local_moodle_reminders'));

echo $OUTPUT->header();

global $CFG;

$user_setting = '';
$message = '';

if ($_GET['resubscribe']) {
    $user_setting = 'email';
    $message = ' <h1>You have resubscribed!</h1> You will now get these emails again.';
} else {
    $user_setting = 'none';
    $message = ' <h1>You have unsubscribed!</h1> You will no longer get these emails.';
}

$DB->execute('
  UPDATE {user_preferences} SET {user_preferences}.value = ? WHERE userid = ? AND {user_preferences}.name = "message_provider_local_moodle_reminders_course_reports_loggedoff"
', array($user_setting, $USER->id));
echo $message;

echo $OUTPUT->footer();
