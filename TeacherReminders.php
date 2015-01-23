<?php

require_once(__DIR__ . '/MoodleEnv.php');
require_once(__DIR__ . '/Teacher.php');

class TeacherReminders {
  function __construct() {
    $teacher_rows = $this->load_teacher_rows();
    $teachers = array_map($this->map_teachers, $teacher_rows);
    $this->send_emails($teachers);
  }

  private function map_teachers($index, $teacher_row) {
    return new Teacher($teacher_row['id'], $teacher_row['email'], explode(',', $teacher_row['course_ids']));
  }

  private function load_teacher_rows() {
    global $DB;
    return array_values($DB->get_records_sql('
      SELECT {user}.id, email, GROUP_CONCAT(DISTINCT {course}.id ORDER BY {course}.fullname) as course_ids FROM {user}
      LEFT JOIN {role_assignments} ON (roleid = 3 OR roleid = 4) AND {role_assignments}.userid = {user}.id
      LEFT JOIN {context} ON contextlevel = 50 AND {context}.id = {role_assignments}.contextid
      LEFT JOIN {course} ON {course}.id = {context}.instanceid AND {course}.visible = 1 AND {course}.format != "site"
      WHERE {course}.id IS NOT NULL
      GROUP BY {user}.id
    '));
  }

  private function send_emails($teachers) {
    // TODO
  }
}
