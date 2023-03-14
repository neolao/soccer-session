<?php
function closeSession(&$session) {
    $session["status"] = SESSION_STATUS_CLOSED;
}
