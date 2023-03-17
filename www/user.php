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

if (!isset($_GET["id"]) || !isset($users[$_GET["id"]])) {
    echo "The user doesn't exist";
    exit(1);
}
$targetUserId = $_GET["id"];

// Handle form to disable a user
if (isset($_POST["action"]) && $_POST["action"] === "disable") {
    disableUser($targetUserId);
    $users = getUsers();
}

// Handle form to enable a user
if (isset($_POST["action"]) && $_POST["action"] === "enable") {
    enableUser($targetUserId);
    $users = getUsers();
}

// Handle form to add a ticket
if (isset($_POST["action"]) && $_POST["action"] === "addTicket") {
    addTicket($targetUserId);
    $users = getUsers();
}

// Handle form to remove a ticket
if (isset($_POST["action"]) && $_POST["action"] === "removeTicket") {
    removeTicket($targetUserId);
    $users = getUsers();
}

// Handle form to delete the user
if (isset($_POST["action"]) && $_POST["action"] === "delete") {
    deleteUser($targetUserId);
    header('Location: /users.php');
    die();
}

// Get the target user
$targetUser = $users[$_GET["id"]];


// Header
include "../inc/header.html";
generateMenu($user, PAGE_USER);

echo '<main>';

echo '<h1>'.$targetUser["name"].'</h1>';

echo '<table>';
echo '<thead><tr><th>Property</th><th>Value</th></tr></thead>';
echo '<tbody>';

echo '<tr><td>ID</td><td>'.$targetUserId.'</td></tr>';
echo '<tr><td>Type</td><td>'.$targetUser["type"].'</td></tr>';
echo '<tr><td>Name</td><td>'.$targetUser["name"].'</td></tr>';
echo '<tr><td>Tickets</td><td>'.$targetUser["tickets"].'</td></tr>';
echo '<tr><td>Captain count</td><td>'.$targetUser["captainCount"].'</td></tr>';

$status = $targetUser["enabled"]?"Enabled":"Disabled";
echo '<tr><td>Status</td><td>'.$status.'</td></tr>';
$scheme = $_SERVER['REQUEST_SCHEME'];
if (empty($scheme)) {
    $scheme = 'https';
}
$link = $scheme.'://'.$_SERVER['SERVER_NAME'].'?user='.$targetUserId;
echo '<tr><td>Link</td><td><a href="'.$link.'">'.$link.'</a></td></tr>';

echo '</tbody>';
echo '</table>';

echo '</main>';

echo '<div class="actions admin">';

echo '<form action="" method="post">';
if ($targetUser["enabled"]) {
    echo '<input type="hidden" name="action" value="disable"/>';
    echo '<input type="submit" value="Disable"/>';
} else {
    echo '<input type="hidden" name="action" value="enable"/>';
    echo '<input type="submit" value="Enable"/>';
}
echo '</form>';

echo '<form action="" method="post">';
echo '<input type="hidden" name="action" value="addTicket"/>';
echo '<input type="submit" value="Add ticket"/>';
echo '</form>';

echo '<form action="" method="post">';
echo '<input type="hidden" name="action" value="removeTicket"/>';
echo '<input type="submit" value="Remove ticket"/>';
echo '</form>';

echo '<form action="" method="post">';
echo '<input type="hidden" name="action" value="delete"/>';
echo '<input type="submit" value="Delete"/>';
echo '</form>';

echo '</div>';

include "../inc/footer.html";
