<?php
function bookGuest(&$session, $name) {
    $players = &$session["players"];
    $waitingPlayers = &$session["waiting"];
    $userId = '[guest] '.$name;

    if (count($players) < MAX_PLAYERS) {
        array_push($players, $userId);
    } else if (count($waitingPlayers) < MAX_WAITING_PLAYERS) {
        array_push($waitingPlayers, $userId);
    }

    $players = array_unique($players);
    $waitingPlayers = array_unique($waitingPlayers);

}
