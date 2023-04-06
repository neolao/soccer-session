<?php
function markGuestTicketAsPaid(&$session, $playerId) {
    $consumedGuestTickets = &$session["consumedGuestTickets"];

    array_push($consumedGuestTickets, $playerId);

    $consumedGuestTickets = array_unique($consumedGuestTickets);
}
