<?php

namespace local_moodle_reminders;

/* Utility class for keeping track of execution time */
class timer {
    private $start_time;

    function start() {
        $this->start_time = microtime(true);
    }

    /**
     * @return int Time in milliseconds since start or restart was called on this class
     */
    function time_passed() {
        return intval((microtime(true) - $this->start_time) * 1000);
    }

    /**
     * Starts the timer again but also returns how much time passed
     * @return int Time Passed
     */
    function restart() {
        $time_passed = $this->time_passed();
        $this->start();
        return $time_passed;
    }
}