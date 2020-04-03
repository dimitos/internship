<?php
// лотырея угадай $cntGuessNumbers чисел из $cntNumbers чисел

// функция подбора комбинации из cntGuessNumbers чисел
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

function valCompare($v1,$v2) {
    if ($v1===$v2) return 0;
    if ($v1 > $v2) return 1;
    return -1;
}

// функция создает массив выигрышных билетов, значению ключа соответствует кол-во отгаданных чисел в варианте
function drawResultFun($cntGuessNumbers, $maxCntWinNumbers, $playerCombinations, $resultLottery) {
    // делаю пустой массив результата розыгрыша в звисимости от мин количества чисел для выигрыша
    $drawResult = [];
    for($i = $maxCntWinNumbers; $i <= $cntGuessNumbers; $i++){
        $drawResult[$i] = array();
    }
    // перебор вариантов и запись в $drawResult
    foreach ($playerCombinations as $val) {
        $cntTotal = count(array_uintersect($val,$resultLottery,'valCompare'));
        if ($cntTotal >= $maxCntWinNumbers) {
            array_push($drawResult[$cntTotal], $val);
        }
    }
    // возвращаем массив результата розыгрыша $drawResult
    return $drawResult;
}

// -----------------------------------------------------------------------------------------------------

$cntNumbers = 36;            // из какого количества чисел угадываем
$cntGuessNumbers = 5;        // количество угадываемых чисел
$maxCntWinNumbers = 3;       // минимальное количество чисел для выигрыша
$cntGuessOption = 1000;     // количество вариантов угадывания

// результат лототрона resultLottery
$resultLottery = combinationNumbers($cntGuessNumbers, $cntNumbers,  1);

// массив комбинаций игроков лотыреи playerCombinations
$playerCombinations = combinationNumbers($cntGuessNumbers, $cntNumbers, $cntGuessOption);

// массив выигрышных билетов
$drawResult = drawResultFun($cntGuessNumbers, $maxCntWinNumbers, $playerCombinations, $resultLottery);


// вывод результата

echo 'Угадываем ' . $cntGuessNumbers . ' из ' . $cntNumbers . '<br>';
echo 'Минимальное количество чисел для выигрыша - ' . $maxCntWinNumbers . '<br>';
echo 'Количество комбинаций игроков - ' . $cntGuessOption  . '<br><hr><br>';

$winningComb = implode(", ", $resultLottery);
echo "Выигрышный вариант:  $winningComb" . '<br>';

foreach ($drawResult as $key => $value) {
    echo 'Угаданы ' . $key . ' числа - ' . count($drawResult[$key]) . ' билетов.' . '<br>';
}











