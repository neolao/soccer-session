<?php
const MAX_PLAYERS = 10;
const MAX_WAITING_PLAYERS = 4;

include "../inc/constants.php";
include "../inc/functions.php";
include "../inc/header.html";

// Load authorized users
$users = getUsers();

// Authentication
$user = authenticate($users);
$userId = $user["id"];

// Menu
generateMenu($user, PAGE_SESSIONS);

// Handle form to add a session
if (isAdmin($user) && isset($_POST["action"]) && $_POST["action"] === "add") {
    $baseName = $_POST["date"];
    addSession($baseName);
}

echo '<main>';

// Sessions
$sessions = getSessions();
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

if (isAdmin($user)) {
    echo '<div class="admin">';

    // Form to add a session
    echo '<form action="" method="post">';
    echo '<input type="hidden" name="action" value="add"/>';
    echo '<input type="date" name="date" required/>';
    echo '<input type="submit" value="Add"/>';
    echo '</form>';

    echo '</div>';
}

include "../inc/footer.html";
