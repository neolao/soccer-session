<?php
function deleteUser($id) {
    $filePath = __DIR__ . "/../../users.json";

    $size = filesize($filePath);
    $fp = fopen($filePath, "r+");
    if (flock($fp, LOCK_EX)) {
        $content = fread($fp, $size);
        $users = json_decode($content, true);

        $newUsers = array();
        foreach ($users as $userId => $user) {
            if ($userId !== $id) {
                $newUsers[$userId] = $user;
            }
        }

        ftruncate($fp, 0);
        fseek($fp, 0);
        fwrite($fp, json_encode($newUsers, JSON_PRETTY_PRINT));
        fflush($fp);
        flock($fp, LOCK_UN);
    }
    fclose($fp);
}
