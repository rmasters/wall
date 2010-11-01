<?php

require "./includes/bootstrap.php";

if (isset($_GET["id"]) && (int) $_GET["id"] > 0) {
    $model = Post::fetch($_GET["id"]);
    if (!$model) {
        $view = new Template(TPL . "/error.phtml");
        $view->message = "No post found for #" . (int) $_GET["id"];
        $layout->title = "Post not found";
    } else {
        $view = new Template(TPL . "/individual.phtml");
        $view->post = $model;
        $layout->title = $model->name . "'s post";
    }
} else {
    $view = new Template(TPL . "/list.phtml");
    
    $count = 10;
    $start = 0;
    $page = isset($_GET["page"]) ? (int) $_GET["page"] - 1 : 1;
    if ($page >= 1) {
        $start = $count * $page;
    }
    
    $posts = Post::fetchAllWithin($start, $count);
    $view->posts = $posts;
    $view->page = $page + 1;
    $layout->title = "All posts";
}

$layout->pageContent = $view;
echo $layout;
