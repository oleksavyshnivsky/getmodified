<?php
/**
 * Копіювання змінених файлів
 * 
 * @author ODE <dying.escape@gmail.com>
 * @copyright 2020
 */


// ————————————————————————————————————————————————————————————————————————————————
// 
// ————————————————————————————————————————————————————————————————————————————————
chdir(__DIR__);
header('Content-Type: text/plain; charset=utf-8');
date_default_timezone_set(in_array('Europe/Kyiv', DateTimeZone::listIdentifiers())
	? 'Europe/Kyiv' : 'Europe/Kiev');

// ————————————————————————————————————————————————————————————————————————————————
// Конфіг
// ————————————————————————————————————————————————————————————————————————————————
define('DIR_BASE', basename(__DIR__));
include_once 'app/config.php';

// ————————————————————————————————————————————————————————————————————————————————
// Функції
// ————————————————————————————————————————————————————————————————————————————————
include_once 'app/core/functions.php';

// ————————————————————————————————————————————————————————————————————————————————
// Вибір завдання
// ————————————————————————————————————————————————————————————————————————————————
if (PHP_SAPI == 'cli')	$task = $argc > 1 ? (strpos($argv[1], '=') === false ? $argv[1] : 'main') : 'main';
else 					$task = filter_input(INPUT_GET, 'task');

switch ($task) {
	case '?': $task = 'help'; break;
	case 'x':
	case 'nocopy':
	case 'zip': $task = 'main'; break;
	default: $task = sanitizeFileName($task);
}


// ————————————————————————————————————————————————————————————————————————————————
// Аргументи
// ————————————————————————————————————————————————————————————————————————————————
$args = getArguments();

// Вибрана гілка
$branch = 'lastdate'.(checkArgument('x')!==null?'x':'');

// Файл з датами
define('LASTDATEFILE', 'app/'.$branch.'.txt');
if (!file_exists(LASTDATEFILE)) touch(LASTDATEFILE);

// ————————————————————————————————————————————————————————————————————————————————
// Перехід до завдання
// ————————————————————————————————————————————————————————————————————————————————
if (!file_exists('app/tasks/'.$task.'.php')) exit('No such file.');

include 'app/tasks/'.$task.'.php';
