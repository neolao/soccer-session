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

// Handle form to close the session
if (isAdmin($user) && isset($_POST["action"]) && $_POST["action"] === "close") {
    closeSession($session);
    saveSession($sessionFilePath, json_encode($session, JSON_PRETTY_PRINT));
}

// Handle form to open the session
if (isAdmin($user) && isset($_POST["action"]) && $_POST["action"] === "open") {
    openSession($session);
    saveSession($sessionFilePath, json_encode($session, JSON_PRETTY_PRINT));
}

// Handle form to consume tickets
if (isAdmin($user) && isset($_POST["action"]) && $_POST["action"] === "consumeTickets") {
    markPlayerTicketAsConsumed($session, $users);
    consumeTickets($session);
    saveSession($sessionFilePath, json_encode($session, JSON_PRETTY_PRINT));
    $users = getUsers();
}

// Handle form to book
if (isset($_POST["action"]) && $_POST["action"] === "book") {
    bookPlayer($session, $userId);
    saveSession($sessionFilePath, json_encode($session, JSON_PRETTY_PRINT));
}
if (isAdmin($user) && isset($_POST["action"]) && $_POST["action"] === "bookPlayer") {
    bookPlayer($session, $_POST["id"]);
    saveSession($sessionFilePath, json_encode($session, JSON_PRETTY_PRINT));
}

// Handle form to unbook
if (isset($_POST["action"]) && $_POST["action"] === "unbook") {
    unbookPlayer($session, $userId);
    saveSession($sessionFilePath, json_encode($session, JSON_PRETTY_PRINT));
}
if (isAdmin($user) && isset($_POST["action"]) && $_POST["action"] === "unbookPlayer") {
    unbookPlayer($session, $_POST["id"]);
    saveSession($sessionFilePath, json_encode($session, JSON_PRETTY_PRINT));
}

// Header
include "../inc/header.html";
generateMenu($user, PAGE_SESSION);

echo '<main>';

echo '<h1>';
if ($session["status"] === SESSION_STATUS_OPEN) {
    echo "Open session";
} else {
    echo "Closed session";
}
echo '</h1>';

// Players
echo '<table>';
echo '<thead><tr><th>Players</th><th>Ticket</th></tr></thead>';
echo '<tbody>';
for ($index = 0; $index < MAX_PLAYERS; $index++) {
    echo '<tr>';
    if (isset($players[$index])) {
        $playerId = $players[$index];
        echo '<td>' . $users[$playerId]["name"] . '</td>';
        if ($users[$playerId]["tickets"] > 0 || in_array($playerId, $session["consumedPlayerTickets"])) {
            echo '<td>OK</td>';
        } else {
            echo '<td></td>';
        }
    } else {
        echo '<td></td>';
        echo '<td></td>';
    }

    echo '</tr>';
}
echo '</tbody>';
echo '</table>';

// Waiting players
echo '<table>';
echo '<thead><tr><th>Waiting players</th><th>Tickets</th></tr></thead>';
echo '<tbody>';
for ($index = 0; $index < MAX_WAITING_PLAYERS; $index++) {
    echo '<tr>';
    if (isset($waitingPlayers[$index])) {
        $playerId = $waitingPlayers[$index];
        echo '<td>' . $users[$playerId]["name"] . '</td>';
        if ($users[$playerId]["tickets"] > 0) {
            echo '<td>OK</td>';
        } else {
            echo '<td></td>';
        }
    } else {
        echo '<td></td>';
        echo '<td></td>';
    }

    echo '</tr>';
}
echo '</tbody>';
echo '</table>';

echo '</main>';

if ($session["status"] === SESSION_STATUS_OPEN && (count($players) < MAX_PLAYERS || count($waitingPlayers) < MAX_WAITING_PLAYERS)) {
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
}

if (isAdmin($user)) {
    // Book a player
    echo '<div class="actions admin">';
    echo '<form action="" method="post">';
    echo '<input type="hidden" name="action" value="bookPlayer"/>';
    echo '<label><select name="id">';
    foreach ($users as $i => $u) {
        if (!in_array($i, $players) && !in_array($i, $waitingPlayers)) {
            echo '<option value="'.$i.'">'.$u["name"].'</option>';
        }
    }
    echo '</select></label>';
    echo '<input type="submit" value="Book"/>';
    echo '</form>';
    echo '</div>';

    // Unbook a player
    echo '<div class="actions admin">';
    echo '<form action="" method="post">';
    echo '<input type="hidden" name="action" value="unbookPlayer"/>';
    echo '<label><select name="id">';
    foreach ($users as $i => $u) {
        if (in_array($i, $players) || in_array($i, $waitingPlayers)) {
            echo '<option value="'.$i.'">'.$u["name"].'</option>';
        }
    }
    echo '</select></label>';
    echo '<input type="submit" value="Unbook"/>';
    echo '</form>';
    echo '</div>';

    echo '<div class="actions admin">';

    // Form to delete the session
    echo '<form action="" method="post">';
    echo '<input type="hidden" name="action" value="delete"/>';
    echo '<input type="submit" value="Delete"/>';
    echo '</form>';

    if ($session["status"] === SESSION_STATUS_OPEN) {
        // Form to close the session
        echo '<form action="" method="post">';
        echo '<input type="hidden" name="action" value="close"/>';
        echo '<input type="submit" value="Close"/>';
        echo '</form>';
    } else {
        // Form to open the session
        echo '<form action="" method="post">';
        echo '<input type="hidden" name="action" value="open"/>';
        echo '<input type="submit" value="Open"/>';
        echo '</form>';
    }

    echo '</div>';

    // Consume tickets
    echo '<div class="actions admin consume-tickets">';
    echo '<table>';
    echo '<thead><tr><th>Players</th><th>Tickets</th></tr></thead>';
    echo '<tbody>';
    for ($index = 0; $index < MAX_PLAYERS; $index++) {
        echo '<tr>';
        if (isset($players[$index])) {
            $playerId = $players[$index];
            echo '<td>' . $users[$playerId]["name"] . '</td>';
            echo '<td>';
            echo '<span class="ticket-count">'.$users[$playerId]["tickets"] .'</span> ??? <span class="ticket-count">'.($users[$playerId]["tickets"] - 1).'</span>';
            echo '</td>';
        } else {
            echo '<td></td>';
            echo '<td></td>';
        }

        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo '<form action="" method="post">';
    echo '<input type="hidden" name="action" value="consumeTickets"/>';
    echo '<input type="submit" value="Consume tickets"/>';
    echo '</form>';
    echo '</div>';
}
include "../inc/footer.html";
