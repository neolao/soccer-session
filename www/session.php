<?php
include "../inc/constants.php";
include "../inc/functions.php";

// Load config
$config = getConfig();

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
$guests = &$session["guests"];

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
    consumeTickets($session, $users);
    countCaptains($session, $users);
    saveSession($sessionFilePath, json_encode($session, JSON_PRETTY_PRINT));
    saveUsers($users);
}

// Handle form to mark guest ticket as paid
if (isAdmin($user) && isset($_POST["action"]) && $_POST["action"] === "markGuestTicketAsPaid") {
    markGuestTicketAsPaid($session, $_POST["id"]);
    saveSession($sessionFilePath, json_encode($session, JSON_PRETTY_PRINT));
}

// Handle form to unmark guest ticket as paid
if (isAdmin($user) && isset($_POST["action"]) && $_POST["action"] === "unmarkGuestTicketAsPaid") {
    unmarkGuestTicketAsPaid($session, $_POST["id"]);
    saveSession($sessionFilePath, json_encode($session, JSON_PRETTY_PRINT));
}

// Handle form to close session and consume tickets
if (isAdmin($user) && isset($_POST["action"]) && $_POST["action"] === "closeAndConsumeTickets") {
    closeSession($session);
    markPlayerTicketAsConsumed($session, $users);
    consumeTickets($session, $users);
    countCaptains($session, $users);
    saveSession($sessionFilePath, json_encode($session, JSON_PRETTY_PRINT));
    saveUsers($users);
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
if (isAdmin($user) && isset($_POST["action"]) && $_POST["action"] === "addGuest") {
    bookGuest($session, $_POST["name"]);
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

// Handle form to mark/unmark a player as captain
if (isAdmin($user) && isset($_POST["action"]) && $_POST["action"] === "markAsCaptain") {
    markPlayerAsCaptain($session, $_POST["id"]);
    saveSession($sessionFilePath, json_encode($session, JSON_PRETTY_PRINT));
}
if (isAdmin($user) && isset($_POST["action"]) && $_POST["action"] === "unmarkAsCaptain") {
    unmarkPlayerAsCaptain($session, $_POST["id"]);
    saveSession($sessionFilePath, json_encode($session, JSON_PRETTY_PRINT));
}

// Handle form to choose random captains
if (isAdmin($user) && isset($_POST["action"]) && $_POST["action"] === "randomCaptains") {
    $includeGuests = (isset($_POST["includeGuests"]) && $_POST["includeGuests"] === "on");
    chooseRandomCaptains($session, $users, $includeGuests);
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
echo '<div class="table-wrapper">';
echo '<table>';
echo '<thead><tr><th>Players</th><th>Ticket</th>';
if (isAdmin($user)) {
    echo '<th class="center">🎖</th>';
    echo '<th>Actions</th>';
}
echo '</tr></thead>';
echo '<tbody>';
for ($index = 0; $index < MAX_PLAYERS; $index++) {
    echo '<tr>';
    if (isset($players[$index])) {
        $playerId = $players[$index];
        echo '<td>' . getPlayerName($playerId, $users);
        if (isCaptain($session, $playerId)) {
            echo "🎖";
        }
        echo '</td>';
        if (getPlayerTickets($playerId, $users, $session) > 0 || in_array($playerId, $session["consumedPlayerTickets"])) {
            echo '<td>Paid</td>';
        } else {
            echo '<td></td>';
        }
    } else {
        echo '<td></td>';
        echo '<td></td>';
    }

    if (isAdmin($user)) {
        echo '<td>';
        if (isset($players[$index])) {
            echo '<span class="captain-count">' . getPlayerCaptainCount($playerId, $users) . '</span>';
        }
        echo '</td>';
        echo '<td>';

        if (isset($players[$index])) {
            // Unbook
            /*
            echo '<form action="" method="post">';
            echo '<input type="hidden" name="action" value="unbookPlayer"/>';
            echo '<input type="hidden" name="id" value="'.$playerId.'"/>';
            echo '<input type="submit" value="Unbook"/>';
            echo '</form>';
            //*/

            // Captain
            if (isCaptain($session, $playerId)) {
                echo '<form action="" method="post">';
                echo '<input type="hidden" name="action" value="unmarkAsCaptain"/>';
                echo '<input type="hidden" name="id" value="'.$playerId.'"/>';
                echo '<input type="submit" value="🎖" class="unmark-captain"/>';
                echo '</form>';
            } elseif (!hasEnoughCaptain($session)) {
                echo '<form action="" method="post">';
                echo '<input type="hidden" name="action" value="markAsCaptain"/>';
                echo '<input type="hidden" name="id" value="'.$playerId.'"/>';
                echo '<input type="submit" value="🎖"/>';
                echo '</form>';
            }

            // Mark guest ticket as paid
            if (isGuest($playerId)) {
                if (getPlayerTickets($playerId, $users, $session) === 0) {
                    echo '<form action="" method="post">';
                    echo '<input type="hidden" name="action" value="markGuestTicketAsPaid"/>';
                    echo '<input type="hidden" name="id" value="'.$playerId.'"/>';
                    echo '<input type="submit" value="💰"/>';
                    echo '</form>';
                } else {
                    echo '<form action="" method="post">';
                    echo '<input type="hidden" name="action" value="unmarkGuestTicketAsPaid"/>';
                    echo '<input type="hidden" name="id" value="'.$playerId.'"/>';
                    echo '<input type="submit" value="💰" class="unmark-guest-ticket"/>';
                    echo '</form>';
                }
            }
        }

        echo '</td>';
    }

    echo '</tr>';
}
echo '</tbody>';
echo '</table>';
echo '</div>';

// Waiting players
echo '<div class="table-wrapper">';
echo '<table>';
echo '<thead><tr><th>Waiting players</th><th>Tickets</th></tr></thead>';
echo '<tbody>';
for ($index = 0; $index < MAX_WAITING_PLAYERS; $index++) {
    echo '<tr>';
    if (isset($waitingPlayers[$index])) {
        $playerId = $waitingPlayers[$index];
        echo '<td>' . getPlayerName($playerId, $users) . '</td>';
        if (getPlayerTickets($playerId, $users, $session) > 0) {
            echo '<td>Paid</td>';
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
echo '</div>';

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

    if (isset($config["linkToBuyTicket"]) && !empty($config["linkToBuyTicket"])) {
        echo '<span class="buy-ticket"><a href="'.$config["linkToBuyTicket"].'">Buy a ticket</a></span>';
    }
    echo '</div>';
}

if (isAdmin($user)) {
    // Random captains
    echo '<div class="actions admin">';
    echo '<form action="" method="post">';
    echo '<input type="hidden" name="action" value="randomCaptains"/>';
    echo '<input type="submit" value="Choose random captains"/>';
    echo '<label><input type="checkbox" name="includeGuests"> Include guests</label>';
    echo '</form>';
    echo '</div>';

    // Book a player
    echo '<div class="actions admin">';
    echo '<form action="" method="post">';
    echo '<input type="hidden" name="action" value="bookPlayer"/>';
    echo '<label><select name="id">';
    foreach ($users as $i => $u) {
        if (!in_array($i, $players) && !in_array($i, $waitingPlayers)) {
            echo '<option value="'.$i.'">'.getPlayerName($i, $users).'</option>';
        }
    }
    echo '</select></label>';
    echo '<input type="submit" value="Book"/>';
    echo '</form>';
    echo '</div>';

    // Unbook a player
    ///*
    echo '<div class="actions admin">';
    echo '<form action="" method="post">';
    echo '<input type="hidden" name="action" value="unbookPlayer"/>';
    echo '<label><select name="id">';
    foreach ($players as $i) {
        echo '<option value="'.$i.'">'.getPlayerName($i, $users).'</option>';
    }
    foreach ($waitingPlayers as $i) {
        echo '<option value="'.$i.'">'.getPlayerName($i, $users).'</option>';
    }
    echo '</select></label>';
    echo '<input type="submit" value="Unbook"/>';
    echo '</form>';
    echo '</div>';
    //*/

    // Add a guest
    echo '<div class="actions admin">';
    echo '<form action="" method="post">';
    echo '<input type="hidden" name="action" value="addGuest"/>';
    echo '<label>Guest: <input type="text" name="name" value="" required/></label>';
    echo '<input type="submit" value="Add guest"/>';
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

        // Form to close the session and consume tickets
        echo '<form action="" method="post">';
        echo '<input type="hidden" name="action" value="closeAndConsumeTickets"/>';
        echo '<input type="submit" value="Close and consume tickets"/>';
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
    echo '<thead><tr><th>Players</th><th>Tickets</th><th>Captain</th></tr></thead>';
    echo '<tbody>';
    for ($index = 0; $index < MAX_PLAYERS; $index++) {
        if (isset($players[$index]) && !isGuest($players[$index])) {
            $playerId = $players[$index];
            echo '<tr>';
            echo '<td>' . getPlayerName($playerId, $users) . '</td>';
            echo '<td>';
            echo '<span class="ticket-count">'.getPlayerTickets($playerId, $users, $session) .'</span>→<span class="ticket-count">'.(getPlayerTickets($playerId, $users, $session) - 1).'</span>';
            echo '</td>';
            echo '<td>';
            if (isCaptain($session, $playerId)) {
                echo '<span class="captain-count">'.getPlayerCaptainCount($playerId, $users) .'</span>→<span class="ticket-count">'.(getPlayerCaptainCount($playerId, $users) + 1).'</span>';
            }
            echo '</td>';
            echo '</tr>';
        }
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
