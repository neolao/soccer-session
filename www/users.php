<?php
include "../inc/constants.php";
include "../inc/functions.php";

// Load authorized users
$users = getUsers();

// Authentication
$user = authenticate($users);
$userId = $user["id"];

if (!isAdmin($user)) {
    echo "Unauthorized user";
    die();
}

// Header
include "../inc/header.html";
generateMenu($user, PAGE_USERS);

echo '<main>';

// Handle form to add a ticket
if (isset($_POST["action"]) && $_POST["action"] === "addTicket") {
    $targetUserId = $_POST["target"];
    addTicket($targetUserId);
    $users = getUsers();
}

// Handle form to remove a ticket
if (isset($_POST["action"]) && $_POST["action"] === "removeTicket") {
    $targetUserId = $_POST["target"];
    removeTicket($targetUserId);
    $users = getUsers();
}


$enabledUsers = array();
$disabledUsers = array();
foreach ($users as $u) {
    if ($u["enabled"]) {
        array_push($enabledUsers, $u);
    } else {
        array_push($disabledUsers, $u);
    }
}

echo '<h1>Enabled users</h1>';
echo '<table class="users">';
echo '<thead><tr><th>Name</th><th>Tickets</th></tr></thead>';
echo '<tbody>';
foreach ($enabledUsers as $u) {
    echo '<tr>';
    echo '<td><a href="/user.php?id='.$u["id"].'">' . $u["name"] . '</a></td>';
    echo '<td>';
    echo '<form action="" method="post">';
    echo '<input type="hidden" name="target" value="'.$u["id"].'"/>';
    echo '<input type="hidden" name="action" value="removeTicket"/>';
    echo '<input type="submit" value="-"/>';
    echo '</form>';
    echo $u["tickets"];
    echo '<form action="" method="post">';
    echo '<input type="hidden" name="target" value="'.$u["id"].'"/>';
    echo '<input type="hidden" name="action" value="addTicket"/>';
    echo '<input type="submit" value="+"/>';
    echo '</form>';
    echo '</td>';
echo '</form>';

    echo '</tr>';
}
echo '</tbody>';
echo '</table>';

echo '<h1>Disabled users</h1>';
echo '<table class="users">';
echo '<thead><tr><th>Name</th></tr></thead>';
echo '<tbody>';
foreach ($disabledUsers as $u) {
    echo '<tr>';
    echo '<td><a href="/user.php?id='.$u["id"].'">' . $u["name"] . '</a></td>';
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';

echo '</main>';

include "../inc/footer.html";
