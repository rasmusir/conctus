<?php
require_once("authhelper.php");
require_once("alloy/view.php");
require_once("scripts/model/user.php");
require_once("scripts/model/blog.php");
use Alloy\View;

$master = View::Get("views/master.html");
$profile = View::Get("views/profile.html");

require_once("routes/menu.php");

if (isset($path[2]))
{
    $ud = \Model\User::Get($path[2]);
    $profile->SetData("name",$ud->firstname . " " . $ud->lastname);
    $profile->SetAttribute("profilepicture","src",$ud->picture."?sz=200");
    
    $blog = \Model\Blog::GetByUid($path[2]);
    
    if ($blog != null)
    {
        $profile->SetData("blogtitle",$blog->title);
        $profile->SetData("blogtime",$blog->timestamp);
        $profile->SetData("blogcontent",$blog->content);
        $profile->SetAttribute("moreblogs","href","/search/blog.by:$ud->firstname $ud->lastname");
    }
    
    if (isset($_SESSION["userid"]) && (string)$_SESSION["userid"] == $path[2])
    {
        $menu = $profile->GetTemplate("menutemplate");
        
        $profile->SetData("menu",$menu);
    }
    
}
$master->SetData("page",$profile);

$master->Render();
?>
