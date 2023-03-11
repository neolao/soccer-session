<?php
function getSession($filePath) {
    $size = filesize($filePath);
    $fp = fopen($filePath, "r");
    $content = fread($fp, $size);
    fclose($fp);

    $session = json_decode($content, true);
    if (json_last_error() != JSON_ERROR_NONE) {
        $session = array(
            "players" => array(),
            "waiting" => array()
        );
    }

    return $session;
}
