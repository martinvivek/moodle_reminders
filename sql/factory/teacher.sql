SELECT
  DISTINCT mdl_user.id,
  mdl_user.email,
  FROM_UNIXTIME(mdl_user.lastaccess) AS last_login
FROM mdl_user
  JOIN mdl_role_assignments ON (roleid = 3 OR roleid = 4) AND mdl_role_assignments.userid = mdl_user.id
  JOIN mdl_context ON contextlevel = 50 AND mdl_context.id = mdl_role_assignments.contextid
  JOIN mdl_course ON mdl_course.id = mdl_context.instanceid AND mdl_course.visible = 1 AND mdl_course.format != 'site'
  JOIN mdl_user_preferences ON mdl_user_preferences.userid = mdl_user.id AND
                                     mdl_user_preferences.name =
                                     'message_provider_local_moodle_reminders_course_reports_loggedoff' AND
                                     mdl_user_preferences.value LIKE 'email'


