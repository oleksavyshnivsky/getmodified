<?php

// ————————————————————————————————————————————————————————————————————————————————
// Дати зміни останніх 10 ($limit) файлів (якщо вони були не більше місяця тому)
// ————————————————————————————————————————————————————————————————————————————————

$toplimit = checkArgument('limit');
$toplimit = filter_var($toplimit, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1,'max_range'=>1000,'default'=>10]]);

$daylimit = checkArgument('days');
$daylimit = filter_var($daylimit, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1,'max_range'=>1000,'default'=>30]]);

// Обмеження давності 
$timelimit = time() - 86400 * $daylimit;

//  Масив часів
$times = [];

// Директорія проекту
$path = realpath(SOURCE);

// Рекурсивний перебір директорій і файлів проекту
/**
 * @param SplFileInfo $file
 * @param mixed $key
 * @param RecursiveCallbackFilterIterator $iterator
 * @return bool True if you need to recurse or if the item is acceptable
 */
$filter = function ($file, $key, $iterator) {
	if ($iterator->hasChildren() && !in_array($file->getFilename(), EXCLUDE)) return true;
	return $file->isFile();
};

$innerIterator = new RecursiveDirectoryIterator(
	$path,
	RecursiveDirectoryIterator::SKIP_DOTS
);
$iterator = new RecursiveIteratorIterator(
	new RecursiveCallbackFilterIterator($innerIterator, $filter)
);

// Час створення/редагування
foreach ($iterator as $pathname => $fileInfo) {
	if (filemtime($pathname) > $timelimit) 		$times[] = ['file' => $pathname, 'time' => filemtime($pathname)];
	elseif (filectime($pathname) > $timelimit) 	$times[] = ['file' => $pathname, 'time' => filectime($pathname)];
}

// Сортування і обрізання масиву на вивід
usort($times, 'cmp');
$times = array_slice($times, 0, $toplimit);

// ————————————————————————————————————————————————————————————————————————————————
// Вивід результату
// ————————————————————————————————————————————————————————————————————————————————
foreach ($times as $row) echo date('c', $row['time']), ' — ', $row['file'], PHP_EOL;

