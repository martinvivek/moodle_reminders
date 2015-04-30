SELECT
  mdl_course.id               AS id,
  mdl_course.fullname         AS name,
# Teacher id will not be stored in course but will be used in the factories of the course's dependencies
  mdl_role_assignments.userid AS teacher_id
FROM mdl_course
# Make sure the teacher is teaching this course
  JOIN mdl_role_assignments ON (roleid = 3 OR roleid = 4) AND mdl_role_assignments.userid = :teacher_id
  JOIN mdl_context
    ON contextlevel = 50 AND mdl_context.instanceid = mdl_course.id AND mdl_context.id = mdl_role_assignments.contextid
WHERE mdl_course.visible = 1 AND mdl_course.format != 'site'
ORDER BY mdl_course.fullname
