SELECT SQL_NO_CACHE
  mdl_forum_discussions.id,
  mdl_forum_discussions.name
FROM mdl_forum_discussions
  JOIN mdl_user ON mdl_user.id = :teacher_id
  JOIN mdl_course ON mdl_course.id = :course_id AND mdl_forum_discussions.course = mdl_course.id
GROUP BY mdl_forum_discussions.id
ORDER BY mdl_forum_discussions.timemodified DESC
LIMIT 5
