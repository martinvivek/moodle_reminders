<?php

/**
 * This is a utility script whose output is to be manually edited
 * and put into the sql/moodle_logstore_to_redis.sql file
 */

namespace local_moodle_reminders;

require_once(__DIR__ . '/../classes/redis_logstore.php');

// Escape twice, once for redis, once for mysql
function redisParam($val) {
    return addslashes('"' . addslashes($val) . '"');
}

echo "Key:<br>";
echo redisParam(redis_logstore::get_event_key('ACTION', 1, 1));
echo "<br><br>";

$value = json_encode(array(
    'id' => 0,
    'eventname' => 'EVENT_NAME',
    'objectid' => 2
));
echo "Value:<br>";
echo redisParam($value);
