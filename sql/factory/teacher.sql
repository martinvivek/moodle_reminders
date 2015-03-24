SELECT
  mdl_user.id,
  mdl_user.email,
  FROM_UNIXTIME(mdl_user.lastaccess) AS last_login
FROM mdl_user
  INNER JOIN mdl_role_assignments ON (roleid = 3 OR roleid = 4) AND mdl_role_assignments.userid = mdl_user.id
  LEFT JOIN mdl_context ON contextlevel = 50 AND mdl_context.id = mdl_role_assignments.contextid
  LEFT JOIN mdl_course
    ON mdl_course.id = mdl_context.instanceid AND mdl_course.visible = 1 AND mdl_course.format != 'site'
  LEFT JOIN mdl_user_preferences ON mdl_user_preferences.userid = mdl_user.id AND mdl_user_preferences.name =
                                                                                  'message_provider_local_moodle_reminders_course_reports_loggedoff'
WHERE mdl_course.id IS NOT NULL AND mdl_user_preferences.value LIKE 'email' IS NOT NULL
GROUP BY mdl_user.id
