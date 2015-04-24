# Moodle Reminders

_Send automatic email reminders to teachers concerning the performance of their students and course items in need of their attention_

__Note:__ Teachers are NOT subscribed by default. No emails will be sent automatically without enabling them. See: _Enabling Weekly Emails_

## Installation
* `cd MOODLE_DIR/local`
* `git clone https://github.com/Arubaruba/moodle-reminders.git`
* `cd moodle_reminders`
* `mkdir twig_cache`
* `chmod 666 twig_cache`
* In the browser: `MOODLE_URL/admin/index.php?confirmplugincheck=1&cache=0`

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
