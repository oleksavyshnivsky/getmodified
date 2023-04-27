<?php

// ————————————————————————————————————————————————————————————————————————————————
// Показ останніх 10 ($limit) дат з файлу lastdate.txt
// ————————————————————————————————————————————————————————————————————————————————

$start = checkArgument('start');
$start = filter_var($start, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1,'max_range'=>1000,'default'=>0]]);

$limit = checkArgument('limit');
$limit = filter_var($limit, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1,'max_range'=>1000,'default'=>10]]);

$dates = array_reverse(file(LASTDATEFILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
$dates = array_slice($dates, $start, $limit);

// foreach ($dates as $key => $value) echo $key + 1, "\t", $value, PHP_EOL;
echo implode(PHP_EOL, $dates);
