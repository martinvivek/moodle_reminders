<?php

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
    permission_denied_page();
} else {
    if ($_GET['action'])
        update_subscription_status($_GET['action'] == 'subscribe', $_GET['force'], $_GET['teacher_id']);

    render_page();
}

/**
 * @param bool $subscribe Subscribe or unsubscribe?
 * @param bool $force Subscribe even people who manually unsubscribed themselves?
 * @param int $teacher_id (Optional) Perform action on only the teacher with this id
 */
function update_subscription_status($subscribe, $force = false, $teacher_id = null) {
    global $DB;

    if ($subscribe) {
        // People who manually unsubscribed already have an existing preference record so we update it
        if ($force) {
            $DB->execute("UPDATE {user_preferences} SET VALUE = 'email' WHERE NAME = 'message_provider_local_moodle_reminders_course_reports_loggedoff' AND (:all_teachers OR userid = :teacher_id)", array(
                'all_teachers' => $teacher_id == null,
                'teacher_id' => $teacher_id
            ));
        }

        // People who are unsubscribed by default have no preference record so we create one

        // If no teacher id was provided we select teachers from the role assignments table
        if ($teacher_id == null) {
            $DB->execute("INSERT IGNORE INTO {user_preferences} (id, userid, NAME, VALUE)
SELECT NULL, {role_assignments}.userid,'message_provider_local_moodle_reminders_course_reports_loggedoff', 'email' FROM {role_assignments} WHERE roleid = 3 OR roleid = 4");
        } else {
            $DB->execute("INSERT IGNORE INTO {user_preferences} (id, userid, NAME, VALUE)
SELECT NULL, ?,'message_provider_local_moodle_reminders_course_reports_loggedoff', 'email'", array($teacher_id));
        }
    } else {
        // We want to remember who unsubscribed so we don't remove preferences for unsubscription
        $DB->execute("DELETE FROM {user_preferences} WHERE NAME = 'message_provider_local_moodle_reminders_course_reports_loggedoff' AND VALUE = 'email' AND (:all_teachers OR userid = :teacher_id)", array(
            'all_teachers' => $teacher_id == null,
            'teacher_id' => $teacher_id
        ));
    }
}

function permission_denied_page() {
    global $PAGE, $OUTPUT;
    $PAGE->set_heading('Permission Denied');
    echo $OUTPUT->header();
    echo 'You\'re not logged in as an admin. Sorry.';
    echo $OUTPUT->footer();
}

function render_page() {
    global $PAGE, $DB, $OUTPUT;
    $PAGE->set_heading('Moodle Reminders - Admin Dashboard');
    echo $OUTPUT->header();

    $subscribed_count = $DB->get_record_sql("SELECT COUNT(*) AS val FROM {user_preferences} WHERE NAME = 'message_provider_local_moodle_reminders_course_reports_loggedoff' AND VALUE = 'email'")->val;

    $manually_unsubscribed_count = $DB->get_record_sql("SELECT COUNT(*) AS val FROM {user_preferences} WHERE NAME = 'message_provider_local_moodle_reminders_course_reports_loggedoff' AND VALUE = 'unsubscribed'")->val;

    $teachers = $DB->get_records_sql("
SELECT {user}.id, firstname, lastname, email,{user_preferences}.value AS subscribed FROM {user}
JOIN {role_assignments} ON {role_assignments}.userid = {user}.id AND {role_assignments}.roleid = 3 OR {role_assignments}.roleid = 4
LEFT JOIN {user_preferences} ON {user_preferences}.userid = {user}.id AND NAME = 'message_provider_local_moodle_reminders_course_reports_loggedoff'
");

    $template_renderer = new template_renderer(false);

    echo $template_renderer->render('admin_dashboard.twig', array(
        'subscribed_count' => $subscribed_count,
        'manually_unsubscribed_count' => $manually_unsubscribed_count,
        'teachers' => $teachers
    ));
    echo $OUTPUT->footer();
}
