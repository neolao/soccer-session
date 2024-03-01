<?php
function deduplicateSessionPlayers(&$session) {
    $session["players"] = array_unique($session["players"]);
    $session["waiting"] = array_unique($session["waiting"]);
    $session["guests"] = array_unique($session["guests"]);
    $session["captains"] = array_unique($session["captains"]);
}
