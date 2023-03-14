<?php
function getPlayerName($id, $users) {
    if (isset($users[$id])) {
        return $users[$id]["name"];
    }

    if (substr($id, 0, 8) === '[guest] ') {
        return $id;
    }

    return $id;
}
