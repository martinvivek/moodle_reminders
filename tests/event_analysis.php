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
define('DATE_FORMAT', 'd/m/Y');

$course_create_date_start = (new DateTime())->modify('-1 year');
$course_create_date_end = new DateTime();

$invalid_dates = false;

if ($_GET['cc_start']) {
    $parsed_start = DateTime::createFromFormat(DATE_FORMAT, $_GET['cc_start']);
    $parsed_end = DateTime::createFromFormat(DATE_FORMAT, $_GET['cc_end']);

    // Make sure both entered dates are valid
    if (!$parsed_start || !$parsed_end) {
        $invalid_dates = true;
    } else {
        $course_create_date_start = $parsed_start;
        $course_create_date_end = $parsed_end;
    }
}

global $DB;

// Get student events from a certain number of weeks back
// Also make sure the student is currently in an active course
$events = array_values($DB->get_records_sql('
    SELECT COUNT(DISTINCT {logstore_standard_log}.id) / DATEDIFF(CURDATE(), FROM_UNIXTIME(MAX({course}.timecreated))) / 7 AS occurrences, {logstore_standard_log}.action AS name FROM {logstore_standard_log}
    INNER JOIN {role_assignments} ON {role_assignments}.roleid = 5 AND {role_assignments}.userid = {logstore_standard_log}.userid
    INNER JOIN {context} ON {context}.contextlevel = 50 AND {context}.id = {role_assignments}.contextid
    INNER JOIN {course} ON {course}.id = {context}.instanceid AND FROM_UNIXTIME({course}.timecreated) BETWEEN :start_date AND :end_date
    GROUP BY {logstore_standard_log}.action ORDER BY COUNT(DISTINCT {logstore_standard_log}.id)
', array(
    'start_date' => $course_create_date_start->format('Y-m-d'),
    'end_date' => $course_create_date_end->format('Y-m-d'),
)));

// Get the number of students
// Also make sure the student is currently in an active course
$student_count = $DB->get_record_sql('
    SELECT COUNT(DISTINCT {role_assignments}.id) AS student_count FROM {role_assignments}
    INNER JOIN {context} ON {context}.contextlevel = 50 AND {context}.id = {role_assignments}.contextid
    INNER JOIN {course} ON {course}.id = {context}.instanceid AND FROM_UNIXTIME({course}.timecreated) BETWEEN :start_date AND :end_date
    WHERE {role_assignments}.roleid = 5
', array(
    'start_date' => $course_create_date_start->format('Y-m-d'),
    'end_date' => $course_create_date_end->format('Y-m-d'),
))->student_count;

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
    'formatted_date_start' => $course_create_date_start->format(DATE_FORMAT),
    'formatted_date_end' => $course_create_date_end->format(DATE_FORMAT),
    'invalid_dates' => $invalid_dates,
    'events' => $events,
    'total_event_count' => $total_event_count,
    'student_count' => $student_count,
    'WEEKS_PAST_CONSIDERED' => WEEKS_PAST_CONSIDERED
));
