<?php
namespace DB;
require_once("scripts/model/user.php");
require_once("scripts/model/blog.php");
class Worker
{
    static function createDatabase($db)
    {
        $err = self::createProcedures($db);
        if ($err)
            return $err;
        if (!$db->query("CALL createTables()"))
            return ("Failed to create tables");
        
        return false;
    }
    
    static function createProcedures($db)
    {
        //////////////////////
        //// createTables ////
        //////////////////////
        
        $query = <<<'EOT'
CREATE PROCEDURE createTables()
BEGIN
    DROP TABLE IF EXISTS users;
    CREATE TABLE users (userid INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, firstname CHAR(30) NOT NULL, lastname CHAR(30) NOT NULL, email CHAR(60) NOT NULL,profilepicture CHAR(150) NOT NULL, googleid CHAR(21) NOT NULL, reg_date TIMESTAMP);
    DROP TABLE IF EXISTS blogs;
    CREATE TABLE blogs (blogid INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, userid INT(6) UNSIGNED NOT NULL, title CHAR(30) NOT NULL, content VARCHAR(8192) NOT NULL, timestamp TIMESTAMP);
END
EOT;
        if (!$db->query("DROP PROCEDURE IF EXISTS createTables") ||
            !$db->query($query)){
            return ("Procedure creation failed<br>".$db->error);
        }
        
        if ($res = \Model\User::CreateProcedures())
            return $res;
        
        if ($res = \Model\Blog::CreateProcedures())
            return $res;
    }
}
?>