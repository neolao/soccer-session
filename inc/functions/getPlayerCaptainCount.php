<?php
function getPlayerCaptainCount($id, $users) {
    if (isset($users[$id])) {
        return $users[$id]["captainCount"];
    }

    return 0;
}
