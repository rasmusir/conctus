<?php
namespace DB;
require_once("db/init.php");
class DB
{
    public static $db;
    
    static function init()
    {
        $address = strpos($_SERVER["SERVER_NAME"],"conctus.eu") !== false ? "conctus.eu.mysql" : "127.0.0.1";
        self::$db = mysqli_connect($address,"conctus_eu","pBa7MdPN","conctus_eu");
        //die ((microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"])." s");
        if(self::$db->connect_errno)
        {
            die("Failed to connect to database");
        }
        //
    }
    
    static function recreate()
    {
        return Worker::createDatabase(self::$db);
    }
}

DB::init();
?>