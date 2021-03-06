<?php
/**
 * Bootstrapper
 */

define("DIR", realpath(dirname(__FILE__) . "/.."));
define("INC", DIR . "/includes");
define("TPL", INC . "/html");

date_default_timezone_set("Europe/London");
session_start();
ob_start();

require INC . "/helpers.php";

function __autoload($name) {
    $path = INC . "/" . str_replace(array("_", "\\"), "/", $name) . ".php";
    require $path;
}

// redirect for browsers not following location headers
function redirect($path) {
    header("Location: $path");
    die("Continue to <a href=\"$path\">$path</a>.");
}

$dsn = "sqlite:" . INC . "/data/testing.db";
$db = new PDO($dsn);
Model::setDb($db);

$layout = new Template(TPL . "/layout.phtml");
