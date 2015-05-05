<?php

//require_once(__DIR__ . '/../classes/redis_logstore.php');
require_once(__DIR__ . '/../vendor/autoload.php');

$observers = array(
    array(
        'eventname' => '*',
        'callback' => '\local_moodle_reminders\redis_logstore::on_event',
        'internal' => false
    )
);
