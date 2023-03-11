<?php

function getUsers() {
    $users = json_decode(file_get_contents(__DIR__ . "/../../users.json"), true);

    foreach ($users as $userId => &$user) {
        $user["id"] = $userId;
    }

    return $users;
}
