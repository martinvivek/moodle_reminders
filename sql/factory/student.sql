SELECT
  CONCAT_WS(' ', mdl_user.firstname, mdl_user.lastname) AS name,
  mdl_user.email                                        AS email,
  FROM_UNIXTIME(MAX(last_accessed.timecreated))         AS last_access_date,
  SUM(
      CASE mdl_logstore_standard_log.action
        WHEN 'submitted' THEN :submitted
        WHEN 'viewed' THEN :viewed
      ELSE 0 END)
  /* Has the same effect of only getting distinct rows */
  * COUNT(DISTINCT mdl_logstore_standard_log.id) / COUNT(mdl_logstore_standard_log.id) /
  /* We want the score per week */
  (DATEDIFF(NOW(), FROM_UNIXTIME(mdl_course.startdate)) / 7)
                                                        AS score
FROM mdl_user
  LEFT JOIN mdl_course ON mdl_course.id = :course_id
  LEFT JOIN mdl_logstore_standard_log
    ON mdl_logstore_standard_log.courseid = mdl_course.id AND mdl_logstore_standard_log.userid = mdl_user.id
  LEFT JOIN mdl_logstore_standard_log AS last_accessed
    ON last_accessed.userid = mdl_user.id AND last_accessed.courseid = mdl_course.id
LIMIT 1;
