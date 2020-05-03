<?php

Class Database{
    private $link;  //����� ����� ��������� ����������

    /**
     * ��� �������� ������� ����� �������� ����������
     * Database constructor.
     */
    public function __construct()
    {
        $this->connect();
    }

    /**
     * ������������� ���������� � ��, ���������� ������� ����������� $this->link
     * @return $this
     */
    private function connect()
    {
        $config = require_once './config/db_config.php';
        $dsn = 'mysql:host=' . $config['host'] . ';port=' . $config['port'] .
            ';dbname=' . $config['db_name'] . ';charset=' . $config['charset'];

        $this->link = new PDO($dsn, $config['username'], $config['password']);    // ������� � link ���� �����������

        return $this; //
    }

    /**
     * ��������� sql ������ � ��������� ���
     * @param $sql
     * @return mixed
     */
    public function execute($sql)
    {
        $sth = $this->link->prepare($sql); //������� PDO �������������� ������ � ��� ����������
        return $sth->execute();
    }

    /**
     * ��������� sql ������ � ���������� ������������� ������ �� ��
     * @param $sql
     * @return array
     */
    public function query($sql)
    {
        $sth = $this->link->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC); //�� ����� �������� ������������� ������ �� ��

        if($result === false){            // �������� result
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



