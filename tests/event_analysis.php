<?php

require_once(__DIR__ . '/../moodle_environment.php');
require_once(__DIR__ . '/../template_renderer.php');

global $DB;

$actions = array();
$action_occurrences = array();
$total_action_count = 0;
$max_events_in_step = 0;

// Seconds
define('DEFAULT_RANGE_CAP', 30);
define('STEP_COUNT', 15);
if ($_POST['max_range']) $_COOKIE['max_range'] = $_POST['max_range'];
define('RANGE_CAP', ($_COOKIE['max_range']) ? $_COOKIE['max_range'] : DEFAULT_RANGE_CAP);
define('RANGE_SIZE', ceil(RANGE_CAP / STEP_COUNT));

// Get student's ids
$students = array_values($DB->get_records_sql('
    SELECT {user}.id FROM {user}
    INNER JOIN mdl_role_assignments ON roleid = 5
'));

// Loop through students and add their action info to the global action var
foreach ($students as &$student) {
    // Load the student's events
    $events = array_values($DB->get_records_sql('
        SELECT id, timecreated, action FROM {logstore_standard_log}
        WHERE userid = ? ORDER BY timecreated
        ', array($student->id)));

    $last_event = null;
    $total_action_count += sizeof($events);
    foreach ($events as &$event) {
        // if there is no last event just set the time difference to 0
        $time_difference = ($last_event) ? $event->timecreated - $last_event->timecreated : 0;
        // get in which time range the event belongs
        $step_id = min(ceil($time_difference / RANGE_SIZE), RANGE_CAP);

        // If the action does not yet exist in the array declare it as an array
        // and set all of the step values to zero
        if (!$actions[$event->action]) $actions[$event->action] = array();
        $action_occurrences[$event->action] += 1;
        $actions[$event->action][$step_id] += 1;
        $max_events_in_step = max($max_events_in_step, $actions[$event->action][$step_id]);
        $last_event = $event;
    }
}

$selected_action = ($actions[$_GET['action']]) ? $_GET['action'] : array_keys($actions)[0];

$renderer = new template_renderer(false);


$steps = array();
for ($i = 0; $i <= RANGE_CAP; $i = $i + RANGE_SIZE) {
    array_push($steps, array(
        'seconds' => $i,
        'action_count' => $actions[$selected_action][$i]
    ));
}

$action_table = array();
foreach ($action_occurrences as $action_name => &$occurrences) {
    array_push($action_table, array(
        'name' => $action_name,
        'occurrences' => $occurrences,
        'percentage' => $occurrences / $total_action_count * 100
    ));
}

usort($action_table, function($action1, $action2) {
    return $action2['occurrences'] - $action1['occurrences'];
});

echo $renderer->twig->render('event_analysis.twig', array(
    'selected_action' => $selected_action,
    'steps' => $steps,
    'max_events_in_step' => $max_events_in_step,
    'action_table' => $action_table,
    'RANGE_CAP' => RANGE_CAP
));
