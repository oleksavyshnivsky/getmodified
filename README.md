# Призначення

Копіювання файлів, які були додані/змінені після певної дати, у нову директорію і/або у ZIP-архів.

# Завдання

## Вибірка останніх доданих/змінених файлів

> php index.php

Вибірка файлів, доданих/змінених після заданої дати  
> php index.php lastdate=2020-01-01T00:00:00+02:00

Вибірка файлів, доданих/змінених після дати X із файлу lastdate.txt (остання дата — 0)
> php index.php daten=X

Вибірка файлів у ZIP-файл

> php index.php zip

Вибірка файлів у ZIP-файл без копіювання у незапаковану директорію
> php index.php zip nocopy

## Останні X дат з файлу lastdate.txt

> php index.php dates

Останні X дат з файлу lastdate.txt, де X — у проміжку від 1 до 10000:
> php index.php dates limit=X

## Дата/час створення/зміни найсвіжіших 10 файлів за останні 30 діб

> php index.php getlastdate

Дата/час створення/зміни найсвіжіших X файлів за останні Y діб, де X і Y — у проміжку від 1 до 10000
> php index.php getlastdate limit=X days=Y

## Виставлення нового опорного часу без вибірки файлів

> php index.php setlastdate
> або
> php index.php setlastdate lastdate=2020-01-01T01:02:03+02:00

## ДРУГА ГІЛКА

Усі попередні команди працюють з файлом *app/lastdate.txt*. 
Щоб відбирати файли для іншого місця (працювати з файлом *app/lastdatex.txt*), потрібно додавати "x" після дії

> php index.php x
> php index.php [дія] x

# Файли проекту

- app/core/functions.php
- app/tasks/dates.php
- app/tasks/getlastdate.php
- app/tasks/help.php
- app/tasks/main.php
- app/tasks/setlastdate.php
- app/config.php
- app/lastdate.txt
- app/lastdatex.txt
- index.php

# config.php 

Повинен містити такі значення:

- SOURCE — директорія, звідки потрібно вибирати файли
- TARGET — директорія, куди потрібно закидати файли
- TARGET_ZIP — Директорія, куди потрібно закидати ZIP-файл + основа імені ZIP-файлу (додати "-" в кінці для краси)
- EXCLUDE — масив імен файлів і директорій, які потрібно пропускати

