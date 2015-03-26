<?php

require_once('factory.php');
require_once('discussion.php');

class discussion_factory extends factory {
    protected function construct_record($row) {
        return new discussion($row->id, $row->name, $row->post_count);
    }
}