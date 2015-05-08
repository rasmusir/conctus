<?php
require_once("authhelper.php");
require_once("alloy/view.php");
require_once("scripts/db.php");
use Alloy\View;

View::On("test", function($args) {
    $data = new \StdClass;
    $err = DB\DB::recreate();
    $data->err = $err;
    return $data;
});

$master = View::Get("views/master.html");
$db = View::Get("views/db.html");
require_once("routes/menu.php");


View::UseJSModule("/client/scripts/db.js");
$master->SetData("page",$db);



$master->Render();
?>