/*

   http://nestedloop.io/post/93248687529/migrating-from-mysql-to-redis#.VUs7_zeli1F

   How to use this script:


  Final Format:

  "ZADD" "{\"table\":\"logstore_standard_log\",\"userid\":\"5\",\"courseid\":\"4\",\"action\":\"viewed\"}" "1430987117" "{\"id\":9,\"eventname\":\"\\\\core\\\\event\\\\course_viewed\",\"objectid\":null}"
*/

SELECT ' FLUSHDB\n';
SELECT CONCAT(' HSET course:', courseid, ':last_access ', userid, ' ', timecreated, '\n'
' HINCRBY course:', courseid, ':viewed ', userid, ' ', 1, '\n')
FROM mdl_logstore_standard_log
WHERE action = 'viewed' AND FROM_UNIXTIME(timecreated) > NOW() - INTERVAL 1 YEAR;

# SET @save_frequency_in_records = 10000;
# SELECT CONCAT(' ZADD \"{\\\"table\\\":\\\"logstore_standard_log\\\",\\\"userid\\\":', userid,',\\\"courseid\\\":', courseid ,',\\\"action\\\":\\\"', action, '\\\"}\" ', timecreated, '  \"{\\\"id\\\":', id ,',\\\"eventname\\\":\\\"', REPLACE(eventname, '\\', '\\\\') ,'\\\",\\\"objectid\\\":', IFNULL(objectid, '0'),'}\"\n INCR logstore_standard_log:autoincrement\n', IF(MOD(id, @save_frequency_in_records) = 0, ' SAVE\n', '')) FROM mdl_logstore_standard_log;
