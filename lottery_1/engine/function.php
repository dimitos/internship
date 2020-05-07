<?php

/**
 * Функция генерирует массив комбинации чисел игрока
 * @param $lotto_cnt_guess_num, количество угадываемых чисел
 * @param $lotto_num_range - диапазон угадываемых чисел
 * @return array, комбинацию чисел
 */
function combinationNumbers($lotto_cnt_guess_num, $lotto_num_range)
{
    $result = [];
    while (count($result) < $lotto_cnt_guess_num)
    {
        $cnt = $lotto_cnt_guess_num - count($result);
        for ($j = 0; $j < $cnt; $j++) {
            $num = mt_rand(1, $lotto_num_range);
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

/**
 * функция генерирует массив c номерами билетов
 * @param $lotto_cnt_tickets, общее количество играющих билетов
 * @return array, массив c номерами билетов
 */
function ticketNumbers($lotto_cnt_tickets)
{
    $result = [];
    while (count($result) < $lotto_cnt_tickets)
    {
        $cnt = $lotto_cnt_tickets - count($result);
        for ($i = 1; $i <= $cnt; $i++) {
            $num = mt_rand(900000000001, 900099999999);
            array_push($result, $num);
        }
        $result = array_unique($result);
    }

    return $result;
}

/**
 * function проверяет введённые данные на 0 и на положительное целое число
 * @param array $arr массив элементов
 * @return array $arr массив строковых элементов чисел без пробелов
 */
function validFill($arr)
{
    foreach ($arr as $key => $value)
    {
        $arr[$key] = str_replace(' ', '', $value);
        if ($arr[$key] == 0 || !ctype_digit($arr[$key])) {
            exit('Необходимо правильно заполнить все поля');
        }
    }
    return $arr;
}



