SELECT
  mdl_assign.name,
  COUNT(DISTINCT CASE WHEN mdl_assign_grades.id IS NOT NULL THEN NULL
                 ELSE mdl_assign_submission.id END) AS submission_count,
  FROM_UNIXTIME(mdl_assign.duedate)                 AS time_due,
  mdl_course_modules.id                             AS module_instance_id
FROM mdl_assign
  JOIN mdl_assign_submission
    ON mdl_assign_submission.assignment = mdl_assign.id AND mdl_assign_submission.status = 'submitted'
  JOIN mdl_course_modules ON mdl_course_modules.module = 1 AND mdl_course_modules.instance = mdl_assign.id
  LEFT JOIN mdl_assign_grades
    ON mdl_assign_grades.assignment = mdl_assign.id AND mdl_assign_grades.userid = mdl_assign_submission.userid AND
       mdl_assign_grades.attemptnumber >= mdl_assign_submission.attemptnumber
WHERE mdl_assign.course = :course_id AND mdl_assign_grades.id IS NULL
GROUP BY mdl_assign.id ORDER BY mdl_assign.name
