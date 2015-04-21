<?php
/**
 * This script allows individuals to view their course report in
 * the browser instead of inside an email client
 */
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/template_renderer.php');

$url = new moodle_url('/local/moodle_reminders/admin.php');
$PAGE->set_url($url);

require_login();

$PAGE->set_pagelayout('report'); // To add the sidebar
$PAGE->set_title('Moodle Reminders Admin Dashboard');

global $USER;

$is_admin = false;
foreach (get_admins() as $admin) {
    if ($admin->id == $USER->id) $is_admin = true;
}

if (!$is_admin) {
    var_dump(get_admins());
    $PAGE->set_heading('Permission Denied');
    echo $OUTPUT->header();
    echo 'You\'re not logged in as an admin. Sorry.';
    echo $OUTPUT->footer();
} else {
    switch ($_GET['action']) {
        /** @noinspection PhpMissingBreakStatementInspection */
        case 'force_subscribe_all':
            $DB->execute("UPDATE {user_preferences} SET VALUE = 'email' WHERE NAME = 'message_provider_local_moodle_reminders_course_reports_loggedoff'");
        case 'subscribe_all':
            $DB->execute("INSERT IGNORE INTO {user_preferences} (id, userid, NAME, VALUE)
SELECT NULL, userid,'message_provider_local_moodle_reminders_course_reports_loggedoff', 'email' FROM {role_assignments} WHERE roleid = 3 OR roleid = 4");
            break;
        case 'unsubscribe_all':
            $DB->execute("DELETE FROM {user_preferences} WHERE NAME = 'message_provider_local_moodle_reminders_course_reports_loggedoff' AND VALUE = 'email'");
            break;
    }

    $PAGE->set_heading('Moodle Reminders Admin Dashboard');
    echo $OUTPUT->header();

    global $DB;
    $subscribed_count = $DB->get_record_sql("SELECT COUNT(*) AS val FROM {user_preferences} WHERE NAME = 'message_provider_local_moodle_reminders_course_reports_loggedoff' AND VALUE = 'email'")->val;

    $manually_unsubscribed_count = $DB->get_record_sql("SELECT COUNT(*) AS val FROM {user_preferences} WHERE NAME = 'message_provider_local_moodle_reminders_course_reports_loggedoff' AND VALUE = 'unsubscribed'")->val;

    $template_renderer = new template_renderer(false);

    echo $template_renderer->render('admin_dashboard.twig', array(
        'subscribed_count' => $subscribed_count,
        'manually_unsubscribed_count' => $manually_unsubscribed_count
    ));
    echo $OUTPUT->footer();

}
