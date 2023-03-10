<?php
const MAX_PLAYERS = 10;
const MAX_WAITING_PLAYERS = 4;

// Functions
function getSession($filePath) {
    $size = filesize($filePath);
    $fp = fopen($filePath, "r");
    $content = fread($fp, $size);
    fclose($fp);

    $session = json_decode($content, true);
    if (json_last_error() != JSON_ERROR_NONE) {
        $session = array(
            "players" => array(),
            "waiting" => array()
        );
    }

    return $session;
}
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


?><!DOCTYPE html>
<html>
    <head>
        <title>Soccer session</title>
        <link rel="stylesheet" type="text/css" href="style.css"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    </head>
    <body>
<?php
// Load authorized users
$users = json_decode(file_get_contents("../users.json"), true);

// Check the user
$userId = null;
if (isset($_COOKIE["user"]) && !isset($_GET["user"])) {
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
$sessionFilePath = __DIR__ . "/../sessions/" . $date . ".json";
if (!file_exists($sessionFilePath)) {
    echo "The session doesn't exist";
    exit(1);
}
$session = getSession($sessionFilePath);
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

?></body>
</html>
