<?php
require_once("authhelper.php");
require_once("alloy/view.php");
use Alloy\View;
$master = View::Get("views/master.html");
$view = View::Get("views/notfound.html");

require_once("routes/menu.php");
$master->SetData("page",$view);

$master->Render();
?>