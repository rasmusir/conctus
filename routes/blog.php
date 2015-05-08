<?php
require_once("authhelper.php");
require_once("alloy/view.php");
require_once("scripts/model/blog.php");
use Alloy\View;
$master = View::Get("views/master.html");
require_once("routes/menu.php");

if (isset($path[2]))
{
    if ($path[2] == "new")
    {
        if (isset($_SESSION["userid"]))
        {
            View::OnPost(function($data) {
                if (isset($data["save"]))
                    View::Fire("toast",array("Saved"));
                if (isset($data["publish"]))
                {
                    $res = \Model\Blog::Insert($_SESSION["userid"],$data["title"],$data["content"]);
                    if (!$res)
                        die("mh");
                    View::Redirect("/profile/".$_SESSION["userid"]);
                }
                View::SendEvents();
            });

            $new = View::Get("views/newblog.html");
            $master->SetData("page",$new);
            $master->Render();
        }
        else
            View::Redirect("/oops");
    }
    else
    {
        if (is_numeric($path[2]))
        {
            $blogview = View::Get("views/blog.html");
            $bid = $path[2];
            
            $blog = \Model\Blog::Get($bid);
            if ($blog)
            {
                $blogview->SetData("title",$blog->title);
                $blogview->SetData("content",$blog->content);
            }
            $master->SetData("page",$blogview);
            $master->Render();
        }
    }
}

?>