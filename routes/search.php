<?php
require_once("scripts/db.php");
require_once("authhelper.php");
require_once("alloy/view.php");
use Alloy\View;
$master = View::Get("views/master.html");
$search = View::Get("views/search.html");
require_once("routes/menu.php");

if (isset($path[2]))
{
    $split = explode(":",urldecode($path[2]));
    $tags = new \stdClass;
    $tags->all = true;
    if (count($split) == 2)
    {
        $keyword = $split[1];
        $tagsa = explode(".",$split[0]);
        $tags->all = false;
        foreach($tagsa as $t)
        {
            $tags->$t = true;
        }
    }
    else
    {
        $keyword = $split[0];
    }
    Search($tags,$keyword);
}

function Search($tags,$keyword)
{
    if ($tags->all || isset($tags->user))
    {
        SearchUsers(trim($keyword));
    }
    
    if ($tags->all || isset($tags->blog))
    {
        SearchBlog(trim($keyword),isset($tags->by));
    }
}

function SearchBlog($keyword,$by)
{
    require_once("scripts/model/blog.php");
    global $search;
    if ($by)
        $blogs = \Model\Blog::GetByAuthor($keyword);
    else
        $blogs = \Model\Blog::GetByTitle($keyword,0,10);
    $blogscard = $search->GetTemplate("card");
    $blogscard->SetData("title","Blogs");
    if ($blogs)
    foreach ($blogs as $blog)
    {
        $blogview = $search->GetTemplate("blog");
        $blogview->SetData("link",$blog->title);
        $blogview->SetAttribute("link","href","/blog/$blog->bid");
        $blogscard->AddData("list",$blogview);
    }
    $search->AddData("deck",$blogscard);
}

function SearchUsers($name)
{
    require_once("scripts/model/user.php");
    global $search;
    $users = \Model\User::GetAllByName($name);
    $usercard = $search->GetTemplate("card");
    $usercard->SetData("title","Users");
    foreach ($users as $user)
    {
        $userview = $search->GetTemplate("user");
        $userview->SetAttribute("picture","src",$user->picture."?sz=50");
        $userview->SetData("firstname",$user->firstname);
        $userview->SetData("lastname",$user->lastname);
        $userview->SetAttribute("link","href","/profile/$user->uid");
        $usercard->AddData("list",$userview);
    }
    
    $search->AddData("deck",$usercard);
}


$master->SetData("page",$search);
$master->Render();
?>