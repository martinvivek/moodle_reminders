SELECT id, email, FROM_UNIXTIME(lastaccess) AS last_login
FROM mdl_user WHERE id = :teacher_id
