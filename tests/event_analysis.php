<?php

/*
 * Created in March 2015 by Andreas Stocker
 *
 * The purpose of this script is to determine the optimal total number of views and creates
 * a student should have, as well as the ratio of views to creates a student should have each week to receive
 * a certain activity score for the teacher email
 */

require_once(__DIR__ . '/../moodle_environment.php');
require_once(__DIR__ . '/../template_renderer.php');

define('WEEKS_PAST_CONSIDERED', 4);

global $DB;

// Get student events from a certain number of weeks back
// Also make sure the student is currently in an active course
$events = array_values($DB->get_records_sql('
    SELECT COUNT({logstore_standard_log}.id) / :weeks1 AS occurrences, {logstore_standard_log}.action AS name FROM {logstore_standard_log}
    INNER JOIN {role_assignments} ON {role_assignments}.roleid = 5 AND {role_assignments}.userid = {logstore_standard_log}.userid
    INNER JOIN {context} ON {context}.contextlevel = 50 AND {context}.id = {role_assignments}.contextid
    INNER JOIN {course} ON {course}.id = {context}.instanceid AND {course}.visible = 1 AND {course}.format != "site"
    WHERE FROM_UNIXTIME({logstore_standard_log}.timecreated) > NOW() - INTERVAL :weeks2 WEEK
    GROUP BY {logstore_standard_log}.action
', array(
    // Annoyingly, we cannot use a parameter twice, lest we get a "duplicate parameter" error
    // Therefore we needs to add numbers to duplicate parameters and enter them multiple times
    'weeks1' => WEEKS_PAST_CONSIDERED,
    'weeks2' => WEEKS_PAST_CONSIDERED,
)));

// Get the number of students
// Also make sure the student is currently in an active course
$student_count = $DB->get_record_sql('
    SELECT COUNT(DISTINCT {role_assignments}.id) AS student_count FROM {role_assignments}
    INNER JOIN {context} ON {context}.contextlevel = 50 AND {context}.id = {role_assignments}.contextid
    INNER JOIN {course} ON {course}.id = {context}.instanceid AND {course}.visible = 1 AND {course}.format != "site"
    WHERE {role_assignments}.roleid = 5
')->student_count;

// Reduce events by their occurrences property
$total_event_count = array_reduce($events, function ($carry, $event) {
    return $carry + $event->occurrences;
}, 0);

//  Sort events by greatest to least number of occurrences
usort($events, function ($event1, $event2) {
    return $event2->occurrences - $event1->occurrences;
});

$renderer = new template_renderer(false);
echo $renderer->twig->render('event_analysis.twig', array(
    'events' => $events,
    'total_event_count' => $total_event_count,
    'student_count' => $student_count,
    'WEEKS_PAST_CONSIDERED' => WEEKS_PAST_CONSIDERED
));
