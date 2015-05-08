<?php
require_once("scripts/db.php");
require_once("authhelper.php");
require_once("scripts/model/blog.php");
require_once("alloy/view.php");
use Alloy\View;
$master = View::Get("views/master.html");
$blogs = View::Get("views/blogs.html");
require_once("routes/menu.php");

$bs = \Model\Blog::GetAll(0,20);
foreach ($bs as $b)
{
    $blog = $blogs->GetTemplate("item");
    $blog->SetAttribute("item","href","/blog/$b->bid");
    $blog->SetData("title",$b->title);
    $blog->SetData("author",$b->author);
    $blogs->AddData("list",$blog);
}

$master->SetData("page",$blogs);
$master->Render();
?>