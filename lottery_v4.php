<?php

// лотырея угадай $cntGuessNumbers чисел из $cntNumbers чисел

// функция генерирует комбинации из cntGuessNumbers чисел
// убрал функцию сравнения in_array, сделал алгоритм на изъятии одинаковых элементов - уменьшил время работы на 10%

function combinationNumbers($cntGuessNumbers, $cntNumbers, $countComb)
{
    $result = [];

   // if ($countComb == 1) {
//        do {
//            $cnt = $cntGuessNumbers - count($result);
//            for ($i = 1; $i <= $cnt; $i++) {
//                $num = mt_rand(1, $cntNumbers);
//                array_push($result, $num);
//            }
//            $result = array_unique($result);
//        }
//        while (count($result) < $cntGuessNumbers);
//        sort($result);
//    } else {
        for ($i = 0; $i < $countComb; $i++) {
            $result[$i][0] = mt_rand(1, $cntNumbers);
            do {
                $cnt = $cntGuessNumbers - count($result[$i]);
                for ($j = 1; $j <= $cnt; $j++) {
                    $num = mt_rand(1, $cntNumbers);
                    array_push($result[$i], $num);
                }
                $result[$i] = array_unique($result[$i]);
            }
            while (count($result[$i]) < $cntGuessNumbers);
            sort($result[$i]);
            //echo ($result[$i]) . '<br>';

        }
    //}
    //var_dump($result);
    return $result;
}

// Функция сравнивает комбинации и выигрыш, прибавляет к комбинации игрока элемент, равный количеству
// угаданных чисел, типа в БД в отдельной колонке проставляет количество угаданных чисел.
// Убрал вложенный foreach сделал через array_intersect - уменьшил время работы ещё на 10%
function drawResultFun($playerCombinations, $resultLottery)
{
    foreach ($playerCombinations as $key => $val) {
        $cnt = count(array_intersect($resultLottery, $val));
        array_push($playerCombinations[$key], $cnt);
    }
    return $playerCombinations;
}

// функция выводит на экран результат общего количества выигрышных билетов
// типа запроса в БД
function totalResult($cntGuessNumbers, $maxCntWinNumbers, $playerCombinations)
{
    $val = array_fill(0, $cntGuessNumbers + 1, 0);

    foreach ($playerCombinations as $key => $value) {
        $val[array_pop($playerCombinations[$key])]++;
    }

    for ($i = $maxCntWinNumbers; $i <= $cntGuessNumbers; $i++) {
        echo 'Угаданы ' . $i . ' числа - ' . $val[$i] . ' билетов.' . '<br>';
    }
}

// -----------------------------------------------------------------------------------------------------

$cntNumbers = 36;            // из какого количества чисел угадываем
$cntGuessNumbers = 5;        // количество угадываемых чисел
$maxCntWinNumbers = 3;       // минимальное количество чисел для выигрыша
$cntGuessOption = 100000;       // количество вариантов угадывания

// получаем результат лототрона resultLottery
$resultLottery = combinationNumbers($cntGuessNumbers, $cntNumbers, 1);

// получаем массив комбинаций игроков лотыреи playerCombinations
$playerCombinations = combinationNumbers($cntGuessNumbers, $cntNumbers, $cntGuessOption);

// дабавляем в массив комбинаций игроков лотыреи количество отгаданных чисел
// каждому игроку (последний эл массива комбинации чисел), типа сделали INSERT
$playerCombinations = drawResultFun($playerCombinations, $resultLottery[0]);


// вывод результата

echo 'Угадываем ' . $cntGuessNumbers . ' из ' . $cntNumbers . '<br>';
echo 'Минимальное количество чисел для выигрыша - ' . $maxCntWinNumbers . '<br>';
echo 'Количество комбинаций игроков - ' . $cntGuessOption . '<br><hr><br>';

$winningComb = implode(", ", $resultLottery[0]);
echo "Выигрышный вариант:  $winningComb" . '<br><br>';

// выводим количество выигрышных билетов
totalResult($cntGuessNumbers, $maxCntWinNumbers, $playerCombinations);

