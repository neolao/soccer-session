<?php

function authenticate($users) {
    $userId = null;
    if (isset($_COOKIE["user"]) && !isset($_GET["user"])) {
        $userId = $_COOKIE["user"];
    } elseif (isset($_GET["user"])) {
        $userId = $_GET["user"];
    }
    if (!isset($users[$userId])) {
        echo "Unknown user";
        exit(1);
    }
    $user = $users[$userId];
    setcookie("user", $userId, time() + (60 * 60 * 24 * 365), "/");

    return $user;
}
