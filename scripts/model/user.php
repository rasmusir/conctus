<?php
namespace Model;
require_once("scripts/db.php");
class User
{
    public static function GetByGID($gid)
    {
        $db = \DB\DB::$db;
        $statement = $db->prepare("CALL getUserByGid(?)");
        $statement->bind_param("s",$gid);
        if ($statement->execute())
        {
            $user = new \stdClass;
            $statement->bind_result($user->uid,$user->firstname,$user->lastname,$user->email,$user->picture,$user->gid);
            if ($statement->fetch())
                return $user;
            else
                return null;
        }
        return null;
    }
    
    public static function Get($uid)
    {
        $db = \DB\DB::$db;
        $statement = $db->prepare("CALL getUser(?)");
        $statement->bind_param("i",$uid);
        if ($statement->execute())
        {
            $user = new \stdClass;
            $statement->bind_result($user->uid,$user->firstname,$user->lastname,$user->email,$user->picture,$user->gid);
            if ($statement->fetch())
                return $user;
            else
                return null;
        }
        return null;
    }
    
    public static function GetAllByName($name)
    {
        $db = \DB\DB::$db;
        $statement = $db->prepare("CALL getAllUsersByName(?)");
        $search = "%$name%";
        $statement->bind_param("s",$search);
        if ($statement->execute())
        {
            $users = array();
            $res = $statement->get_result();
            while($row = $res->fetch_array())
            {
                $user = new \stdClass;
                $user->firstname = $row["firstname"];
                $user->lastname = $row["lastname"];
                $user->picture = $row["profilepicture"];
                $user->uid = $row["userid"];
                array_push($users,$user);
            }
            
            return $users;
        }
        return null;
    }
    
    public static function InsertUser($fn,$ln,$email,$picture,$gid)
    {
        $db = \DB\DB::$db;
        $statement = $db->prepare("CALL insertUser(?,?,?,?,?)");
        $statement->bind_param("sssss",$fn,$ln,$email,$picture,$gid);
        if ($statement->execute())
        {
            $statement->bind_result($uid);
            $statement->fetch();
            return $uid;
        }
        else
            return false;
        
    }
    
    public static function CreateProcedures()
    {
        
        $db = \DB\DB::$db;
                
        //////////////////////
        //// getUserByGid ////
        //////////////////////
        
        $query = <<<'EOT'
CREATE PROCEDURE getUserByGid(IN gid_val CHAR(21))
BEGIN
    SELECT userid,firstname,lastname,email,profilepicture,googleid FROM users WHERE googleid=gid_val LIMIT 1;
END
EOT;
        if (!$db->query("DROP PROCEDURE IF EXISTS getUserByGid") ||
            !$db->query($query)){
            return ("Procedure creation failed<br>".$db->error);
        }
        
        //////////////////////
        ////    getUser   ////
        //////////////////////
        
        $query = <<<'EOT'
CREATE PROCEDURE getUser(IN uid_val INT(6))
BEGIN
    SELECT userid,firstname,lastname,email,profilepicture,googleid FROM users WHERE userid=uid_val LIMIT 1;
END
EOT;
        if (!$db->query("DROP PROCEDURE IF EXISTS getUser") ||
            !$db->query($query)){
            return ("Procedure creation failed<br>".$db->error);
        }
        
        /////////////////////////////
        ////  getAllUsersByName  ////
        /////////////////////////////
        
        $query = <<<'EOT'
CREATE PROCEDURE getAllUsersByName(IN p_name CHAR(30))
BEGIN
    SELECT userid,firstname,lastname,profilepicture FROM users WHERE firstname LIKE p_name OR lastname LIKE p_name LIMIT 20;
END
EOT;
        if (!$db->query("DROP PROCEDURE IF EXISTS getAllUsersByName") ||
            !$db->query($query)){
            return ("Procedure creation failed<br>".$db->error);
        }
        
        //////////////////////
        ////  insertUser  ////
        //////////////////////
        
        $query = <<<'EOT'
CREATE PROCEDURE insertUser(IN p_firstname CHAR(30), IN p_lastname CHAR(30), IN p_email CHAR(60), IN p_picture CHAR(100), IN p_gid CHAR(21))
BEGIN
    INSERT INTO users(firstname,lastname,email,profilepicture,googleid) VALUES(p_firstname,p_lastname,p_email,p_picture,p_gid);
    SELECT LAST_INSERT_ID();
END
EOT;
        if (!$db->query("DROP PROCEDURE IF EXISTS insertUser") ||
            !$db->query($query)){
            return ("Procedure creation failed<br>".$db->error);
        }
        
        
        return false;
    }
}
?>