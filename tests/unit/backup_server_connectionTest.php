<?php

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../backup_server_connection.php');

class backup_server_connectionTest extends PHPUnit_Framework_TestCase {
    public function test_connection() {
        global $__backup_server_database_connection;
        $this->assertEquals($__backup_server_database_connection->query('SELECT 1')->num_rows, 1);
    }
}
