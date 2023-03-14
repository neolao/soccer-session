<?php
function isCaptain($session, $playerId) {
    return in_array($playerId, $session["captains"]);
}
