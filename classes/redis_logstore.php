<?php

namespace local_moodle_reminders;

require_once(__DIR__ . '/../vendor/autoload.php');

class redis_logstore {

    private static function connect_to_redis() {
        $redis = null;
        try {
            $redis = new \Predis\Client();
        } catch (\Exception $exception) {
            echo 'Could not connect to redis.';
            echo $exception->getMessage();
        }
        return $redis;
    }

//    private $redis;
//    function __construct() {
//        try {
//            $this->redis = new Predis\Client();
//            $this->redis->set('php', 1);
//        } catch (Exception $exception) {
//            echo 'Could not connect to redis.';
//            echo $exception->getMessage();
//        }
//    }
//
//    public function add_record($id, $course_id, $user_id, $eventname, $object_id, $ip) {
//        $this->redis->set('php', 'got_event 1234');
//    }

    public static function on_event($event) {
        // Try to connect to redis; if this fails connect_to_redis will return null
        if ($redis = redis_logstore::connect_to_redis()) {
            $redis->set('php', $event->timecreated);
        }
    }
}