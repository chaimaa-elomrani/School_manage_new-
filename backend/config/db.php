<?php

namespace Core;

use PDO;
use PDOException;

class Db {
    private static ?PDO $instance = null;

    private function __construct(){}

    public static function connection(): PDO {
        if(self::$instance !== null){
            return self::$instance;
        }

        $host = 'localhost';
        $dbname = 'school_manage';
        $username = 'root';
        $password = '';

        try{
            // Fix: Remove spaces in DSN
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

            self::$instance = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_PERSISTENT => false
            ]);
        }catch (PDOException $err){
            error_log($err->getMessage());
            die('Database connection error: ' . $err->getMessage());
        }
        return self::$instance;
    }
}