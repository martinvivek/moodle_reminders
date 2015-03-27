SELECT
  mdl_forum_discussions.id,
  mdl_forum_discussions.name,
  COUNT(DISTINCT mdl_forum_posts.id) AS post_count
FROM mdl_forum_discussions
  JOIN mdl_forum_posts ON mdl_forum_posts.discussion = mdl_forum_discussions.id
  LEFT JOIN mdl_logstore_standard_log ON mdl_logstore_standard_log.target = "discussion" AND
                                         mdl_logstore_standard_log.objectid = mdl_forum_discussions.id AND
                                         mdl_logstore_standard_log.userid = :teacher_id AND
                                         mdl_logstore_standard_log.timecreated > mdl_forum_discussions.timemodified
WHERE mdl_forum_discussions.course = :course_id AND mdl_logstore_standard_log.id IS NULL
GROUP BY mdl_forum_discussions.id ORDER BY mdl_forum_discussions.name