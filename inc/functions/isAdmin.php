<?php
function isAdmin($user) {
    return (isset($user["type"]) && $user["type"] === "admin");
}
