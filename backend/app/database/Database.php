<?php

namespace app\database;

use PDO;
class Database{

    public static function connection(){
        return new PDO('mysql:host=localhost; dbname=jwt;', 'root', '', [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ]);


    }

}