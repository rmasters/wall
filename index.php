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
    $offset = 0;
    $page = isset($_GET["page"]) ? (int) $_GET["page"] - 1 : 1;
    if ($page >= 1) {
        $offset = $count * ($page - 1);
    }

    $posts = Post::fetchAllWithin($offset, $count);
    $view->posts = $posts;
    $view->perPage = $count;
    $view->page = $page;
    $layout->title = "All posts";
}

$layout->pageContent = $view;
echo $layout;
