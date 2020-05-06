<?php

// функция генерирует комбинацию из cntGuessNumbers чисел
function combinationNumbers($cntGuessNumbers, $cntNumbers) {
    $result = [];
    while (count($result) < $cntGuessNumbers) {
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

/**
 * function проверяет введённые данные на пустоту и на положительное число
 * @param array $arr
 * @return array
 */
function validFill($arr){
    foreach ($arr as $key => $value)
    {
        $arr[$key] = str_replace(' ', '', $value);
        if ($arr[$key] == 0 || !ctype_digit($arr[$key])) {
            exit('Необходимо правильно заполнить все поля');
        }
    }
    return $arr;
}



