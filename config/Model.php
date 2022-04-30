<?php

namespace Config;

use PDO;

class Model
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = new PDO('mysql:host=phpmyadmin.student-sup.info;dbname=gajc9642_predictor;charset=UTF8', 'gajc9642_luis-t', 'qvBwKH#.H#L)~]gaH1', [
        //$this->pdo = new PDO('mysql:host=localhost;dbname=predictor_api;charset=UTF8', 'root', 'root', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_FOUND_ROWS => true
            ]
        );
    }
}