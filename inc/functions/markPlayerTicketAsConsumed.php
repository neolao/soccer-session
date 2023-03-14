<?php
function markPlayerTicketAsConsumed(&$session, $users) {
    $playerIds = $session["players"];
    $playersWithEnoughTickets = array();

    foreach ($users as $userId => &$user) {
        if (in_array($userId, $playerIds) && $user["tickets"] > 0) {
            array_push($playersWithEnoughTickets, $userId);
        }
    }

    $session["consumedPlayerTickets"] = $playersWithEnoughTickets;
}
