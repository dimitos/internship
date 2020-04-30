<?php
// Создание таблицы tickets с билетами и рандомными комбинациями игроков.
// Колонки: id (автоинкремент),
//          ticket (номер билета, строка, при генерации писать туда случайные числа от 900000000001 до 900099999999),
//          combination (здесь храниться игровая комбинация),
//          count_guessed количество угаданных чисел в комбинации,
//          win_sum  сумма выигрыша комбинации

set_time_limit(600);
include __DIR__ . '/config/bd.php';      // подключаемся  к базе
include_once ('engine/function.php');   // подключаем файл функций

echo "<h3>Отметьте, какую лотырею будем проводить</h3>
<form name='form1' method='post' action=''>
<input type='radio' name='lot' value='36' /> 5 из 36 <br>
<input type='radio' name='lot' value='45' /> 6 из 45<br><br>
<h3>Введите количество билетов для формирования рандомных комбинаций до 500000 штук.</h3>
<input name='cntTic' type='text' size='20' maxlength='6'><br><br>


<input type='submit' name='button' value='Проверить данные'>
</form>";

 var_dump($_POST);


$cntNumbers = $_POST['lot'];  // из какого количества чисел угадываем
if ($_POST['lot'] == 36) {
    $cntGuessNumbers = 5;        // количество угадываемых чисел
    $maxCntWinNumbers = 3;       // минимальное количество чисел для выигрыша
} else {
    $cntGuessNumbers = 6;        // количество угадываемых чисел
    $maxCntWinNumbers = 4;       // минимальное количество чисел для выигрыша
}

$cntGuessOption = $_POST['cntTic'];         // количество билетов в розыгрыше


if (validStart($cntNumbers, $cntGuessOption)){
    echo "<h3>Лотырея {$cntGuessNumbers} из {$cntNumbers}. Количество билетов: {$cntGuessOption}.</h3>";
};

echo "<form name='form1' method='post' action=''>
<input type='submit' name='button' value='Сформировать базу комбинаций'>
</form>";


echo "<h3>Подождите. Формируется база билетов с комбинациями</h3>";




$arrayTicket = ticketNumbers($cntGuessOption);   // создаем массив сномерами билетов

// делаем запись массива номеров билетов и комбинаций игроков в файл для заливки его в БД
$fp = fopen('/file.txt', 'w'); // окрыли файл
foreach ($arrayTicket as $fields) {
    $row = array($fields, implode(', ', combinationNumbers($cntGuessNumbers, $cntNumbers)));
    fputcsv($fp, $row);   // сделали запись в файл
}
fclose($fp);    // закрыли файл

//------------------------------------------------------------------------------------------------------
// создали базу
mysqli_query($link, "DROP DATABASE IF EXISTS `lotto`");
$createDB = "CREATE DATABASE `lotto`";
if (mysqli_query($link, $createDB)) {
    echo "База данных создана успешно" . '<br>';
} else {
    echo "Ошибка создания базы данных: " . mysqli_error($link);
}

// создали табличку
mysqli_query($link, "DROP TABLE IF EXISTS `lotto`.`tickets`");
$createTable =
    "CREATE TABLE `lotto`.`tickets` (
  `id` SERIAL PRIMARY KEY,
  `ticket` BIGINT  NULL,
  `combination` VARCHAR(50),
  `count_guessed` INT(2) DEFAULT 0,
  `win_sum` BIGINT(10) DEFAULT 0
  )";
if (!mysqli_query($link, $createTable)) {
    echo "Ошибка создания базы данных: " . mysqli_error($link);
}

// заливаем в БД файл номеров билетов и комбинаций игроков
mysqli_query($link, "USE `lotto`");
$q_import =
    "LOAD DATA  INFILE '/file.txt'
    INTO TABLE tickets
    FIELDS TERMINATED BY ','
    ENCLOSED BY '\"'
    (ticket, combination)";
mysqli_query($link, $q_import);

mysqli_query($link, "ALTER TABLE `lotto`.`tickets` ADD INDEX (`combination`, `count_guessed`, `win_summ`)");

mysqli_close($link);
unlink('/file.txt');  // удалили промежуточный файл

echo "<h3>База сформирована</h3>";

