<?php
require_once("authhelper.php");
use Alloy\View;

if (isset($_SESSION["token"]) && $_SESSION["token"])
{
    $ud = $_SESSION["userdata"];
    $master->SetData("givenname",$ud->name);
    $master->SetAttribute("toppicture","src",$ud->picture."?sz=50");
    $master->SetAttribute("toppicture","class","profilepicture");
    
    $memenu = View::Get("views/subviews/memenu.html");
    $master->SetData("memenu",$memenu);
    $memenu->SetAttribute("profile","href","/profile/".$_SESSION["userid"]);
}
else
{
    $master->SetData("givenname"," ");
    $master->SetAttribute("toppicture","src"," ");
    $master->SetAttribute("toppicture","class","hidden");
    
    $loginbutton = $master->GetTemplate("menuitem");
    $master->SetData("memenu",$loginbutton);
}
?>