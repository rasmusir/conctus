<?php
namespace Model;
require_once("scripts/db.php");
class Blog
{
    public static function GetByUID($uid)
    {
        $db = \DB\DB::$db;
        $statement = $db->prepare("CALL getBlogByUid(?)");
        $statement->bind_param("i",$uid);
        if ($statement->execute())
        {
            $blog = new \stdClass;
            $statement->bind_result($blog->bid,$blog->uid,$blog->title,$blog->content,$blog->timestamp);
            if ($statement->fetch())
                return $blog;
            else
                return null;
        }
        return null;
    }
    
    public static function Get($bid)
    {
        $db = \DB\DB::$db;
        $statement = $db->prepare("CALL getBlog(?)");
        $statement->bind_param("i",$bid);
        if ($statement->execute())
        {
            $blog = new \stdClass;
            $statement->bind_result($blog->bid,$blog->uid,$blog->title,$blog->content,$blog->timestamp,$blog->firstname,$blog->lastname);
            if ($statement->fetch())
                return $blog;
            else
                return null;
        }
        return null;
    }
    
    public static function GetAll($limit,$offset)
    {
        $db = \DB\DB::$db;
        $statement = $db->prepare("CALL getAllBlogs(?,?)");
        $statement->bind_param("ii",$limit,$offset);
        if ($statement->execute())
        {
            $blogs = array();
            $res = $statement->get_result();
            while($row = $res->fetch_array())
            {
                $blog = new \stdClass;
                $blog->bid = $row["blogid"];
                $blog->title = $row["title"];
                $blog->content = $row["content"];
                $blog->uid = $row["userid"];
                $blog->timestamp = $row["timestamp"];
                $blog->author = $row["firstname"]." ".$row["lastname"];
                array_push($blogs,$blog);
            }
            
            return $blogs;
        }
        return null;
    }
    
    public static function GetByTitle($title,$limit,$offset)
    {
        $db = \DB\DB::$db;
        $statement = $db->prepare("CALL getBlogsByTitle(?,?,?)");
        $search = "%$title%";
        $statement->bind_param("sii",$search,$limit,$offset);
        if ($statement->execute())
        {
            $blogs = array();
            $res = $statement->get_result();
            while($row = $res->fetch_array())
            {
                $blog = new \stdClass;
                $blog->title = $row["title"];
                $blog->content = $row["content"];
                $blog->bid = $row["blogid"];
                $blog->timestamp = $row["timestamp"];
                array_push($blogs,$blog);
            }
            
            return $blogs;
        }
        else
        {
            var_dump($statement->error);
        }
        return null;
    }
    
    public static function GetByAuthor($author)
    {
        $db = \DB\DB::$db;
        $statement = $db->prepare("CALL getBlogsByAuthor(?)");
        $search = "%$author%";
        $statement->bind_param("s",$search);
        if ($statement->execute())
        {
            $blogs = array();
            $res = $statement->get_result();
            while($row = $res->fetch_array())
            {
                $blog = new \stdClass;
                $blog->title = $row["title"];
                $blog->content = $row["content"];
                $blog->bid = $row["blogid"];
                $blog->timestamp = $row["timestamp"];
                array_push($blogs,$blog);
            }
            
            return $blogs;
        }
        else
        {
            var_dump($statement->error);
        }
        return null;
    }
    
    public static function Insert($uid,$title,$content)
    {
        $db = \DB\DB::$db;
        $statement = $db->prepare("CALL insertBlog(?,?,?)");
        $statement->bind_param("iss",$uid,$title,$content);
        if ($statement->execute())
        {
            $statement->bind_result($bid);
            $statement->fetch();
            return $bid;
        }
        else
            return false;
        
    }
    
    public static function CreateProcedures()
    {
        
        $db = \DB\DB::$db;
                
        //////////////////////
        //// getBlogByUid ////
        //////////////////////
        
        $query = <<<'EOT'
CREATE PROCEDURE getBlogByUid(IN uid_val CHAR(21))
BEGIN
    SELECT blogid,userid,title,content,timestamp FROM blogs WHERE userid=uid_val ORDER BY blogid DESC LIMIT 1;
END
EOT;
        if (!$db->query("DROP PROCEDURE IF EXISTS getBlogByUid") ||
            !$db->query($query)){
            return ("Procedure creation failed<br>".$db->error);
        }
        
        //////////////////////////
        //// getBlogsByAuthor ////
        //////////////////////////
        
        $query = <<<'EOT'
CREATE PROCEDURE getBlogsByAuthor(IN p_author CHAR(42))
BEGIN
    SELECT blogid,blogs.userid,title,content,timestamp,users.firstname,users.lastname,users.userid FROM blogs INNER JOIN users ON blogs.userid = users.userid WHERE CONCAT(users.firstname," ",users.lastname) LIKE p_author ORDER BY blogid DESC;
END
EOT;
        if (!$db->query("DROP PROCEDURE IF EXISTS getBlogsByAuthor") ||
            !$db->query($query)){
            return ("Procedure creation failed<br>".$db->error);
        }
        
        //////////////////////
        ////    getBlog   ////
        //////////////////////
        
        $query = <<<'EOT'
CREATE PROCEDURE getBlog(IN p_bid INT(6))
BEGIN
    SELECT blogid,blogs.userid,title,content,timestamp,users.firstname,users.lastname FROM blogs INNER JOIN users ON blogs.userid = users.userid WHERE blogid=p_bid ORDER BY blogid DESC LIMIT 1;
END
EOT;
        if (!$db->query("DROP PROCEDURE IF EXISTS getBlog") ||
            !$db->query($query)){
            return ("Procedure creation failed<br>".$db->error);
        }
        
        
        ///////////////////////
        ////  getAllBlogs  ////
        ///////////////////////
        
        $query = <<<'EOT'
CREATE PROCEDURE getAllBlogs(IN p_offset INT(6) UNSIGNED,IN p_limit INT(6) UNSIGNED)
BEGIN
    PREPARE STMT FROM
    "SELECT blogid,blogs.userid,title,content,timestamp,users.firstname,users.lastname FROM blogs INNER JOIN users ON blogs.userid = users.userid ORDER BY blogid DESC LIMIT ?,?";
    SET @_LIMIT = p_limit;
    SET @_OFFSET = p_offset;
    EXECUTE STMT USING @_OFFSET, @_LIMIT;
    DEALLOCATE PREPARE STMT;
END
EOT;
        if (!$db->query("DROP PROCEDURE IF EXISTS getAllBlogs") ||
            !$db->query($query)){
            return ("Procedure creation failed<br>".$db->error);
        }
        
        ///////////////////////////
        ////  getBlogsByTitle  ////
        ///////////////////////////
        
        $query = <<<'EOT'
CREATE PROCEDURE getBlogsByTitle(IN p_title CHAR(30),IN p_offset INT(6) UNSIGNED,IN p_limit INT(6) UNSIGNED)
BEGIN
    PREPARE STMT FROM
    "SELECT blogid,blogs.userid,title,content,timestamp,users.firstname,users.lastname FROM blogs INNER JOIN users ON blogs.userid = users.userid WHERE title LIKE ? ORDER BY blogid DESC LIMIT ?,?";
    SET @_LIMIT = p_limit;
    SET @_OFFSET = p_offset;
    SET @_TITLE = p_title;
    EXECUTE STMT USING @_TITLE,@_OFFSET, @_LIMIT;
    DEALLOCATE PREPARE STMT;
END
EOT;
        if (!$db->query("DROP PROCEDURE IF EXISTS getBlogsByTitle") ||
            !$db->query($query)){
            return ("Procedure creation failed<br>".$db->error);
        }
        
        
        //////////////////////
        ////  insertBlog  ////
        //////////////////////
        
        $query = <<<'EOT'
CREATE PROCEDURE insertBlog(IN p_userid INT(6), IN p_title CHAR(30), IN p_content VARCHAR(8196))
BEGIN
    INSERT INTO blogs(userid,title,content) VALUES(p_userid,p_title,p_content);
    SELECT LAST_INSERT_ID();
END
EOT;
        if (!$db->query("DROP PROCEDURE IF EXISTS insertBlog") ||
            !$db->query($query)){
            return ("Procedure creation failed<br>".$db->error);
        }
        
        
        return false;
    }
}
?>