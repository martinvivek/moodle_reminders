SELECT
  mdl_course.id                                             AS id,
  mdl_course.fullname                                       AS name,
  FROM_UNIXTIME(MAX(mdl_logstore_standard_log.timecreated)) AS last_accessed_date,
# Teacher id will not be stored in course but will be used in the factories of the course's dependencies
  mdl_role_assignments.userid                               AS teacher_id
FROM mdl_course
# Make sure the teacher is teaching this course
  JOIN mdl_role_assignments ON (roleid = 3 OR roleid = 4) AND mdl_role_assignments.userid = :teacher_id
  JOIN mdl_context
    ON contextlevel = 50 AND mdl_context.instanceid = mdl_course.id AND mdl_context.id = mdl_role_assignments.contextid
# LEFT JOIN instead of JOIN to also show courses the teacher has never accessed
  LEFT JOIN mdl_logstore_standard_log ON mdl_logstore_standard_log.userid = mdl_role_assignments.userid AND
                                         mdl_logstore_standard_log.courseid = mdl_course.id
WHERE mdl_course.visible = 1 AND mdl_course.format != 'site'
# Prevents a bug which causes MAX(logstore.timecreated) to reduce the number of records found to 1
GROUP BY mdl_course.id
ORDER BY mdl_course.fullname
