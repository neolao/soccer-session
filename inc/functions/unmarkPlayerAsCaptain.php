<?php
function unmarkPlayerAsCaptain(&$session, $playerId) {
    $captains = &$session["captains"];

    $captains = array_filter($captains, function ($element) use ($playerId) {
        return $element !== $playerId;
    });
    $captains = array_values($captains);
    $captains = array_unique($captains);
}
