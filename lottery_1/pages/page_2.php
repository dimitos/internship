<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
    <div class="s1">
        <?php
        require_once dirname(__DIR__) . '\engine/draw.php';
        ?>
        <h3>Лотерея <?=$cntGuessNumbers?> из <?=$cntNumbers?>. </h3>
        <h3>Введите выигрышную комбинацию чисел без совпадений</h3>

        <form method='post' action="page_2.php">
            <?php
            for ($i = 1; $i <= $cntGuessNumbers; $i++)
            {
                echo "
                <label>
                    <input name='{$i}' type='text' size='2' maxlength='2'>
                </label>";
            }
            ?>

            <h3>Введите выигрышные суммы за количество угаданных чисел</h3>

            <?php
            $index = $cntGuessNumbers + $maxCntWinNumbers;
            for ($i = $cntGuessNumbers + 1; $i <= $index; $i++)
            {
                $cnt = $i - $maxCntWinNumbers;
                echo "
                <label>
                   За {$cnt} - <input name='{$i}' type='text' size='10' maxlength='7'> рублей<br>
                </label><br>";
            }
            ?>

            <label>
                <input type='submit' value='Нажмите кнопку, чтобы узнать результаты розыгрыша!'>
            </label>
        </form>
    </div>
</body>
</html>

<?php
if ($total_time > 0) {
    print_r("
                <h2>Выигрышная комбинация - $comb</h2>
                <h3>Всего играло {$cntGuessOption[0]['cnt']} билетов из них {$total_win_tickets[0]['cnt']} выигрышных</h3>
            ");

    foreach ($winSum as $key => $item)
    {
        if ($item[1] == 0) {
            echo '<h3>' . $key . ' чисел никто не угадал.</h3>';
        } else {
            echo '<h3>Угаданных чисел ' . $key . ' - ' . $item[1] . ' билетов. Выигрыш по ' . $item[0] . ' рублей.</h3>';
        }
    }

    print_r("   
                <p>Проверка заняла {$total_time} сек.</p>
                <a class='button' href='page_1.php'>Перейти к новой лотерее</a>
            ");
}


