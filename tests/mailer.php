<?php

# needs to be executed by php mailer.php; cannot be opened in browser
define('CLI_SCRIPT', 1);

require(__DIR__ . '/../lib.php');

local_moodle_reminders_cron();

