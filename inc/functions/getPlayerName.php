<?php
function getPlayerName($id, $users) {
    if (isset($users[$id])) {
        return $users[$id]["name"];
    }

    if (isGuest($id)) {
        $name = substr($id, 8);
        return $name.' 🙇';
    }

    return $id;
}
