<?php
require_once("authhelper.php");
require_once("alloy/view.php");
use Alloy\View;

$master = View::Get("views/master.html");
$home = View::Get("views/home.html");

require_once("routes/menu.php");
$master->SetData("page",$home);

$master->Render();
?>
