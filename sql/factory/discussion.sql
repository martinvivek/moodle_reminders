SELECT SQL_NO_CACHE
  mdl_forum_discussions.id,
  mdl_forum_discussions.name,
  COUNT(DISTINCT mdl_forum_posts.id) AS post_count
FROM mdl_forum_discussions
  JOIN mdl_user ON mdl_user.id = :teacher_id
  JOIN mdl_course ON mdl_course.id = :course_id AND mdl_forum_discussions.course = mdl_course.id
  JOIN mdl_forum_posts ON
                         mdl_forum_posts.discussion = mdl_forum_discussions.id AND
                         mdl_forum_posts.userid != mdl_user.id
#                          AND
#                          !EXISTS(
#                              SELECT 1
#                              FROM mdl_logstore_standard_log
#                              WHERE
#                                mdl_logstore_standard_log.target = 'discussion' AND
#                                mdl_logstore_standard_log.objectid = mdl_forum_discussions.id AND
#                                mdl_logstore_standard_log.userid = mdl_user.id AND
#                                mdl_logstore_standard_log.courseid = mdl_course.id AND
#                                mdl_logstore_standard_log.timecreated > mdl_forum_discussions.timemodified
#                              LIMIT 1
#                          )
GROUP BY mdl_forum_discussions.id
ORDER BY mdl_forum_discussions.timemodified DESC
