<?php

require_once(__DIR__ . '/../../config.php');

define('LOGSTORE_CACHE_TABLE_NAME', 'moodle_reminders_logstore_cache');

function update_cache() {

    global $DB;

    // Delete the outdated cache table
    $DB->execute('DROP TABLE IF EXISTS ' . LOGSTORE_CACHE_TABLE_NAME);

    // Create a new cache table by copying the logstore table (without data)
    $DB->execute('CREATE TABLE ' . LOGSTORE_CACHE_TABLE_NAME . ' SELECT * FROM {logstore_standard_log} LIMIT 0');

    // Get the date the oldest active course was created
    $oldest_visible_course = $DB->get_record_sql("SELECT MIN(timecreated) AS timecreated FROM {course} WHERE {course}.visible = 1 AND {course}.format != 'site'");

    // Copy the data to be cached into the cache table from the logstore
    $DB->execute('INSERT INTO ' . LOGSTORE_CACHE_TABLE_NAME . ' SELECT logstore.*
    FROM {logstore_standard_log} logstore WHERE logstore.action = "viewed" AND logstore.timecreated > ?', array($oldest_visible_course->timecreated));
}
