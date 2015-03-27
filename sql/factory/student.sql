SELECT
  mdl_user.id                                               AS id,
  CONCAT_WS(' ', mdl_user.firstname, mdl_user.lastname)     AS name,
  mdl_user.email                                            AS email,
  FROM_UNIXTIME(MAX(mdl_logstore_standard_log.timecreated)) AS last_access_date,
  SUM(
      CASE mdl_logstore_standard_log.action
      #SQL action_cases
      ELSE 0 END)
  /* Has the same effect of only getting distinct rows */
  * COUNT(DISTINCT mdl_logstore_standard_log.id) / COUNT(mdl_logstore_standard_log.id) /
  /* We want the score per week */
  (DATEDIFF(NOW(), FROM_UNIXTIME(mdl_course.startdate)) / 7)
                                                            AS score
FROM mdl_user
  JOIN mdl_course ON mdl_course.id = :course_id
  JOIN mdl_context ON mdl_context.contextlevel = 50 AND mdl_context.instanceid = mdl_course.id
# The student
  JOIN mdl_role_assignments
    ON roleid = 5 AND mdl_user.id = mdl_role_assignments.userid AND mdl_role_assignments.contextid = mdl_context.id
  LEFT JOIN mdl_logstore_standard_log
    ON mdl_logstore_standard_log.courseid = mdl_course.id AND mdl_logstore_standard_log.userid = mdl_user.id

# Prevents a bug which causes MAX(logstore.timecreated) to reduce the number of records found to 1
GROUP BY mdl_user.id ORDER BY score, name
