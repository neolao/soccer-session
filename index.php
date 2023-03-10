<?php
const MAX_PLAYERS = 10;
const MAX_WAITING_PLAYERS = 4;

// Functions
function saveSession($filePath, $session) {
    $fp = fopen($filePath, "w+");
    if (flock($fp, LOCK_EX)) {
        ftruncate($fp, 0);
        fwrite($fp, $session);
        fflush($fp);
        flock($fp, LOCK_UN);
    }
    fclose($fp);
}

// Load authorized users
$users = json_decode(file_get_contents("./users.json"), true);

// Check the user
$userId = null;
if (isset($_COOKIE["user"])) {
    $userId = $_COOKIE["user"];
} elseif (isset($_GET["user"])) {
    $userId = $_GET["user"];
}
if (!isset($users[$userId])) {
    echo "Unknown user";
    exit(1);
}
$user = $users[$userId];
setcookie("user", $userId, time() + (60 * 60 * 24 * 365), "/");
echo '<h1>Hello ' . $user["name"] . '</h1>';

// Load the session
if (!isset($_GET["date"])) {
    echo "The session doesn't exist";
    exit(1);
}
$date = $_GET["date"];
$sessionFilePath = __DIR__ . "/" . $date . ".json";
if (!file_exists($sessionFilePath)) {
    echo "The session doesn't exist";
    exit(1);
}
$sessionContent = file_get_contents($sessionFilePath);
$session = json_decode($sessionContent, true);
if (json_last_error() != JSON_ERROR_NONE) {
    $session = array(
        "players" => array(),
        "waiting" => array()
    );
}
$players = &$session["players"];
$waitingPlayers = &$session["waiting"];

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

// Handle form to unbook
if (isset($_POST["action"]) && $_POST["action"] === "unbook") {
    // Remove from players
    if (in_array($userId, $players)) {
        $players = array_filter($players, function ($element) {
            global $userId;
            return $element !== $userId;
        });
    }
    // Remove from waiting list
    if (in_array($userId, $waitingPlayers)) {
        $waiting = array_filter($waitingPlayers, function ($element) {
            global $userId;
            return $element !== $userId;
        });
    }
    // Put user from waiting list to player list
    if (count($players) < MAX_PLAYERS) {
        if (count($waitingPlayers) > 0) {
            array_push($players, array_shift($waitingPlayers));
        }
    }

    $players = array_unique($players);
    $waitingPlayers = array_unique($waitingPlayers);

    saveSession($sessionFilePath, json_encode($session));
}

// Players
echo '<h2>Players</h2>';
echo '<table>';
echo '<thead><tr><th>Name</th></tr></thead>';
echo '<tbody>';
foreach ($players as $playerId) {
    echo '<tr>';
    echo '<td>' . $users[$playerId]["name"] . '</td>';
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';

// Waiting players
echo '<h2>Waiting players</h2>';
echo '<table>';
echo '<thead><tr><th>Name</th></tr></thead>';
echo '<tbody>';
foreach ($waitingPlayers as $playerId) {
    echo '<tr>';
    echo '<td>' . $users[$playerId]["name"] . '</td>';
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';

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
