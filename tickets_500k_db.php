<?php
set_time_limit(800);
// 500.000 мой ноут сделал за 102 секунды.
// 1.000.000 мой ноут сделал за 252 секунды.
// В 2,5 раза разница из-за увелиыения массива номеров или надо делить txt файл на части и по частям заливать?

// функция генерирует комбинацию из cntGuessNumbers чисел
function combinationNumbers($cntGuessNumbers, $cntNumbers) {
    $result = [];
    do {
        $cnt = $cntGuessNumbers - count($result);
        for ($j = 0; $j < $cnt; $j++) {
            $num = mt_rand(1, $cntNumbers);
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

// -----------------------------------------------------------------------------------------------------
// создаем файл номеров билетов и комбинаций игроков для заливки его в БД

$cntNumbers = 36;            // из какого количества чисел угадываем
$cntGuessNumbers = 5;        // количество угадываемых чисел
$maxCntWinNumbers = 3;       // минимальное количество чисел для выигрыша
$cntGuessOption = 500000;         // количество билетов в розыгрыше

$arrayTicket = ticketNumbers($cntGuessOption);   // создаем массив сномерами билетов

// делаем запись массива номеров билетов и комбинаций игроков в файл
$fp = fopen('/file.txt', 'w'); // окрыли файл
foreach ($arrayTicket as $fields) {
    $row = array($fields, implode(', ', combinationNumbers($cntGuessNumbers, $cntNumbers)));
    fputcsv($fp, $row);   // сделали запись в файл
}
fclose($fp);    // закрыли файл

//------------------------------------------------------------------------------------------------------
// делаем заливку файла номеров билетов и комбинаций игроков в БД

// подключаемся
$host = '127.0.0.1';         // адрес сервера
$database = '';              // имя базы данных
$user = 'root';              // имя пользователя
$password = '';              // пароль
$link = new mysqli($host, $user, $password, $database)
or die("Ошибка " . mysqli_error($link));
echo "Соединение с MySQL установлено!" . '<br>';
echo "Информация о сервере: " . mysqli_get_host_info($link) . '<br>';

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
  INDEX (`combination`)
  )";
if (mysqli_query($link, $createTable)) {
    echo "Таблица создана успешно" . '<br>';
} else {
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

mysqli_close($link);

