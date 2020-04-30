<?php

// выигрыш, прилетает из формы
$winComb = ['02', '18', '23', '28', '30']; // выигрышная комбинация

// проверяем на валидность

//------------------------------------------------------------

// суммы выигрыша, можно сделать расчёт в зависимости от общей сумма от продажи билетов и от количества
// выигрышных комбинаций
$winGuessNumb =
    [
        3 => 5000,
        4 => 100000,
        5 => 1000000
    ];

//------------------------------------------------------------

// вносим изменения в БД

// подключаемся
include __DIR__ . '/bd.php';

// вносим в столбец count_guessed количество угаданных чисел в комбинации
foreach ($winComb as $value) {
    $q_import =
        "UPDATE `lotto`.`tickets` SET `count_guessed` = (`count_guessed` + 1) WHERE `combination` LIKE '%{$value}%'";
    mysqli_query($link, $q_import);
}

// вносим в столбец win_sum суммы выигрышей
foreach ($winGuessNumb as $key => $value) {
    $q_import =
        "UPDATE `lotto`.`tickets` SET `win_sum` = {$value} where `lotto`.`tickets`.`count_guessed` = {$key}";
    mysqli_query($link, $q_import);
}











