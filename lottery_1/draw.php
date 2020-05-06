<?php

$config = require_once 'config/db_config.php';
require_once 'engine/Database.php';
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

//------------------------------------------------------------------------------
// запрашиваем выигрышную комбинацию и суммы выигрышей
echo "<h3>Лотерея $cntGuessNumbers из $cntNumbers. </h3>
    <h3>Введите выигрышную комбинацию</h3>
    <form method='post'>";

for ($i = 1; $i <= $cntGuessNumbers; $i++){
    echo "
    <label>
        <input name='{$i}' type='text' size='2' maxlength='2'>
    </label>";
}

echo '<br><br><h3>Введите выигрышные суммы за количество угаданных чисел</h3>';
$index = $cntGuessNumbers + $maxCntWinNumbers;
for ($i = $cntGuessNumbers + 1; $i <= $index; $i++){
    $cnt = $i - $maxCntWinNumbers;
    echo "
    <label>
       За {$cnt} - <input name='{$i}' type='text' size='10' maxlength='7'> рублей<br>
    </label><br>";
}

echo "<br><label>
            <input type='submit' value='Нажмите кнопку, чтобы узнать результаты розыгрыша!'>
      </label>
      </form>";

//------------------------------------------------------------------------------------------------------------
// делаем проверку на ввод данных розыгрыша и разбиваем на два массива: комбинация и суммы

$start=gettimeofday();     // тайминг

foreach ($_POST as $value){
    if ($value == ''|| !preg_match("|^[\d]*$|", $value)) {
        exit('Необходимо заполнить все поля натуральными числами, 
        а для выигрышной комбинации - в диапазоне от 1 до ' . $cntNumbers . ' без совпадений');
    }
}

// разбиваем массив _POST на комбинацию и суммы. Комбинацию одновременно проверяем.
$winComb = [];
$winSum = [];

foreach ($_POST as $key => $value){
    if($key <= $cntGuessNumbers){
        if(1 > $value || $value > $cntNumbers || in_array($value, $winComb)){
            exit('Числа в комбинации должны быть диапазоне от 1 до ' . $cntNumbers . ' без совпадений');
        }
        if (strlen($value) == 1) {
            $value = '0' . $value;
        }
        $winComb[$key] = $value;
    } else {
        $winSum[$key - $maxCntWinNumbers] = $value;
    }
}
sort($winComb);

//------------------------------------------------------------------------------------------------------------
// вносим изменения в БД

// сбрасываем на DEFAULT столбцы count_guessed и win_sum
$db->query('UPDATE `lotto`.`tickets` SET `count_guessed` = DEFAULT, `win_sum` = DEFAULT');

// вносим в столбец count_guessed количество угаданных чисел в комбинации
foreach ($winComb as $value) {
    $query =
        "UPDATE `lotto`.`tickets` SET `count_guessed` = (`count_guessed` + 1) WHERE `combination` LIKE '%{$value}%'";
    $db->query($query);
}

// вносим в столбец win_sum суммы выигрышей
foreach ($winSum as $key => $value) {
    $query =
        'UPDATE `lotto`.`tickets` SET `win_sum` = ' . $value . ' where `lotto`.`tickets`.`count_guessed` = ' . $key;
    $db->query($query);
}

//------------------------------------------------------------------------------------------------------------
// выводим результаты розыгрыша
$comb = implode(', ', $winComb);

$cntGuessOption = $db->query('SELECT count(*)  AS `cnt`  FROM `lotto`.`tickets`');

$totWinTickets = $db->query('SELECT count(*)  AS `cnt`  FROM `lotto`.`tickets`  WHERE `win_sum` != 0 ');

echo "
<h2>Выигрышная комбинация - $comb</h2>
<h3>Всего играло {$cntGuessOption[0]['cnt']} билетов из них {$totWinTickets[0]['cnt']} выигрышных</h3>
";

foreach ($winSum as $key => $value) {
    $winTickets = $db->query('SELECT count(*)  AS `cnt`  FROM `lotto`.`tickets`  WHERE `count_guessed` = ' . $key);

    if ($winTickets[0]['cnt'] == 0) {
        echo "
        <h3>$key чисел никто не угадал.</h3>";
    } else {
        echo "
        <h3>Угаданных чисел $key -   {$winTickets[0]['cnt']} билетов. Выигрыш по $value рублей.</h3>";
    }
}

$end=gettimeofday();
$totalTime = (float)($end['sec'] - $start['sec']);
echo 'Проверка заняла ' . $totalTime . ' сек.<br><br>';
?>

<a href='index.php'style='
       text-decoration: none;
       background-color: #7af4f4;
       font-family: sans-serif;
       font-size: 20px;
       padding: 7px 30px;
       border-radius: 15px;
       margin-top: 30px;
       margin-left: 50px;'>
    Перейти к новой лотерее</a>









