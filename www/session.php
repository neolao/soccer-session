<?php
include "../inc/constants.php";
include "../inc/functions.php";

// Load authorized users
$users = getUsers();

// Authentication
$user = authenticate($users);
$userId = $user["id"];

// Load the session
if (!isset($_GET["date"])) {
    echo "The session doesn't exist";
    exit(1);
}
$date = $_GET["date"];
$sessionFilePath = __DIR__ . "/../sessions/" . $date . ".json";
if (!file_exists($sessionFilePath)) {
    echo "The session doesn't exist";
    exit(1);
}
$session = getSession($sessionFilePath);
$players = &$session["players"];
$waitingPlayers = &$session["waiting"];

// Handle form to delete the session
if (isAdmin($user) && isset($_POST["action"]) && $_POST["action"] === "delete") {
    deleteSession($session["baseName"]);
    header('Location: /');
    die();
}

// Handle form to book
if (isset($_POST["action"]) && $_POST["action"] === "book") {
    if (count($players) < MAX_PLAYERS) {
        array_push($players, $userId);
    } else if (count($waitingPlayers) < MAX_WAITING_PLAYERS) {
        array_push($waitingPlayers, $userId);
    }

    $players = array_unique($players);
    $waitingPlayers = array_unique($waitingPlayers);

    saveSession($sessionFilePath, json_encode($session));
}

// Header
include "../inc/header.html";
generateMenu($user, PAGE_SESSION);

// Handle form to unbook
if (isset($_POST["action"]) && $_POST["action"] === "unbook") {
    // Remove from players
    if (in_array($userId, $players)) {
        $players = array_filter($players, function ($element) {
            global $userId;
            return $element !== $userId;
        });
        $players = array_values($players);
    }
    // Remove from waiting list
    if (in_array($userId, $waitingPlayers)) {
        $waitingPlayers = array_filter($waitingPlayers, function ($element) {
            global $userId;
            return $element !== $userId;
        });
        $waitingPlayers = array_values($waitingPlayers);
    }
    // Put user from waiting list to player list
    if (count($players) < MAX_PLAYERS) {
        if (count($waitingPlayers) > 0) {
            array_push($players, array_shift($waitingPlayers));
            $waitingPlayers = array_values($waitingPlayers);
        }
    }

    $players = array_unique($players);
    $waitingPlayers = array_unique($waitingPlayers);

    saveSession($sessionFilePath, json_encode($session));
}

echo '<main>';

// Players
echo '<table>';
echo '<thead><tr><th>Players</th></tr></thead>';
echo '<tbody>';
for ($index = 0; $index < MAX_PLAYERS; $index++) {
    echo '<tr>';
    if (isset($players[$index])) {
        $playerId = $players[$index];
        echo '<td>' . $users[$playerId]["name"] . '</td>';
    } else {
        echo '<td></td>';
    }
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';

// Waiting players
echo '<table>';
echo '<thead><tr><th>Waiting players</th></tr></thead>';
echo '<tbody>';
for ($index = 0; $index < MAX_WAITING_PLAYERS; $index++) {
    echo '<tr>';
    if (isset($waitingPlayers[$index])) {
        $playerId = $waitingPlayers[$index];
        echo '<td>' . $users[$playerId]["name"] . '</td>';
    } else {
        echo '<td></td>';
    }
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';

echo '</main>';

echo '<div class="actions">';
if (in_array($userId, $players) || in_array($userId, $waitingPlayers)) {
    // Form to unbook
    echo '<form action="" method="post">';
    echo '<input type="hidden" name="action" value="unbook"/>';
    echo '<input type="submit" value="Unbook"/>';
    echo '</form>';
} else {
    // Form to book
    echo '<form action="" method="post">';
    echo '<input type="hidden" name="action" value="book"/>';
    echo '<input type="submit" value="Book"/>';
    echo '</form>';
}
echo '</div>';

if (isAdmin($user)) {
    echo '<div class="admin">';

    // Form to delete the session
    echo '<form action="" method="post">';
    echo '<input type="hidden" name="action" value="delete"/>';
    echo '<input type="submit" value="Delete"/>';
    echo '</form>';

    echo '</div>';
}
include "../inc/footer.html";
