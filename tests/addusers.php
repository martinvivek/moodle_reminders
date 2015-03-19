<?php

require_once(__DIR__ . '/../../../config.php');

$raw_string = "
Adamidou Marilena, Y

Bell Shyainne

De Acetis Jerome

Du Yongjian

Eminalieva Leyla

Guillarmo Marwan

Ioannou Froso, A.

Kanaris Menelaos, N.

Kuntubayeva Ainura

Livshits Kirill

Mamedov Elvin

Nyamukapa Matifadza

Pippou Marina, A.

Pommarede Marc

Psaropoulou Myria, C.

Theodosi Marios

Thrasivoulou Filippos, P.

Violaris Kyriakos, C.

";

global $DB;

$i = 0;
foreach (explode("\n", str_replace(',', '', $raw_string)) as &$fullname) {
    $split = explode(' ', $fullname);
    if (sizeof($split) > 2) {
        $firstName = $split[1];
        $lastName = $split[0];

        $email = $lastName . '.' . $firstName[0] . '@unic.aa.cc';
        echo "
   INSERT INTO `moodle`.`mdl_user` (`id`, `auth`, `confirmed`, `policyagreed`, `deleted`, `suspended`, `mnethostid`, `username`, `password`, `idnumber`, `firstname`, `lastname`, `email`, `emailstop`, `icq`, `skype`, `yahoo`, `aim`, `msn`, `phone1`, `phone2`, `institution`, `department`, `address`, `city`, `country`, `lang`, `calendartype`, `theme`, `timezone`, `firstaccess`, `lastaccess`, `lastlogin`, `currentlogin`, `lastip`, `secret`, `picture`, `url`, `description`, `descriptionformat`, `mailformat`, `maildigest`, `maildisplay`, `autosubscribe`, `trackforums`, `timecreated`, `timemodified`, `trustbitmask`, `imagealt`, `lastnamephonetic`, `firstnamephonetic`, `middlename`, `alternatename`) VALUES (NULL, 'manual', '1', '0', '0', '0', '1', '".$firstName . $lastName."', '$2y$10$KY17v62EsqOLE/JZB/gm3u6ZNyf1gzC1SmUdsq7a2vTr.x6TLz/76', '', '".$firstName."', '".$lastName."', '".$email."', '0', '', '', '', '', '', '', '', '', '', '', '', '', 'en', 'gregorian', '', '99', '1417076484', '1426590552', '1426589797', '1426589813', '127.0.0.1', '', '0', '', '', '1', '1', '0', '2', '1', '0', '1417076351', '1417076476', '0', '', '', '', '', '');
    ";
    }
}
