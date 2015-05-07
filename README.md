# Moodle Reminders

_Send automatic email reminders to teachers concerning the performance of their students and course items in need of their attention_

__Note:__ Teachers are NOT subscribed by default. No emails will be sent automatically without enabling them. See: _Enabling Weekly Emails_

## Installation

### Clone Repository into _local_ Moodle Directory and Install
* `cd MOODLE_DIR/local`
* `git clone https://github.com/Arubaruba/moodle-reminders.git`
* `cd moodle_reminders`
* In the browser: `MOODLE_URL/admin/index.php?confirmplugincheck=1&cache=0`

### Set up Redis
* `sudo apt-get install redis-server redis-cli`
* Generate secure password `openssl rand -base64 32`
* Add to _/etc/redis/redis.conf_: `requirepass "generated_password"`
* Create _redis_password.php_ (in _MOODLE_DIR/local/moodle_reminders_)
``` php
<php? define('LOCAL_MOODLE_REMINDERS_REDIS_PASSWORD', 'generated_password');
```
* Import moodle logstore to redis `mysql -u root -p moodle_table_name --skip-column-names --raw < sql/moodle_logstore_to_redis.sql | redis-cli --pipe`

### Set Correct Permissions for Twig Cache
* `mkdir twig_cache`
* `chmod 666 twig_cache`


## Preview Emails in Browser
* Log in as a teacher
* In browser: `MOODLE_URL/local/moodle_reminders/web_view.php`

## Enabling Weekly Emails
* In the browser: `MOODLE_URL/local/moodle_reminders/admin.php`
* Click _Subscribe All_
* __Done!__ All teachers on this Moodle installation should now get reminder emails. These are sent weekly when the moodle cron is run. 

### Disabling Weekly Emails
* In the browser: `MOODLE_URL/local/moodle_reminders/admin.php`
* Click _Unsubscribe All_
