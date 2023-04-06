<?php
function unmarkGuestTicketAsPaid(&$session, $playerId) {
    $consumedGuestTickets = &$session["consumedGuestTickets"];

    $consumedGuestTickets = array_filter($consumedGuestTickets, function ($element) use ($playerId) {
        return $element !== $playerId;
    });
    $consumedGuestTickets = array_values($consumedGuestTickets);
    $consumedGuestTickets = array_unique($consumedGuestTickets);
}
