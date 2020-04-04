<?php
// лотырея угадай $cntGuessNumbers чисел из $cntNumbers чисел

// функция генерирует комбинации из cntGuessNumbers чисел
function combinationNumbers($cntGuessNumbers, $cntNumbers, $countComb) {
    $result = [];

    if ($countComb == 1) {
        for ($i = 1; $i < $cntGuessNumbers; $i++) {
            $result[0] = mt_rand(1, $cntNumbers);
            $num = mt_rand(1, $cntNumbers);
            while (in_array($num, $result)) {
                $num = mt_rand(1, $cntNumbers);
            }
            array_push($result, $num);
        }
        sort($result);
    } else {
        for ($i = 0; $i < $countComb; $i++) {
            $result[$i][0] = mt_rand(1, $cntNumbers);
            for ($j = 1; $j < $cntGuessNumbers; $j++) {
                $num = mt_rand(1, $cntNumbers);
                while (in_array($num, $result[$i])) {
                    $num = mt_rand(1, $cntNumbers);
                }
                array_push($result[$i], $num);
            }
            sort($result[$i]);
        }
    }
    return $result;
}

// функция сравнивает комбинации и выигрыш, прибавляет к комбинации игрока элемент, равный количеству
// угаданных чиселб типа в БД в отдельной колонке проставляет количество угаданных чисел
function drawResultFun($playerCombinations, $resultLottery) {

    function valCompare($v1,$v2) {
        if ($v1===$v2) return 0;
        if ($v1 > $v2) return 1;
        return -1;
    }

    foreach ($playerCombinations as $key => $val) {
       array_push($playerCombinations[$key], count(array_uintersect($val,$resultLottery,'valCompare')));
    }
    return $playerCombinations;
}

// функция выводит на экран результат общего количества выигрышных билетов
// типа запроса в БД
function totalResult($cntGuessNumbers, $maxCntWinNumbers,$playerCombinations) {
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
$cntGuessOption = 1000;      // количество вариантов угадывания

// получаем результат лототрона resultLottery
$resultLottery = combinationNumbers($cntGuessNumbers, $cntNumbers,  1);

// получаем массив комбинаций игроков лотыреи playerCombinations
$playerCombinations = combinationNumbers($cntGuessNumbers, $cntNumbers, $cntGuessOption);

// дабавляем в массив комбинаций игроков лотыреи количество отгаданных чисел
// каждому игроку (последний эл массива комбинации чисел), типа сделали INSERT
$playerCombinations = drawResultFun($playerCombinations, $resultLottery);


// вывод результата

echo 'Угадываем ' . $cntGuessNumbers . ' из ' . $cntNumbers . '<br>';
echo 'Минимальное количество чисел для выигрыша - ' . $maxCntWinNumbers . '<br>';
echo 'Количество комбинаций игроков - ' . $cntGuessOption  . '<br><hr><br>';

$winningComb = implode(", ", $resultLottery);
echo "Выигрышный вариант:  $winningComb" . '<br><br>';

// выводим количество выигрышных билетов
totalResult($cntGuessNumbers, $maxCntWinNumbers,$playerCombinations);







