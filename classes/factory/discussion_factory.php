<?php

namespace local_moodle_reminders;

require_once(__DIR__ . '/factory.php');
require_once(__DIR__ . '/../discussion.php');

class discussion_factory extends factory {
    protected function construct_record($row, $load_dependencies) {
        return new discussion($row->id, $row->name);
    }
}