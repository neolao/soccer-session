<?php

function getClosedSessions() {
    $sessions = array();

    foreach (glob(__DIR__ . "/../../sessions/*.json") as $sessionFilePath) {
        $session = getSession($sessionFilePath);
        if ($session["status"] === "closed") {
            array_push($sessions, $session);
        }
    }

    $sessions = array_reverse($sessions);
    return $sessions;
}
