<?php
# Создание таблицы tickets с билетами и рандомными комбинациями игроков.
# Колонки: id (автоинкремент),
#          ticket (номер билета, строка, при генерации писать туда случайные числа от 900000000001 до 900099999999),
#          combination (здесь храниться игровая комбинация),
#          count_guessed количество угаданных чисел в комбинации,
#          win_sum  сумма выигрыша комбинации

require_once dirname(__DIR__) . '\engine/function.php';
$db_config = require_once dirname(__DIR__) . '\config/db_config.php';
require_once dirname(__DIR__) . '\engine/Database.php';

/**
 * объект класса Database PDO подключение к базе данных
 */
$db = new Database($db_config);

# проверочка стартовых данных
/**
 * $arr_input_date - массив введенных данных из _POST
 */
$arr_input_date = validFill($_POST);

/**
 * $cnt_tickets - общее количество играющих билетов
 */
$lotto_cnt_tickets = $arr_input_date['cnt_tic'];

if (!isset($arr_input_date['lot']) || $lotto_cnt_tickets > 500000) {
    exit('Необходимо правильно заполнить все поля');
}

/**
 * $lotto_num_range - диапазон угадываемых чисел
 */
$lotto_num_range = $arr_input_date['lot'];

if ($lotto_num_range == 36) {
    /**
     * $lotto_cnt_guess_num - количество угадываемых чисел
     */
    $lotto_cnt_guess_num = 5;

    /**
     * $lotto_min_cnt_win - минимальное количество угаданных чисел для выигрыша
     */
    $lotto_min_cnt_win = 3;
} else {
    $lotto_cnt_guess_num = 6;
    $lotto_min_cnt_win = 4;
}

# тайминг начало
$start=gettimeofday();

/**
 * $arr_ticket - создаем массив с номерами билетов
 */
$arr_ticket = ticketNumbers($lotto_cnt_tickets);

# делаем запись массива номеров билетов и комбинаций игроков в файл для заливки его в БД
$fp = fopen('/file.txt', 'w');
foreach ($arr_ticket as $fields)
{
    /**
     * добавляем в строку файла комбинацию игрока
     * @param $lotto_cnt_guess_num, количество угадываемых чисел
     * @param $lotto_num_range - диапазон угадываемых чисел
     */
    $row = array($fields, implode(', ', combinationNumbers($lotto_cnt_guess_num, $lotto_num_range)));
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
$db_create_table =
    'CREATE TABLE `lotto`.`tickets` (
  `id` SERIAL PRIMARY KEY,
  `ticket` BIGINT  NULL,
  `combination` VARCHAR(50),
  `count_guessed` INT(2) DEFAULT 0,
  `win_sum` BIGINT(10) DEFAULT 0
  )';
$db->query($db_create_table);

# проверка на создание таблицы  ==============================================================

# заливаем в БД файл номеров билетов и комбинаций игроков
$db->query('USE `lotto`');
$db_import_file =
    'LOAD DATA  INFILE "/file.txt"
    INTO TABLE tickets
    FIELDS TERMINATED BY ","
    ENCLOSED BY \'"\'
    (ticket, combination)';
$db->query($db_import_file);
$db->query('ALTER TABLE `lotto`.`tickets` ADD INDEX (`combination`, `count_guessed`, `win_sum`)');
$db->closeConnection(); # закрываем соединение с базой

# удалили промежуточный файл
unlink('/file.txt');

# тайминг конец
$end=gettimeofday();
$total_time = (float)($end['sec'] - $start['sec']);















