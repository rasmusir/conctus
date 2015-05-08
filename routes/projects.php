<?php
require_once("alloy/view.php");
use Alloy\View;
$master = View::Get("views/master.html");
$page = View::Get("views/projects.html");

require_once("routes/menu.php");
$master->SetData("page",$page);

$project = $page->GetTemplate("project");

$page->AddData("list",$project);
$page->AddData("list",$project);
$page->AddData("list",$project);
$page->AddData("list",$project);

$master->Render();
?>
