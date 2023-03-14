<?php
function isGuest($id) {
    return (substr($id, 0, 8) === '[guest] ');
}
