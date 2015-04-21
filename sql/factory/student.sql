SELECT
/* Prevent score inflation through rapid page refreshing
   By only counting a specific page view every 30 seconds */
  mdl_user.id                                           AS id,
  CONCAT_WS(' ', mdl_user.firstname, mdl_user.lastname) AS name,
  mdl_user.email                                        AS email,
  FROM_UNIXTIME(mdl_user.lastlogin)                     AS last_access_date,
  SUM(
      CASE mdl_logstore_standard_log.action
      #SQL action_cases
      ELSE 0 END)
  /* we multiply by the percentage of events that where created within 3 seconds distance of another */
  * (COUNT(DISTINCT ROUND(mdl_logstore_standard_log.timecreated / 3)) / COUNT(mdl_logstore_standard_log.id))
  /* We want the score per week */
  / (DATEDIFF(NOW(), FROM_UNIXTIME(mdl_course.startdate)) / 7)
                                                        AS score
FROM mdl_user
  JOIN mdl_course ON mdl_course.id = :course_id
  JOIN mdl_context ON mdl_context.contextlevel = 50 AND mdl_context.instanceid = mdl_course.id
# The student
  INNER JOIN mdl_role_assignments
    ON roleid = 5 AND mdl_user.id = mdl_role_assignments.userid AND mdl_role_assignments.contextid = mdl_context.id
  JOIN mdl_logstore_standard_log
    ON mdl_logstore_standard_log.courseid = mdl_course.id AND mdl_logstore_standard_log.userid = mdl_user.id

GROUP BY mdl_user.id
ORDER BY score, name
