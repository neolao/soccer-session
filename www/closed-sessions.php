<?php
include "../inc/constants.php";
include "../inc/functions.php";

// Load authorized users
$users = getUsers();

// Authentication
$user = authenticate($users);
$userId = $user["id"];

// Header
include "../inc/header.html";
generateMenu($user, PAGE_CLOSED_SESSIONS);

echo '<main>';

// Sessions
$sessions = getClosedSessions();
echo '<table>';
echo '<thead><tr><th>Date</th><th>Status</th></tr></thead>';
echo '<tbody>';
foreach ($sessions as $session) {
    echo '<tr>';
    echo '<td><a href="/session.php?date='.$session["baseName"].'">' . date("Y-m-d", $session["timestamp"]) . '</a></td>';
    echo '<td>' . $session["status"] . '</td>';
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';

echo '</main>';

include "../inc/footer.html";
