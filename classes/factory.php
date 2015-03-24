<?php

define('FACTORY_QUERY_FOLDER', __DIR__ . '/../sql/factory/');

abstract class factory {
    /**
     * This function is called by other classes and triggers the custom 'construct_record' function
     * for each record found
     *
     * @param $file_name string The name of a file in the /sql/factory directory
     * @param $vars array() A map of parameters which will be replaced in the loaded sql file
     * These params should be prefixed with ':' in the file
     * Note: A param cannot occur more than once
     * @return array() Records found by the query
     */
    public function load_records($file_name, $vars = array()) {
        global $DB;
        // Load the sql query as a string
        $query_string = file_get_contents(FACTORY_QUERY_FOLDER . $file_name);

        // Execute the query and map each record with a custom defined 'construct_record' function
        return array_map(array($this, 'construct_record'), $DB->get_records_sql($query_string, $vars));
    }

    abstract protected function construct_record($row);
}