<?php

require_once(__DIR__ . '/MoodleEnv.php');

/**
 * Stores basic teacher info
 */
class Teacher {
  /**
   * @var integer $id Id of teacher in database (mdl_user.id)
   * @var string $email Teacher's email address
   * @var array(Course) $courses All of the courses the teacher is currently teaching
   */
  public
    $id,
    $email,
    $courses;

  /**
   * @param integer $id
   * @param string $email
   * @param array $course_ids
   */
  function __constructor($id, $email, $course_ids) {
    global $DB;
    $this->id = $id;
    $this->email= $email;
    $this->courses = $DB->get_records('SELECT 1');
//    $this->courses = $course_ids;
  }
}