<?php
require("alloy/view.php");
use Alloy\View;
$master = View::Get("views/master.html");
$home = View::Get("views/home.html");

$master->SetData("page",$home);

$master->Render();
?>
