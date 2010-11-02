<?php

require "./includes/bootstrap.php";

$layout->title = "Post to the wall";

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    try {
        if (!isset($_POST["name"], $_POST["content"])) {
            throw new UserException("All fields must be submitted.");
        }
        
        if (!isset($_SESSION["last_posted"])) {
            $_SESSION["last_posted"] = time();
        } else {
            $interval = time() - $_SESSION["last_posted"];
            if ($interval < 20) {
                $remaining = 20 - $interval;
                throw new UserException("Please wait $remaining more seconds before posting again.");
            } else {
                $_SESSION["last_posted"] = $now;
            }
        }

        $post = new Post;
        $post->name = $_POST["name"];
        $post->content = $_POST["content"];
        $post->preformatted = (isset($_POST["preformatted"]) && $_POST["preformatted"] == "on") ? true : false;
        
        $post->save();
       
        redirect("index.php?id={$post->id}");
    } catch (UserException $e) {
        $view = new Template(TPL . "/error.phtml");
        $view->message = $e->getMessage();
        $layout->title = "Error";
    }
}

$form = new Template(TPL . "/submit.phtml");
if (isset($view)) {
    $layout->pageContent = $view . $form;
} else {
    $layout->pageContent = $form;
}

echo $layout;
