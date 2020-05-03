<?php

Class Database{
    private $link;  //здесь будем сохранять соединение

    /**
     * при создании объекта сразу вызываем соединение
     * Database constructor.
     */
    public function __construct()
    {
        $this->connect();
    }

    /**
     * устанавливать соединение с бд, возвращаем текущее подключение $this->link
     * @return $this
     */
    private function connect()
    {
        $config = require_once './config/db_config.php';
        $dsn = 'mysql:host=' . $config['host'] . ';port=' . $config['port'] .
            ';dbname=' . $config['db_name'] . ';charset=' . $config['charset'];

        $this->link = new PDO($dsn, $config['username'], $config['password']);    // запишем в link наше подключение

        return $this; //
    }

    /**
     * принимает sql запрос и выполняет его
     * @param $sql
     * @return mixed
     */
    public function execute($sql)
    {
        $sth = $this->link->prepare($sql); //функция PDO подготавливает запрос к его выполнению
        return $sth->execute();
    }

    /**
     * принимает sql запрос и возвращает ассоциативный массив из бд
     * @param $sql
     * @return array
     */
    public function query($sql)
    {
        $sth = $this->link->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC); //мы хотим получить ассоциативный массив из бд

        if($result === false){            // проверим result
            return [];
        }

        return $result;
    }

}
//
//$db = new Database();
//
//$sss = $db->query("SELECT * FROM lotto.tickets ORDER BY count_guessed desc limit 5");
//var_dump($sss);



