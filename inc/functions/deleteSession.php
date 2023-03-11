<?php
function deleteSession($baseName) {
    unlink(__DIR__ . "/../../sessions/" . $baseName . ".json");
}
