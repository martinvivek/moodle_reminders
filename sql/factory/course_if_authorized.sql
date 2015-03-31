SELECT
  mdl_course.id                                             AS id,
  mdl_course.fullname                                       AS name
FROM mdl_course
  JOIN mdl_role_assignments ON (roleid = 3 OR roleid = 4) AND mdl_role_assignments.userid = :teacher_id
  JOIN mdl_context
    ON contextlevel = 50 AND mdl_context.instanceid = mdl_course.id AND mdl_context.id = mdl_role_assignments.contextid
WHERE mdl_course.id = :course_id LIMIT 1;
