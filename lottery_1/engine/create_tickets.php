<?php
# Создание таблицы tickets с билетами и рандомными комбинациями игроков.
# Колонки: id (автоинкремент),
#          ticket (номер билета, строка, при генерации писать туда случайные числа от 900000000001 до 900099999999),
#          combination (здесь храниться игровая комбинация),
#          count_guessed количество угаданных чисел в комбинации,
#          win_sum  сумма выигрыша комбинации


$config = require_once dirname(__DIR__) . '\config/db_config.php';
require_once dirname(__DIR__) . '\engine/Database.php';
$db = new Database($config);
require_once dirname(__DIR__) . '\engine/function.php';

# проверочка стартовых данных
$input_date = validFill($_POST);
$cntGuessOption = $input_date['cnt_tic'];
if (!isset($input_date['lot']) || $cntGuessOption > 500000) {
    exit('Необходимо правильно заполнить все поля да да ');
}

$cntNumbers = $input_date['lot'];  # из какого количества чисел угадываем
if ($cntNumbers == 36) {
    $cntGuessNumbers = 5;        # количество угадываемых чисел
    $maxCntWinNumbers = 3;       # минимальное количество чисел для выигрыша
} else {
    $cntGuessNumbers = 6;        # количество угадываемых чисел
    $maxCntWinNumbers = 4;       # минимальное количество чисел для выигрыша
}

$start=gettimeofday();     # тайминг

$arrayTicket = ticketNumbers($cntGuessOption);   // создаем массив с номерами билетов

# делаем запись массива номеров билетов и комбинаций игроков в файл для заливки его в БД
$fp = fopen('/file.txt', 'w');
foreach ($arrayTicket as $fields)
{
    $row = array($fields, implode(', ', combinationNumbers($cntGuessNumbers, $cntNumbers)));
    fputcsv($fp, $row);
}
fclose($fp);

//------------------------------------------------------------------------------------------------------
# создаём базу
$db->query('DROP DATABASE IF EXISTS `lotto`');
$db->query('CREATE DATABASE `lotto`');
$db->query('DROP TABLE IF EXISTS `lotto`.`tickets`');

// проверка на создание базы ==================================================================

# создаём табличку
$create_table =
    'CREATE TABLE `lotto`.`tickets` (
  `id` SERIAL PRIMARY KEY,
  `ticket` BIGINT  NULL,
  `combination` VARCHAR(50),
  `count_guessed` INT(2) DEFAULT 0,
  `win_sum` BIGINT(10) DEFAULT 0
  )';
$db->query($create_table);

# проверка на создание таблицы  ==============================================================

# заливаем в БД файл номеров билетов и комбинаций игроков
$db->query('USE `lotto`');
$import_file_db =
    'LOAD DATA  INFILE "/file.txt"
    INTO TABLE tickets
    FIELDS TERMINATED BY ","
    ENCLOSED BY \'"\'
    (ticket, combination)';
$db->query($import_file_db);
$db->query('ALTER TABLE `lotto`.`tickets` ADD INDEX (`combination`, `count_guessed`, `win_sum`)');
$db->closeConnection(); # закрываем соединение с базой

unlink('/file.txt');  # удалили промежуточный файл

$end=gettimeofday();
$total_time = (float)($end['sec'] - $start['sec']);















