# Moodle Reminders

_Send automatic email reminders to teachers_

## Installation

* `git clone https://github.com/Arubaruba/moodle-reminders.git <MOODLE_DIR>/local/moodle_reminders`
* In Browser: `http://localhost/MOODLE_LOCATION/admin/index.php?confirmplugincheck=1&cache=0`

_Note: Teachers are NOT subscribed by default._ 

## How to Subscribe all Teachers
```sql
INSERT IGNORE INTO mdl_user_preferences (id, userid, name, value)
SELECT null, userid,'message_provider_local_moodle_reminders_course_reports_loggedoff', 'email' FROM mdl_role_assignments WHERE roleid = 3 OR roleid = 4
```

## How to Unsubscribe all Teachers

```sql
DELETE FROM mdl_user_preferences
WHERE name = 'message_provider_local_moodle_reminders_course_reports_loggedoff'
```

## How to set the Email Language

_Administration > Site Administration > Language > Language Settings >  Default Language_

__Note:__ The language set here must also exist within the _lang_ directory of this project

