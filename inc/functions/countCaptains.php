<?php
function countCaptains($session, &$users) {
    $captains = $session["captains"];
    if (count($captains) <= 0) {
        return;
    }

    foreach($captains as $captainId) {
        if (isset($users[$captainId])) {
            $user = &$users[$captainId];
            if (!isset($user["captainCount"])) {
                $user["captainCount"] = 0;
            }
            $user["captainCount"] = (int) $user["captainCount"] + 1;
        }
    }
}
