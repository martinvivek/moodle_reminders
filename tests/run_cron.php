<?php

namespace local_moodle_reminders;

require_once(__DIR__ . '/../lib.php');

global $USER;

$is_admin = false;
foreach (get_admins() as $admin) {
    if ($admin->id == $USER->id) $is_admin = true;
}

if ($is_admin) local_moodle_reminders_cron();
else echo 'You must be admin to do this';

