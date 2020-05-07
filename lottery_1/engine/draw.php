<?php

require_once dirname(__DIR__) . '\engine/function.php';
$config = require_once dirname(__DIR__) . '\config/db_config.php';
require_once dirname(__DIR__) . '\engine/Database.php';
$db = new Database($config);

$row = $db->query('SELECT `combination` FROM `lotto`.`tickets` LIMIT 1');
$cntGuessNumbers = count(explode(', ', $row[0]['combination']));

if ($cntGuessNumbers == 5){
    $cntNumbers = 36;
    $maxCntWinNumbers = 3;
} else {
    $cntNumbers = 45;
    $maxCntWinNumbers = 4;
}

# ------------------------------------------------------------------------------------------------------------
# делаем проверку на ввод данных розыгрыша и разбиваем на два массива: комбинация и суммы

$input_date = validFill($_POST);

$winComb = [];
$winSum = [];

# Разбиваем _POST на комбинацию и суммы.
foreach ($input_date as $key => $value)
{
    if($key <= $cntGuessNumbers) {
        if (strlen($value) == 1) {
            $value = '0' . $value;
        }
        $winComb[$key] = $value;
    } else {
        $winSum[$key - $maxCntWinNumbers][0] = $value;
    }
}

# Комбинацию проверяем на диапазон и совпадения чисел в кмбинации.
foreach ($winComb as $val)
{
    if($val > $cntNumbers || $cntGuessNumbers > count(array_unique($winComb))){
        exit('Числа в комбинации должны быть диапазоне от 1 до ' . $cntNumbers . ' без совпадений');
    }
}
sort($winComb);

# ------------------------------------------------------------------------------------------------------------
# вносим изменения в БД

$start=gettimeofday();     # тайминг

# сбрасываем на DEFAULT столбцы count_guessed и win_sum
$db->query('UPDATE `lotto`.`tickets` SET `count_guessed` = DEFAULT, `win_sum` = DEFAULT');

# вносим в столбец count_guessed количество угаданных чисел в комбинации
foreach ($winComb as $value) {
    $query =
        "UPDATE `lotto`.`tickets` SET `count_guessed` = (`count_guessed` + 1) WHERE `combination` LIKE '%{$value}%'";
    $db->query($query);
}

# вносим в базу в столбец win_sum суммы выигрышей
foreach ($winSum as $key => $value)
{
    $query =
        'UPDATE `lotto`.`tickets` SET `win_sum` = ' . $winSum[$key][0] . ' where `lotto`.`tickets`.`count_guessed` = ' . $key;
    $db->query($query);
}

# ------------------------------------------------------------------------------------------------------------
# выводим резудьтаты розыгрыша
# из базы берём количество выигрышных билетов по каждой сумме выигрыша и заносим в массив $winSum для вывода
foreach ($winSum as $key => $value)
{
    $winTickets = $db->query('SELECT count(*)  AS `cnt`  FROM `lotto`.`tickets`  WHERE `count_guessed` = ' . $key);
    $winSum[$key][1] = $winTickets[0]['cnt'];
}

# для вывода
$comb = implode(', ', $winComb);
$cntGuessOption = $db->query('SELECT count(*)  AS `cnt`  FROM `lotto`.`tickets`');
$total_win_tickets = $db->query('SELECT count(*)  AS `cnt`  FROM `lotto`.`tickets`  WHERE `win_sum` != 0 ');
$db->closeConnection(); # закрываем соединение с базой

$end=gettimeofday();
$total_time = (float)($end['sec'] - $start['sec']);








