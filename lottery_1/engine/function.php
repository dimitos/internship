<?php

// функция генерирует комбинацию из cntGuessNumbers чисел
function combinationNumbers($cntGuessNumbers, $cntNumbers) {
    $result = [];
    do {
        $cnt = $cntGuessNumbers - count($result);
        for ($j = 0; $j < $cnt; $j++) {
            $num = mt_rand(1, $cntNumbers);
            if (strlen($num) == 1) {
                $num = '0' . $num;
            }
            array_push($result, $num);
        }
        $result = array_unique($result);
    }
    while (count($result) < $cntGuessNumbers);
    sort($result);
    return $result;
}

// функция генерирует массив номеров билетов
function ticketNumbers($cntGuessOption) {
    $result = [];
    do {
        $cnt = $cntGuessOption - count($result);
        for ($i = 1; $i <= $cnt; $i++) {
            $num = mt_rand(900000000001, 900099999999);
            array_push($result, $num);
        }
        $result = array_unique($result);
    }
    while (count($result) < $cntGuessOption);
    return $result;
}

// функция проверяет на валидность введенных стартовых данных
function validStart($cntNumbers, $cntGuessOption, $cntGuessNumbers){

    if ($cntNumbers == '') {
        exit("Необходимо выбрать лотырею");
    }

    if ($cntGuessOption == '' || $cntGuessOption > 500000 || !preg_match("|^[\d]*$|", $cntGuessOption)) {
        exit("Необходимо ввести количество билетов натуральными числом до 500000");
    }

    echo "Лотырея $cntGuessNumbers из $cntNumbers. Количество билетов: $cntGuessOption.";

    return;
}

