# CREATE CANDOR FUNCTION
# This function creates 1 unique number from two ids
DROP FUNCTION IF EXISTS CANDOR_PAIR;
CREATE FUNCTION CANDOR_PAIR (X INT,Y INT) RETURNS INT
  RETURN (X + Y) * (X + Y + 1) / 2 + Y;

SET @course_start_time = DATE('2015-1-1');
SET @course_start_slack_weeks = 8;
SET @weeks_in_semester = 120;

SELECT
  COUNT(DISTINCT mdl_logstore_standard_log.id) / COUNT(DISTINCT CANDOR_PAIR(mdl_logstore_standard_log.userid, courseid)) / @weeks_in_semester AS occurences,
  action
FROM mdl_logstore_standard_log

# Event needs to be triggered by a student
  INNER JOIN mdl_role_assignments ON mdl_role_assignments.roleid = 5 AND mdl_role_assignments.userid = mdl_logstore_standard_log.userid

# Event needs to occur within the context of a course that started near a specified date
  INNER JOIN mdl_course ON mdl_course.id = courseid AND FROM_UNIXTIME(startdate)
  BETWEEN (@course_start_time - INTERVAL @course_start_slack_weeks WEEK) AND (@course_start_time + INTERVAL @course_start_slack_weeks WEEK)

GROUP BY action ORDER BY occurences DESC;


