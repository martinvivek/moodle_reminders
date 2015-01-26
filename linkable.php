<?php

/**
 * Extending this class signals that a class instance can generate a link to the object's moodle page
 */
abstract class linkable {
    /**
     * @return string A link to this object's page in moodle; Note that a login may be required
     */
    abstract function get_link();
}