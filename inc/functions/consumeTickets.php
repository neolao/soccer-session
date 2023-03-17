<?php
function consumeTickets($session, &$users) {
    $playerIds = $session["players"];

    foreach ($users as $userId => &$user) {
        if (in_array($userId, $playerIds)) {
            $tickets = isset($user["tickets"])?$user["tickets"]:0;
            $user["tickets"] = $tickets - 1;
        }
    }
}
