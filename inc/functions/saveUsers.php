<?php
function saveUsers($users) {
    $filePath = __DIR__ . "/../../users.json";
    $fp = fopen($filePath, "w+");
    if (flock($fp, LOCK_EX)) {
        ftruncate($fp, 0);
        fwrite($fp, json_encode($users, JSON_PRETTY_PRINT));
        fflush($fp);
        flock($fp, LOCK_UN);
    }
    fclose($fp);
}
