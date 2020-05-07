<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
    <div class="s1">
        <h3>Отметьте, какую лотерею будем проводить</h3>
        <form method='post'>
            <label>
                <input type='radio' name='lot' value='36' />
            </label> 5 из 36 <br>
            <label>
                <input type='radio' name='lot' value='45' />
            </label> 6 из 45<br><br>
            <h3>Введите количество билетов для формирования рандомных комбинаций до 500000 штук.</h3>
            <label>
                <input name='cnt_tic' type='text' size='20' maxlength='6' placeholder='не более 500000'>
            </label><br><br>
            <input type='submit' value='Создать базу билетов с комбинациями'>
        </form>

        <?php
        require_once dirname(__DIR__) . '\engine/create_tickets.php';

        if ($total_time > 0) {
            print_r("
            <h3>Лотерея {$lotto_cnt_guess_num}  из {$lotto_num_range} . Количество билетов: {$lotto_cnt_tickets}</h3>
            <p>База сформирована за {$total_time} сек.</p>
            <a class='button' href='page_2.php'>Перейти к розыгрышу</a>
        ");
        }
        ?>
    </div>
</body>
</html>



