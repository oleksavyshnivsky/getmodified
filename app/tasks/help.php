<?php 

echo <<<DOC

ВИБІРКА ОСТАННІХ ДОДАНИХ/ЗМІНЕНИХ ФАЙЛІВ
> php index.php

Вибірка файлів, доданих/змінених після заданої дати  
> php index.php lastdate=2020-01-01T00:00:00+02:00

Вибірка файлів, доданих/змінених після дати X із файлу lastdate.txt (остання дата — 0)
> php index.php daten=X

Вибірка файлів у ZIP-файл
> php index.php zip

Вибірка файлів у ZIP-файл без копіювання у незапаковану директорію
> php index.php zip nocopy


ОСТАННІ x ДАТ З ФАЙЛУ lastdate.txt
> php index.php dates

Останні X дат з файлу lastdate.txt, де X — у проміжку від 1 до 10000:
> php index.php dates limit=X


ДАТА/ЧАС СТВОРЕННЯ/ЗМІНИ НАЙСВІЖІШИХ 10 ФАЙЛІВ ЗА ОСТАННІ 30 ДІБ
> php index.php getlastdate

Дата/час створення/зміни найсвіжіших X файлів за останні Y діб, де X і Y — у проміжку від 1 до 10000
> php index.php getlastdate limit=X days=Y


ВИСТАВЛЕННЯ НОВОГО ОПОРНОГО ЧАСУ БЕЗ ВИБІРКИ ФАЙЛІВ
> php index.php setlastdate
або
> php index.php setlastdate lastdate=2020-01-01T01:02:03+02:00


ДРУГА ГІЛКА
Усі попередні команди працюють з файлом "app/lastdate.txt". 
Щоб відбирати файли для іншого місця, потрібно додавати "x" після дії
> php index.php x
> php index.php [дія] x

DOC;
