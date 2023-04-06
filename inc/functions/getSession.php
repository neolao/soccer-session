<?php
function getSession($filePath) {
    $size = filesize($filePath);
    if ($size > 0) {
        $fp = fopen($filePath, "r");
        $content = fread($fp, $size);
        fclose($fp);
        $session = json_decode($content, true);
    }

    if (!isset($session) || !is_array($session)) {
        $session = array();
    }

    if (!isset($session["status"])) {
        $session["status"] = SESSION_STATUS_OPEN;
    }
    if (!isset($session["players"])) {
        $session["players"] = array();
    }
    if (!isset($session["waiting"])) {
        $session["waiting"] = array();
    }
    if (!isset($session["consumedPlayerTickets"])) {
        $session["consumedPlayerTickets"] = array();
    }
    if (!isset($session["guests"])) {
        $session["guests"] = array();
    }
    if (!isset($session["consumedGuestTickets"])) {
        $session["consumedGuestTickets"] = array();
    }
    if (!isset($session["captains"])) {
        $session["captains"] = array();
    }

    $baseName = basename($filePath, ".json");
    $session["baseName"] = $baseName;
    $session["timestamp"] = strtotime($baseName);

    return $session;
}
