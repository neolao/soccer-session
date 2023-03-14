<?php
include "../inc/constants.php";
include "../inc/functions.php";

// Load authorized users
$users = getUsers();

// Authentication
$user = authenticate($users);
$userId = $user["id"];

// Handle form to add a session
if (isAdmin($user) && isset($_POST["action"]) && $_POST["action"] === "add") {
    $baseName = $_POST["date"];
    addSession($baseName);
}

// Header
include "../inc/header.html";
generateMenu($user, PAGE_SESSIONS);

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
    echo '<div class="actions admin">';

    // Form to add a session
    echo '<form action="" method="post">';
    echo '<input type="hidden" name="action" value="add"/>';
    echo '<label>Date: <input type="date" name="date" required/></label>';
    echo '<input type="submit" value="Add"/>';
    echo '</form>';

    echo '</div>';
}

include "../inc/footer.html";
