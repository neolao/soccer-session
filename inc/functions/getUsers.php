<?php

function getUsers() {
    $filePath = __DIR__ . "/../../users.json";

    $fp = fopen($filePath, "r");
    if (flock($fp, LOCK_SH)) {
        $content = "";
        while (!feof($fp)) {
            $content .= fread($fp, 8192);
        }
    }
    fclose($fp);

    $users = json_decode($content, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        die("Unable to get users, JSON Error: " . json_last_error_msg());
    }

    foreach ($users as $userId => &$user) {
        $user["id"] = $userId;

        if (!isset($user["type"])) {
            $user["type"] = "normal";
        }
        if (!isset($user["enabled"])) {
            $user["enabled"] = true;
        }
        if (!isset($user["tickets"])) {
            $user["tickets"] = 0;
        }
    }

    return $users;
}
