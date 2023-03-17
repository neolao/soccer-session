<?php
function chooseRandomCaptains(&$session, $users, $includeGuests) {
    $players = &$session["players"];
    $captains = &$session["captains"];

    $playersSortedByCaptainCount = array();
    foreach($players as $playerId) {
        if ($includeGuests === false && isGuest($playerId)) {
            continue;
        }
        array_push($playersSortedByCaptainCount, $playerId);
    }
    uasort($playersSortedByCaptainCount, function($firstPlayer, $secondPlayer) use ($users) {
        $firstPlayerCaptainCount = getPlayerCaptainCount($firstPlayer, $users);
        $secondPlayerCaptainCount = getPlayerCaptainCount($secondPlayer, $users);

        if ($firstPlayerCaptainCount === $secondPlayerCaptainCount) {
            return rand(0, 1);
        }
        return ($firstPlayerCaptainCount < $secondPlayerCaptainCount)? -1 : 1;
    });

    $captains = array();
    if (count($playersSortedByCaptainCount) > 0) {
        $firstChosenCaptain = array_shift($playersSortedByCaptainCount);
        array_push($captains, $firstChosenCaptain);
    }
    if (count($playersSortedByCaptainCount) > 0) {
        $secondChosenCaptain = array_shift($playersSortedByCaptainCount);
        array_push($captains, $secondChosenCaptain);
    }
}
