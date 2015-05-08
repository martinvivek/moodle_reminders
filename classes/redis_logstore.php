<?php

namespace local_moodle_reminders;

require_once(__DIR__ . '/../vendor/autoload.php');


/**
 * Class redis_logstore
 * @package local_moodle_reminders
 */
class redis_logstore {
    /**
     * @var $redis_connection \Predis\Client Should only be accessed by the connect_to_redis_once function
     */
    private static $redis_connection;

    /**
     * We only connect to redis when we need to
     * @return \Predis\Client
     */
    private static function connect_to_redis_once() {
        if (!redis_logstore::$redis_connection) {
            try {
                redis_logstore::$redis_connection = new \Predis\Client(require(__DIR__ . '/../redis_config.php'));
            } catch (\Exception $exception) {
                echo 'Could not connect to redis.';
                echo $exception->getMessage();
            }
        }
        return redis_logstore::$redis_connection;
    }

    /**
     * @param $user_id
     * @param $action String Moodle event action type like "viewed", "created" etc...
     * @param $course_id
     * @return Integer How many times a user performed an action of a certain type within a course
     */
    public static function user_action_frequency_in_course($user_id, $action, $course_id) {
        $redis = redis_logstore::connect_to_redis_once();
        return $redis->hget('course:' . $course_id . ':' . $action, $user_id);
    }

    /**
     * @param $course_id
     * @param $user_id
     * @return Integer UNIX timestamp
     */
    public static function course_last_accessed_by_user($course_id, $user_id) {
        $redis = redis_logstore::connect_to_redis_once();
        return $redis->hget('course:' . $course_id . ':last_access', $user_id);
    }

    /**
     * Configured hook into the moodle event system
     * @param $event
     */
    public static function on_event($event) {
        if ($event->action == 'viewed') {
            $redis = redis_logstore::connect_to_redis_once();
            $redis->hincrby('course:' . $event->courseid . ':viewed', $event->userid, 1);
            $redis->hmset('course:' . $event->courseid . ':last_access', $event->userid, $event->timecreated);
        }
    }
}