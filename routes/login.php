<?php
require_once("scripts/db.php");
require_once("authhelper.php");
require_once("alloy/view.php");
use Alloy\View;
$master = View::Get("views/master.html");
$login = View::Get("views/login.html");

if ($_VIEW == "logout")
{
    unset($_SESSION['token']);
    unset($_SESSION["userdata"]);
    unset($_SESSION["userid"]);
}
if (!isset($_SESSION['token']))
{
    $login->SetAttribute("login","href",$authUrl);
}

require_once("routes/menu.php");

$master->SetData("page",$login);


$master->Render();
?>