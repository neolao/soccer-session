<?php
function addUser($id, $name) {
    if (empty($id) || empty($name)) {
        return;
    }

    $filePath = __DIR__ . "/../../users.json";

    $size = filesize($filePath);
    $fp = fopen($filePath, "r+");
    if (flock($fp, LOCK_EX)) {
        $content = fread($fp, $size);
        $users = json_decode($content, true);

        // Add the user only if it doesn't already exist
        if (!isset($users[$id])) {
            $users[$id] = array(
                "name" => $name
            );
        }

        ftruncate($fp, 0);
        fseek($fp, 0);
        fwrite($fp, json_encode($users, JSON_PRETTY_PRINT));
        fflush($fp);
        flock($fp, LOCK_UN);
    }
    fclose($fp);

}
