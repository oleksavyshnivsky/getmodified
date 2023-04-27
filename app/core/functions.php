<?php

// ————————————————————————————————————————————————————————————————————————————————
// Рекурсивне видалення вмісту директорії
// ————————————————————————————————————————————————————————————————————————————————
function removeDirectory($path) {
	$files = glob($path . '/*');
	foreach ($files as $file) is_dir($file) ? removeDirectory($file) : unlink($file);
	rmdir($path);
	return;
}


// ————————————————————————————————————————————————————————————————————————————————
// Очищене ім’я файлу
// ————————————————————————————————————————————————————————————————————————————————
function sanitizeFileName($file) {
	// Remove anything which isn't a word, whitespace, number
	// or any of the following caracters -_~,;:[]().
	$file = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).])", '', $file);
	// Remove any runs of periods (thanks falstro!)
	$file = preg_replace("([\.]{2,})", '', $file);

	return $file;
}

// ————————————————————————————————————————————————————————————————————————————————
// Аргументи
// ————————————————————————————————————————————————————————————————————————————————
function getArguments() {
	$args = [];
	if (PHP_SAPI == 'cli') {
		global $argv;
		$tmp = $argv;
		array_shift($tmp);
		foreach ($tmp as $arg) {
			$e = explode('=', $arg);
			$args[$e[0]] = count($e) == 2 ? $e[1] : false;	// Важливо: Не null
		}
	} else {
		$args = $_GET; 
	}
	return $args;
}


// ————————————————————————————————————————————————————————————————————————————————
// Перевірка значення аргументу
// ————————————————————————————————————————————————————————————————————————————————
function checkArgument($key) {
	global $args;
	return isset($args[$key]) ? $args[$key] : null;
}


// ————————————————————————————————————————————————————————————————————————————————
// Сортування файлів за часом створення/редагування
// ————————————————————————————————————————————————————————————————————————————————
function cmp($a, $b) {
	if ($a['time'] == $b['time']) return 0;
	return ($a['time'] < $b['time']) ? 1 : -1;
}

