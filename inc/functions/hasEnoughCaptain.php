<?php
function hasEnoughCaptain($session) {
    if (count($session["captains"]) >= 2) {
        return true;
    }
    return false;
}
