<?php

function saveSession($filePath, $session) {
    $fp = fopen($filePath, "w+");
    if (flock($fp, LOCK_EX)) {
        ftruncate($fp, 0);
        fwrite($fp, $session);
        fflush($fp);
        flock($fp, LOCK_UN);
    }
    fclose($fp);
}
