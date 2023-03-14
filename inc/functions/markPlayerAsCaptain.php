<?php
function markPlayerAsCaptain(&$session, $playerId) {
    $captains = &$session["captains"];

    array_push($captains, $playerId);

    $captains = array_unique($captains);
}
