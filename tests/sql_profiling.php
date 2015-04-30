<?php

/**
 * @return float Microtime to be passed to end_timer function
 */
function start_timer() {
    return microtime(true);
}

/**
 * @param $start_time float Value returned by start_timer
 * @return float Seconds since start_timer
 */
function end_timer($start_time) {
    return round(microtime(true) - $start_time, 2);
}


function time_course_factory() {

}
