<?php
// helper functions

function humanTime($timestamp) {
    if ($timestamp instanceof DateTime) {
        $timestamp = $timestamp->getTimestamp();
    }
    
    if (is_string($timestamp)) {
        $timestamp = strtotime($timestamp);
    }
    $now = time();
    $diff = $now - $timestamp;
    $future = ($diff < 0) ? true : false;
    $diff = abs($diff);
    
    if ($diff < 10) { // 10 secs
        $unit = "a moment";
    } elseif ($diff < 60) { // a minute
        $unit = "$diff seconds";
    } elseif ($diff < 3600) { // an hour
        $unit = floor($diff / 60) . " minutes";
    } elseif ($diff < 86400) { // a day
        $unit = floor($diff / 3600) . " hours";
    } elseif ($diff < 172800) { // two days
        $unit = "yesterday"; // ..?
    } elseif ($diff < 604800) { // a week
        $unit = date("l", $timestamp);
    } else {
        return date("H:i:s jS M, Y", $timestamp);
    }
    
    return ($future) ? "in $unit" : "$unit ago";
}

function formatMessage($input) {
    $input = preg_replace("~\*(.+)\*~", "<em>$1</em>", $input); // *xx* to <em>**</em>
    
    // convert hyperlinks
    // regex source: http://stackoverflow.com/questions/1960461/convert-plain-text-hyperlinks-into-html-hyperlinks-in-php/3525863#3525863
    $pattern = "~(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)~";
    $input = preg_replace($pattern, "<a href=\"$1\" target=\"_blank\">$1</a>", $input);
    
    // convert twitter links
    $input = convertTwitterUsernames($input);
    
    return $input;
}

function convertTwitterUsernames($input) {
    $pattern = "~\B@([a-zA-Z0-9_]{1,15})~";
    return preg_replace($pattern, "@<a href=\"http://twitter.com/$1\">$1</a>", $input);
}
