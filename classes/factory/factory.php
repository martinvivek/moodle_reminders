<?php

define('FACTORY_QUERY_FOLDER', __DIR__ . '/../../sql/factory/');
define('UNESCAPED_VAR_PREFIX', '#SQL ');

require_once(__DIR__ . '/../../backup_server_connection.php');

abstract class factory {

    /**
     * Maps a row retrieved in an SQL query to an initialized object of the class type
     * @param $row object
     * @param $load_dependencies bool
     * @return object
     */
    abstract protected function construct_record($row, $load_dependencies);

    /**
     * This function triggers the custom 'construct_record' function for each record found to return an array of initialized objects produced by the factory
     *
     * @param $file_name string The name of a file in the /sql/factory directory
     * @param $vars array() A map or set of parameters which will be replaced in the loaded sql file
     * Param keys should be prefixed with ':' in the sql file if a map is used
     * If a set is used, '?'s should be put in place of the values
     * They may not occur more than once
     * @param $unescaped_vars array() Must be prefixed with UNESCAPED_VAR_PREFIX and may appear multiple times
     * @param $load_dependencies bool Passed to construct_record function
     * @return array() Records found by the query
     */
    public function load_records($file_name, $vars = array(), $unescaped_vars = array(), $load_dependencies = true) {
        $prefixed_unescaped_var_keys = array_map(function ($key) {
            return UNESCAPED_VAR_PREFIX . $key;
        }, array_keys($unescaped_vars));

        // Load the sql query as a string
        $query_string = file_get_contents(FACTORY_QUERY_FOLDER . $file_name);
        // Insert unescaped vars
        $query_string = str_replace($prefixed_unescaped_var_keys, array_values($unescaped_vars), $query_string);

        global $__backup_server_database_connection;

        foreach (array_keys($vars) as $var) {
            $query_string = str_replace(':' . $var, $__backup_server_database_connection->real_escape_string($vars[$var]), $query_string, $count = 1);
        }

        $query_result = $__backup_server_database_connection->query($query_string);

        $records = array();
        if ($query_result) {
            for ($i = 0, $ii = $query_result->num_rows, $records = array($ii); $i < $ii; $i++) {
                $records[$i] = $this->construct_record((object)$query_result->fetch_assoc(), $load_dependencies);
            }
        }
        return $records;
    }
}