<?php

function saveSession($filePath, $session) {
    deduplicateSessionPlayers($session);

    $encodedSession = json_encode($session, JSON_PRETTY_PRINT);

    $fp = fopen($filePath, "w+");
    if (flock($fp, LOCK_EX)) {
        ftruncate($fp, 0);
        fwrite($fp, $encodedSession);
        fflush($fp);
        flock($fp, LOCK_UN);
    }
    fclose($fp);
}
