<?php

require_once dirname(__DIR__) . '\engine/function.php';
$db_config = require_once dirname(__DIR__) . '\config/db_config.php';
require_once dirname(__DIR__) . '\engine/Database.php';

/**
 * объект класса Database PDO подключение к базе данных
 */
$db = new Database($db_config);

$row = $db->query('SELECT `combination` FROM `lotto`.`tickets` LIMIT 1');
/**
 * $lotto_cnt_guess_num - количество угадываемых чисел получаем из имеющийся базы
 */
$lotto_cnt_guess_num = count(explode(', ', $row[0]['combination']));

$lotto_min_cnt_win = 3;
if ($lotto_cnt_guess_num == 5){
    /**
     * $lotto_cnt_guess_num - количество угадываемых чисел
     */
    $lotto_num_range = 36;

    /**
     * $lotto_min_cnt_win - минимальное количество угаданных чисел для выигрыша
     */
    $lotto_min_cnt_win = 3;
} else {
    $lotto_num_range = 45;
    $lotto_min_cnt_win = 4;
}

# ------------------------------------------------------------------------------------------------------------
# делаем проверку на ввод данных розыгрыша и разбиваем на два массива: комбинация и суммы
/**
 * $arr_input_date - массив введенных данных из _POST
 */
$arr_input_date = validFill($_POST);

$arr_win_comb = [];
$arr_win_sum = [];

# Разбиваем _POST на комбинацию и суммы.
foreach ($arr_input_date as $key => $value)
{
    if($key <= $lotto_cnt_guess_num) {
        if (strlen($value) == 1) {
            $value = '0' . $value;
        }
        $arr_win_comb[$key] = $value;
    } else {
        $arr_win_sum[$key - $lotto_min_cnt_win][0] = $value;
    }
}

# Комбинацию проверяем на диапазон и совпадения чисел в кмбинации.
foreach ($arr_win_comb as $val)
{
    if($val > $lotto_num_range || $lotto_cnt_guess_num > count(array_unique($arr_win_comb))){
        exit('Числа в комбинации должны быть диапазоне от 1 до ' . $lotto_num_range . ' без совпадений');
    }
}
sort($arr_win_comb);

# ------------------------------------------------------------------------------------------------------------
# вносим изменения в БД
# тайминг начало
$start=gettimeofday();

# сбрасываем на DEFAULT столбцы count_guessed и win_sum
$db->query('UPDATE `lotto`.`tickets` SET `count_guessed` = DEFAULT, `win_sum` = DEFAULT');

# вносим в столбец count_guessed количество угаданных чисел в комбинации
foreach ($arr_win_comb as $value) {
    $db_query =
        "UPDATE `lotto`.`tickets` SET `count_guessed` = (`count_guessed` + 1) WHERE `combination` LIKE '%{$value}%'";
    $db->query($db_query);
}

# вносим в базу в столбец win_sum суммы выигрышей
foreach ($arr_win_sum as $key => $value)
{
    $db_query =
        'UPDATE `lotto`.`tickets` SET `win_sum` = ' . $arr_win_sum[$key][0] . ' where `lotto`.`tickets`.`count_guessed` = ' . $key;
    $db->query($db_query);
}

# ------------------------------------------------------------------------------------------------------------
# выводим резудьтаты розыгрыша
# из базы берём количество выигрышных билетов по каждой сумме выигрыша и заносим в массив $winSum для вывода
foreach ($arr_win_sum as $key => $value)
{
    $arr_win_tickets = $db->query('SELECT count(*)  AS `cnt`  FROM `lotto`.`tickets`  WHERE `count_guessed` = ' . $key);
    $arr_win_sum[$key][1] = $arr_win_tickets[0]['cnt'];
}

# для вывода
$lotto_comb = implode(', ', $arr_win_comb);
$lotto_cnt_tickets = $db->query('SELECT count(*)  AS `cnt`  FROM `lotto`.`tickets`');
$lotto_win_tickets = $db->query('SELECT count(*)  AS `cnt`  FROM `lotto`.`tickets`  WHERE `win_sum` != 0 ');

# закрываем соединение с базой
$db->closeConnection();

# тайминг конец
$end=gettimeofday();
$total_time = (float)($end['sec'] - $start['sec']);








