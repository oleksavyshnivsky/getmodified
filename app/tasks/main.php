<?php

// ————————————————————————————————————————————————————————————————————————————————
// Копіювання файлів, змінених після вказаної дати (чи дати останнього виконання цього завдання)
// ————————————————————————————————————————————————————————————————————————————————

// Ціль — директорія і/або zip
$copyToDir = !array_key_exists('nocopy', $args);
$copyToZIP = array_key_exists('zip', $args);
if (!$copyToDir and !$copyToZIP) exit('Нікуди копіювати файли.');

// Опорна дата (копіювати файли, змінені після неї)
$lastdate_txt = checkArgument('lastdate');
if (!$lastdate_txt) {
	$dates = array_reverse(file(LASTDATEFILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));

	$dateN = (int)checkArgument('daten');
	$lastdate_txt = isset($dates[$dateN]) ? $dates[$dateN] : (isset($dates[0]) ? $dates[0]: null);
}
$lastdate = strtotime($lastdate_txt??'');

// Нова опорна дата
$new_lastdate_unix = time();
$new_lastdate = date('c', $new_lastdate_unix);

// Директорія проекту
$path = realpath(SOURCE);

// ————————————————————————————————————————————————————————————————————————————————
// 
// ————————————————————————————————————————————————————————————————————————————————
if ($copyToDir and (!file_exists(TARGET) or !is_dir(TARGET))) mkdir(TARGET);
if ($copyToZIP and (!file_exists(TARGET_ZIP) or !is_dir(TARGET_ZIP))) mkdir(TARGET_ZIP);

// ————————————————————————————————————————————————————————————————————————————————
// Очистка директорії результату
// ————————————————————————————————————————————————————————————————————————————————
if ($copyToDir) foreach (new DirectoryIterator(TARGET) as $fileInfo) {
	if (!$fileInfo->isDot()) {
		$pathname = $fileInfo->getPathname();
		is_dir($pathname) ? removeDirectory($pathname) : unlink($pathname);
	}
}

// ————————————————————————————————————————————————————————————————————————————————
// Read .gitignore file, if exists
// ————————————————————————————————————————————————————————————————————————————————
$gitIgnorePatterns = EXCLUDE;
$gitIgnoreFile = SOURCE . '/.gitignore';
if (file_exists($gitIgnoreFile)) {
	$gitIgnorePatterns = file($gitIgnoreFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	$gitIgnorePatterns = array_map('trim', $gitIgnorePatterns);
}
if (in_array(DIR_BASE, $gitIgnorePatterns)) $gitIgnorePatterns[] = DIR_BASE;
if (in_array('.git', $gitIgnorePatterns)) $gitIgnorePatterns[] = '.git';

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
	if ($iterator->hasChildren()) {
		global $gitIgnorePatterns;

		$subpath = str_replace('\\', '/', $iterator->getSubPath()); // Виправлення шляху для Linux
		$basename = basename($file);
		$subpath_basename = ($subpath?$subpath.'/':'').$basename;

		$use = true;
		foreach ($gitIgnorePatterns as $pattern) {
			if (fnmatch($pattern, $subpath_basename) or fnmatch($pattern, $subpath_basename.'/')) {
				$use = false;
				break;
			}
		}

		 // && !in_array($file->getFilename(), EXCLUDE)
		return $use;
	}
	return $file->isFile();
};

$innerIterator = new RecursiveDirectoryIterator(
	$path,
	RecursiveDirectoryIterator::SKIP_DOTS
);
$iterator = new RecursiveIteratorIterator(
	new RecursiveCallbackFilterIterator($innerIterator, $filter)
	, RecursiveIteratorIterator::SELF_FIRST
);

// Створення ZIP-файлу
if ($copyToZIP) {
	$zipfilename = TARGET_ZIP.basename(realpath(SOURCE)).date('-Ymd-Hi').'.zip';
	$zip = new ZipArchive();
	if (!$zip->open($zipfilename, ZIPARCHIVE::CREATE)) exit('Помилка створення ZIP-файлу ' . $zipfilename);
}

// Лічильник скопійованих файлів
$counter = 0;

foreach ($iterator as $pathname => $fileInfo) {
	if (!$fileInfo->isFile()) continue;

	if (filemtime($pathname) > $lastdate or filectime($pathname) > $lastdate) {
		$subpath = str_replace('\\', '/', $iterator->getSubPath()); // Виправлення шляху для Linux
		$basename = basename($pathname);
		$subpath_basename = ($subpath?$subpath.'/':'').$basename;

		// Пропускати файли з іменем як у EXCLUDE
		// if (in_array($basename, EXCLUDE)) continue;
		$use = true;
		foreach ($gitIgnorePatterns as $pattern) {
			if (fnmatch($pattern, $subpath_basename)) {
				$use = false;
				break;
			}
		}
		if (!$use) continue;

		// Створення піддиректорії, якщо потрібно
		if ($copyToDir and !file_exists(TARGET.'/'.$subpath)) mkdir(TARGET.'/'.$subpath, 0777, true);

		// Повідомлення у консолі і лічильник
		echo $pathname, PHP_EOL;
		$counter++;

		// Дія
		if ($copyToDir) copy($pathname, TARGET.'/'.$subpath_basename);
		if ($copyToZIP) $zip->addFile($pathname, $subpath_basename);
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

