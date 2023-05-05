<?php
function generateMenu($user, $currentPage) {
    echo '<menu>';

    // Sessions
    echo '<li';
    if ($currentPage === PAGE_SESSIONS) {
        echo ' class="selected"';
    }
    echo '><a href="/">Sessions</a></li>';

    // Closed sessions
    echo '<li';
    if ($currentPage === PAGE_CLOSED_SESSIONS) {
        echo ' class="selected"';
    }
    echo '><a href="/closed-sessions.php">Closed</a></li>';

    // Session
    if ($currentPage === PAGE_SESSION) {
        $date = $_GET["date"];
        $sessionFilePath = __DIR__ . "/../../sessions/" . $date . ".json";
        $session = getSession($sessionFilePath);
        echo '<li class="selected">'.date("Y-m-d", $session["timestamp"]).'</li>';
    }

    // Users
    if (isAdmin($user)) {
        echo '<li';
        if ($currentPage === PAGE_USERS) {
            echo ' class="selected"';
        }
        echo '><a href="/users.php">Users</a></li>';
    }
    echo '</menu>';
}
