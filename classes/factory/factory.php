<?php

require_once(__DIR__ . '/../../logstore_cache.php');

define('FACTORY_QUERY_FOLDER', __DIR__ . '/../../sql/factory/');
define('UNESCAPED_VAR_PREFIX', '#SQL ');

abstract class factory {

    /**
     * Maps a row retrieved in an SQL query to an initialized object of the class type
     * @param $row object
     * @return object
     */
    abstract protected function construct_record($row);

    /**
     * This function triggers the custom 'construct_record' function for each record found to return an array of initialized objects produced by the factory
     *
     * @param $file_name string The name of a file in the /sql/factory directory
     * @param $vars array() A map or set of parameters which will be replaced in the loaded sql file
     * Param keys should be prefixed with ':' in the sql file if a map is used
     * If a set is used, '?'s should be put in place of the values
     * They may not occur more than once
     * @param $unescaped_vars array() Must be prefixed with UNESCAPED_VAR_PREFIX and may appear multiple times
     * @return array() Records found by the query
     */
    public function load_records($file_name, $vars = array(), $unescaped_vars = array()) {
        global $DB;

        $prefixed_unescaped_var_keys = array_map(function ($key) {
            return UNESCAPED_VAR_PREFIX . $key;
        }, array_keys($unescaped_vars));

        // Load the sql query as a string
        $query_string = file_get_contents(FACTORY_QUERY_FOLDER . $file_name);
        // Insert unescaped vars
        $query_string = str_replace($prefixed_unescaped_var_keys, array_values($unescaped_vars), $query_string);
        // Use the logstore cache instead of the actual logstore
        $query_string = str_replace('mdl_logstore_standard_log', LOGSTORE_CACHE_TABLE_NAME, $query_string);
        // Replace mdl_table with {table} for maximum compatibility
        // (we don't do this in the file so phpstorm hinting can load the actual tables from the database)
        $query_string = preg_replace('/mdl_(\w+)/','{$1}', $query_string);

        // Execute the query and map each record with a custom defined 'construct_record' function
        $records = $DB->get_records_sql($query_string, $vars);
        return array_map(array($this, 'construct_record'), $records);
    }
}