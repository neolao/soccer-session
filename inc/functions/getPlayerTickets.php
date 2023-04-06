<?php
function getPlayerTickets($id, $users, $session) {
    if (isset($users[$id])) {
        return $users[$id]["tickets"];
    }

    $consumedGuestTickets = $session["consumedGuestTickets"];
    if (isGuest($id) && in_array($id, $consumedGuestTickets)) {
        return 1;
    }

    return 0;
}
