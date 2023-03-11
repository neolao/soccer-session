<?php

function getSessions() {
    $sessions = array();

    foreach (glob(__DIR__ . "/../../sessions/*.json") as $sessionFilePath) {
        $session = getSession($sessionFilePath);
        array_push($sessions, $session);
    }

    $sessions = array_reverse($sessions);
    return $sessions;
}
