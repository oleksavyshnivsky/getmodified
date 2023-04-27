<?php

// ————————————————————————————————————————————————————————————————————————————————
// Нова опорна дата
// Якщо не задана — береться "зараз"
// ————————————————————————————————————————————————————————————————————————————————

$new_lastdate_txt = checkArgument('lastdate');
$new_lastdate_unix = strtotime($new_lastdate_txt) or $new_lastdate_unix = time();
$new_lastdate = date('c', $new_lastdate_unix);

// Запис
file_put_contents(LASTDATEFILE, PHP_EOL . $new_lastdate, FILE_APPEND | LOCK_EX);
echo 'Done', PHP_EOL;
