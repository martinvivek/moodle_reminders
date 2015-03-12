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

$date_start = (new DateTime())->modify('-2 month');
$date_end = new DateTime();

$invalid_dates = false;

if ($_GET['cc_start']) {
    $parsed_start = DateTime::createFromFormat(DATE_FORMAT, $_GET['cc_start']);
    $parsed_end = DateTime::createFromFormat(DATE_FORMAT, $_GET['cc_end']);

    // Make sure both entered dates are valid
    if (!$parsed_start || !$parsed_end) {
        $invalid_dates = true;
    } else {
        $date_start = $parsed_start;
        $date_end = $parsed_end;
    }
}

global $DB;

$began_sql_queries = microtime(true);

// Get student events from a certain number of weeks back
// Also make sure the student is currently in an active course
$events = array_values($DB->get_records_sql('
    SELECT COUNT(DISTINCT {logstore_standard_log}.id) / DATEDIFF(DATE(:end_date1), DATE(:start_date1)) / 7 AS occurrences, {logstore_standard_log}.action AS name FROM {logstore_standard_log}
    INNER JOIN {role_assignments} ON {role_assignments}.roleid = 5 AND {role_assignments}.userid = {logstore_standard_log}.userid
    WHERE FROM_UNIXTIME({logstore_standard_log}.timecreated) BETWEEN :start_date2 AND :end_date2
    GROUP BY {logstore_standard_log}.action ORDER BY occurrences DESC

', array(
    'start_date1' => $date_start->format('Y-m-d'),
    'end_date1' => $date_end->format('Y-m-d'),
    'start_date2' => $date_start->format('Y-m-d'),
    'end_date2' => $date_end->format('Y-m-d'),
)));

// Get the number of students
$student_count = $DB->get_record_sql('
    SELECT COUNT(DISTINCT {role_assignments}.userid) AS student_count FROM {role_assignments}
    INNER JOIN {logstore_standard_log} ON FROM_UNIXTIME({logstore_standard_log}.timecreated) BETWEEN :start_date AND :end_date
    WHERE {role_assignments}.roleid = 5
', array(
    'start_date' => $date_start->format('Y-m-d'),
    'end_date' => $date_end->format('Y-m-d'),
))->student_count;

// Reduce events by their occurrences property
$total_event_count = array_reduce($events, function ($carry, $event) {
    return $carry + $event->occurrences;
}, 0);

$finished_sql_queries = microtime(true);


$renderer = new template_renderer(false);
echo $renderer->twig->render('event_analysis.twig', array(
    'time_taken' => $finished_sql_queries - $began_sql_queries,
    'formatted_date_start' => $date_start->format(DATE_FORMAT),
    'formatted_date_end' => $date_end->format(DATE_FORMAT),
    'invalid_dates' => $invalid_dates,
    'events' => $events,
    'total_event_count' => $total_event_count,
    'student_count' => $student_count,
    'WEEKS_PAST_CONSIDERED' => WEEKS_PAST_CONSIDERED
));
