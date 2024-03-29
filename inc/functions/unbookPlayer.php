<?php
function unbookPlayer(&$session, $userId) {
    $players = &$session["players"];
    $waitingPlayers = &$session["waiting"];
    $captains = &$session["captains"];

    // Remove from players
    if (in_array($userId, $players)) {
        $players = array_filter($players, function ($element) use ($userId) {
            return $element !== $userId;
        });
        $players = array_values($players);
    }

    // Remove from waiting list
    if (in_array($userId, $waitingPlayers)) {
        $waitingPlayers = array_filter($waitingPlayers, function ($element) use ($userId) {
            return $element !== $userId;
        });
        $waitingPlayers = array_values($waitingPlayers);
    }

    // Put user from waiting list to player list
    if (count($players) < MAX_PLAYERS) {
        if (count($waitingPlayers) > 0) {
            array_push($players, array_shift($waitingPlayers));
            $waitingPlayers = array_values($waitingPlayers);
        }
    }

    // Remove from captains
    if (in_array($userId, $captains)) {
        $captains = array_filter($captains, function ($element) use ($userId) {
            return $element !== $userId;
        });
        $captains = array_values($captains);
    }

    $players = array_unique($players);
    $waitingPlayers = array_unique($waitingPlayers);
    $captains = array_unique($captains);
}
