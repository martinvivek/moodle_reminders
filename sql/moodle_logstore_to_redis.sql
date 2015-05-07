/*

   http://nestedloop.io/post/93248687529/migrating-from-mysql-to-redis#.VUs7_zeli1F

   How to use this script:


  Final Format:

  "ZADD" "{\"table\":\"logstore_standard_log\",\"userid\":\"5\",\"courseid\":\"4\",\"action\":\"viewed\"}" "1430987117" "{\"id\":9,\"eventname\":\"\\\\core\\\\event\\\\course_viewed\",\"objectid\":null}"
*/

SELECT CONCAT(' ZADD \"{\\\"table\\\":\\\"logstore_standard_log\\\",\\\"userid\\\":', userid,',\\\"courseid\\\":', courseid ,',\\\"action\\\":\\\"', action, '\\\"}\" ', timecreated, '  \"{\\\"id\\\":', id ,',\\\"eventname\\\":\\\"', REPLACE(eventname, '\\', '\\\\') ,'\\\",\\\"objectid\\\":', IFNULL(objectid, '0'),'}\"\n') FROM mdl_logstore_standard_log;
