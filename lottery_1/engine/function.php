<?php
function clearStr($str) {
    global $link;
    $str = trim($str);
    $str = strip_tags($str);
    return mysqli_real_escape_string($link, $str);
}