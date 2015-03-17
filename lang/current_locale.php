<?php

define("CLI_SCRIPT", 1);
require_once(__DIR__ . '/../../../config.php');

echo "Current language : " . current_language() . "\n
Note: This is the language in which emails will actually be mailed.
render_test_email.php may use a different language depending on the moodle language settings of an open moodle tab
and the browser
";