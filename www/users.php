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

// Handle form to disable a user
if (isset($_POST["action"]) && $_POST["action"] === "disable") {
    $targetUserId = $_POST["target"];
    disableUser($targetUserId);
    $users = getUsers();
}

// Handle form to enable a user
if (isset($_POST["action"]) && $_POST["action"] === "enable") {
    $targetUserId = $_POST["target"];
    enableUser($targetUserId);
    $users = getUsers();
}

// Header
include "../inc/header.html";
generateMenu($user, PAGE_USERS);

echo '<main>';

echo '<table class="users">';
echo '<thead><tr><th>ID</th><th>Name</th><th>Tickets</th><th>Type</th><th>Status</th></tr></thead>';
echo '<tbody>';
foreach ($users as $u) {
    $status = $u["enabled"]?"Enabled":"Disabled";
    echo '<tr>';
    echo '<td>' . $u["id"] . '</td>';
    echo '<td>' . $u["name"] . '</td>';
    echo '<td></td>';
    echo '<td>' . $u["type"] . '</td>';

    // Status
    echo '<td>' . $status;
    echo '<form action="" method="post">';
    echo '<input type="hidden" name="target" value="'.$u["id"].'"/>';
    if ($u["enabled"]) {
        echo '<input type="hidden" name="action" value="disable"/>';
        echo '<input type="submit" value="Disable"/>';
        echo '</form>';
    } else {
        echo '<input type="hidden" name="action" value="enable"/>';
        echo '<input type="submit" value="Enable"/>';
    }
    echo '</form>';
    echo '</td>';
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';

echo '</main>';

include "../inc/footer.html";
