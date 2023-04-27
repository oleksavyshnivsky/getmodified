<?php

// ————————————————————————————————————————————————————————————————————————————————
// Копіювання файлів, змінених після вказаної дати (чи дати останнього виконання цього завдання)
// ————————————————————————————————————————————————————————————————————————————————

// Ціль — директорія і/або zip
$copyToDir = checkArgument('nocopy') === null;
$copyToZIP = checkArgument('zip') !== null;
if (!$copyToDir and !$copyToZIP) exit('Нікуди копіювати файли.');

// Опорна дата (копіювати файли, змінені після неї)
$lastdate_txt = checkArgument('lastdate');
if (!$lastdate_txt) {
	$dates = array_reverse(file(LASTDATEFILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));

	$dateN = (int)checkArgument('daten');
	$lastdate_txt = isset($dates[$dateN]) ? $dates[$dateN] : (isset($dates[0]) ? $dates[0]: null);
}
$lastdate = strtotime($lastdate_txt);

// Нова опорна дата
$new_lastdate_unix = time();
$new_lastdate = date('c', $new_lastdate_unix);

// Директорія проекту
$path = realpath(SOURCE);

// ————————————————————————————————————————————————————————————————————————————————
// 
// ————————————————————————————————————————————————————————————————————————————————
if (!file_exists(TARGET) or !is_dir(TARGET)) mkdir(TARGET);
if (!file_exists(TARGET_ZIP) or !is_dir(TARGET_ZIP)) mkdir(TARGET_ZIP);

// ————————————————————————————————————————————————————————————————————————————————
// Очистка директорії результату
// ————————————————————————————————————————————————————————————————————————————————
foreach (new DirectoryIterator(TARGET) as $fileInfo) {
	if (!$fileInfo->isDot()) {
		$pathname = $fileInfo->getPathname();
		is_dir($pathname) ? removeDirectory($pathname) : unlink($pathname);
	}
}

// ————————————————————————————————————————————————————————————————————————————————
// Рекурсивний перебір директорій і файлів проекту
// ————————————————————————————————————————————————————————————————————————————————
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

// Створення ZIP-файлу
if ($copyToZIP) {
	$zipfilename = TARGET_ZIP.basename(realpath('..')).date('-Ymd-Hi').'.zip';
	$zip = new ZipArchive();
	if (!$zip->open($zipfilename, ZIPARCHIVE::CREATE)) exit('Помилка створення ZIP-файлу ' . $zipfilename);
}

// Лічильник скопійованих файлів
$counter = 0;

foreach ($iterator as $pathname => $fileInfo) {
	if (filemtime($pathname) > $lastdate or filectime($pathname) > $lastdate) {
		$subpath = $iterator->getSubPath();
		$basename = basename($pathname);

		// Пропускати файли з іменем як у EXCLUDE
		if (in_array($basename, EXCLUDE)) continue;

		// Створення піддиректорії, якщо потрібно
		if (!file_exists(TARGET.'/'.$subpath)) mkdir(TARGET.'/'.$subpath, 0777, true);

		// Повідомлення у консолі і лічильник
		echo $pathname, PHP_EOL;
		$counter++;

		// Дія
		if ($copyToDir) copy($pathname, TARGET.'/'.$subpath.'/'.$basename);
		if ($copyToZIP) $zip->addFile($pathname, $subpath.'/'.$basename);
	}
}

if ($copyToZIP) $zip->close();

// ————————————————————————————————————————————————————————————————————————————————
// Збереження останньої дати
// ————————————————————————————————————————————————————————————————————————————————
if ($counter) file_put_contents(LASTDATEFILE, PHP_EOL . $new_lastdate, FILE_APPEND | LOCK_EX);

// ————————————————————————————————————————————————————————————————————————————————
// 
// ————————————————————————————————————————————————————————————————————————————————
echo 'Done', PHP_EOL;
