<?php

/**
 * Додавання нового проєкту
 */

// ————————————————————————————————————————————————————————————————————————————————
// Команда виклику
// ————————————————————————————————————————————————————————————————————————————————
$tpl = 'php index.php makeproject name={name} source={source} [target={target}] [ziptarget={ziptarget}]';

// ————————————————————————————————————————————————————————————————————————————————
// Атрибути
// ————————————————————————————————————————————————————————————————————————————————
$name = checkArgument('name');
$source = checkArgument('source');
$target = checkArgument('target');
$ziptarget = checkArgument('ziptarget');

if (!$name)
	$stop = 'Не задана назва проєкту';
elseif (file_exists('config/'.$name))
	$stop = 'Такий проєкт уже існує';
elseif (!$source)
	$stop = 'Не задане джерело';
elseif (!file_exists($source))
	$stop = 'Недоступний шлях до джерела';
elseif (!$target and !$ziptarget)
	$stop = 'Не задана директорія для результату';
elseif ((!$target or !file_exists($target)) and (!$ziptarget or !file_exists($ziptarget)))
	$stop = 'Недоступний шлях до директорії для результату';

if (isset($stop)) {
	error($stop, false);
	exit('Приклад виклику: '.$tpl.PHP_EOL);
}

if ($source) $source = rtrim($source, '/').'/';
if ($target) $target = rtrim($target, '/').'/';
if ($ziptarget) $ziptarget = rtrim($ziptarget, '/').'/';

// ————————————————————————————————————————————————————————————————————————————————
// Створити конфіг-директорію
// ————————————————————————————————————————————————————————————————————————————————
$dir_config = 'config/'.$name;
mkdir($dir_config) or error('Не вдалося створити директорію '.$dir_config);

// ————————————————————————————————————————————————————————————————————————————————
// Створити конфіг
// ————————————————————————————————————————————————————————————————————————————————
$config = file_get_contents('app/config.example.php');
if ($source) $config = str_replace("const SOURCE = '..';", "const SOURCE = '{$source}';", $config); 
if ($target) $config = str_replace("const TARGET = './NEW/';", "const TARGET = '{$target}';", $config); 
if ($ziptarget) $config = str_replace("const TARGET_ZIP = './ZIP/';", "const TARGET_ZIP = '{$ziptarget}';", $config); 
file_put_contents($dir_config.'/config.php', $config);

// ————————————————————————————————————————————————————————————————————————————————
// Створити файл для дат
// ————————————————————————————————————————————————————————————————————————————————
touch($dir_config.'/lastdate.txt');

// ————————————————————————————————————————————————————————————————————————————————
// 
// ————————————————————————————————————————————————————————————————————————————————
success('Виконано');
