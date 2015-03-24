SELECT
  mdl_course.fullname                             AS name,
  GROUP_CONCAT(DISTINCT mdl_user.id)              AS student_ids,
  GROUP_CONCAT(DISTINCT mdl_assign.id)            AS assignment_ids,
  FROM_UNIXTIME(MAX(last_accessed.timecreated))   AS last_accessed_date,
  GROUP_CONCAT(DISTINCT
               CASE WHEN mdl_logstore_standard_log.id IS NOT NULL THEN NULL
               ELSE mdl_forum_discussions.id END) AS discussion_ids
FROM mdl_course
  LEFT JOIN mdl_context ON contextlevel = 50 AND mdl_context.instanceid = mdl_course.id
  LEFT JOIN mdl_role_assignments ON roleid = 5 AND mdl_context.id = mdl_role_assignments.contextid
  LEFT JOIN mdl_user ON mdl_role_assignments.userid = mdl_user.id
  LEFT JOIN mdl_assign ON mdl_assign.course = mdl_course.id
  LEFT JOIN mdl_forum_discussions ON mdl_forum_discussions.course = mdl_course.id
  LEFT JOIN mdl_logstore_standard_log ON mdl_logstore_standard_log.target = 'discussion' AND
                                         mdl_logstore_standard_log.objectid = mdl_forum_discussions.id AND
                                         mdl_logstore_standard_log.userid = mdl_user.id AND
                                         mdl_logstore_standard_log.timecreated > mdl_forum_discussions.timemodified
  LEFT JOIN mdl_logstore_standard_log AS last_accessed
    ON last_accessed.userid = ? AND last_accessed.courseid = mdl_course.id
LIMIT 1;
