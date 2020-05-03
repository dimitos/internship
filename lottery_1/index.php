<?php
// Создание таблицы tickets с билетами и рандомными комбинациями игроков.
// Колонки: id (автоинкремент),
//          ticket (номер билета, строка, при генерации писать туда случайные числа от 900000000001 до 900099999999),
//          combination (здесь храниться игровая комбинация),
//          count_guessed количество угаданных чисел в комбинации,
//          win_sum  сумма выигрыша комбинации

require_once 'engine/function.php';
require_once 'engine/Database.php';
$db = new Database();

echo "<h3>Отметьте, какую лотерею будем проводить</h3>
<form method='post'>
<input type='radio' name='lot' value='36' /> 5 из 36 <br>
<input type='radio' name='lot' value='45' /> 6 из 45<br><br>
<h3>Введите количество билетов для формирования рандомных комбинаций до 500000 штук.</h3>
<input name='cntTic' type='text' size='20' maxlength='6'><br><br>
<input type='submit' value='Создать базу билетов с комбинациями'>
</form>";

$cntNumbers = $_POST['lot'];  // из какого количества чисел угадываем
if ($_POST['lot'] == 36) {
    $cntGuessNumbers = 5;        // количество угадываемых чисел
    $maxCntWinNumbers = 3;       // минимальное количество чисел для выигрыша
} else {
    $cntGuessNumbers = 6;        // количество угадываемых чисел
    $maxCntWinNumbers = 4;       // минимальное количество чисел для выигрыша
}

$cntGuessOption = trim($_POST['cntTic']);         // количество билетов в розыгрыше, удаляем пробелы вначале и конце

// проверочка стартовых данных
validStart($cntNumbers, $cntGuessOption, $cntGuessNumbers);

$start=gettimeofday();     // тайминг

$arrayTicket = ticketNumbers($cntGuessOption);   // создаем массив сномерами билетов

// делаем запись массива номеров билетов и комбинаций игроков в файл для заливки его в БД
$fp = fopen('/file.txt', 'w');
foreach ($arrayTicket as $fields) {
    $row = array($fields, implode(', ', combinationNumbers($cntGuessNumbers, $cntNumbers)));
    fputcsv($fp, $row);
}
fclose($fp);

//------------------------------------------------------------------------------------------------------
echo "<h3>Создаем базу</h3>";
// создаём базу
$db->execute("DROP DATABASE IF EXISTS `lotto`");
$db->execute("CREATE DATABASE `lotto`");
$db->execute("DROP TABLE IF EXISTS `lotto`.`tickets`");

// проверка на создание базы

// создаём табличку
$create_table =
    "CREATE TABLE `lotto`.`tickets` (
  `id` SERIAL PRIMARY KEY,
  `ticket` BIGINT  NULL,
  `combination` VARCHAR(50),
  `count_guessed` INT(2) DEFAULT 0,
  `win_sum` BIGINT(10) DEFAULT 0
  )";
$db->execute($create_table);

// проверка на создание таблицы

// заливаем в БД файл номеров билетов и комбинаций игроков
$db->execute("USE `lotto`");
$import_file_db =
    "LOAD DATA  INFILE '/file.txt'
    INTO TABLE tickets
    FIELDS TERMINATED BY ','
    ENCLOSED BY '\"'
    (ticket, combination)";
$db->execute($import_file_db);
$db->execute("ALTER TABLE `lotto`.`tickets` ADD INDEX (`combination`, `count_guessed`, `win_summ`)");

unlink('/file.txt');  // удалили промежуточный файл

$end=gettimeofday();
$total_time = (float)($end['sec'] - $start['sec']);
echo "База сформирована за $total_time сек" . '<br><br>';
?>

<a href='draw.php' style='
       text-decoration: none;
       background-color: #7af4f4;
       font-family: sans-serif;
       font-size: 20px;
       padding: 7px 30px;
       border-radius: 15px;
       margin-top: 30px;
       margin-left: 50px;'>
    Перейти к розыгрышу</a>








