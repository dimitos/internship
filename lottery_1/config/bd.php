<?php
$host = '127.0.0.1';         // ����� �������
$database = '';              // ��� ���� ������
$user = 'root';              // ��� ������������
$password = '';              // ������
$link = new mysqli($host, $user, $password, $database)
or die("������ " . mysqli_error($link));
//echo "���������� � MySQL �����������!" . '<br>';
//echo "���������� � �������: " . mysqli_get_host_info($link) . '<br>';