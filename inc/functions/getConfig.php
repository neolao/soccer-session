<?php

function getConfig() {
    $filePath = __DIR__ . "/../../config.json";

    if (file_exists($filePath)) {
        $fp = fopen($filePath, "r");
        if (flock($fp, LOCK_SH)) {
            $content = "";
            while (!feof($fp)) {
                $content .= fread($fp, 8192);
            }
        }
        fclose($fp);

        $config = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            die("Unable to get config, JSON Error: " . json_last_error_msg());
        }
    } else {
        $config = array();
    }

    return $config;
}
