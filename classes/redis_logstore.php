<?php

namespace local_moodle_reminders;

require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../redis_password.php');

define('REDIS_LOGSTORE_TABLE', 'logstore_standard_log');

/**
 * Class redis_logstore
 * @package local_moodle_reminders
 */

try {
    global $redisDB;
    $redisDB = new \Predis\Client(array('password' => LOCAL_MOODLE_REMINDERS_REDIS_PASSWORD));
} catch (\Exception $exception) {
    echo 'Could not connect to redis.';
    echo $exception->getMessage();
}

class redis_logstore {

    public static function get_event_key($action, $userid, $courseid) {
        return json_encode(array(
            'table' => REDIS_LOGSTORE_TABLE,
            'userid' => intval($userid),
            'courseid' => intval($courseid),
            'action' => $action
        ));
    }

    public static function on_event($event) {
        // Try to connect to redis; if this fails connect_to_redis will return null
        global $redisDB;
        $key = redis_logstore::get_event_key($event->action, $event->userid, $event->courseid);
        $value = json_encode(array(
            'id' => $redisDB->incr(REDIS_LOGSTORE_TABLE . ':autoincrement'),
            'eventname' => $event->eventname,
            'objectid' => intval($event->objectid)
        ));
        $score = intval($event->timecreated);
        $redisDB->zadd($key, array($value => $score));
    }
}