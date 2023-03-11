<?php
function addSession($baseName) {
    touch(__DIR__ . "/../../sessions/" . $baseName . ".json");
}
