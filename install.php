<?php

require "./includes/bootstrap.php";

Post::drop();
Post::create();
echo "posts table created.\n";
