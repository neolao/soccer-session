<?php
function getPlayerTickets($id, $users) {
    if (isset($users[$id])) {
        return $users[$id]["tickets"];
    }

    if (substr($id, 0, 8) === '[guest] ') {
        return 1;
    }

    return 0;
}
