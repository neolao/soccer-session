<?php
function removeTicket($id) {
    $filePath = __DIR__ . "/../../users.json";

    $size = filesize($filePath);
    $fp = fopen($filePath, "r+");
    if (flock($fp, LOCK_EX)) {
        $content = fread($fp, $size);
        $users = json_decode($content, true);

        foreach ($users as $userId => &$user) {
            if ($userId === $id) {
                $tickets = isset($user["tickets"])?$user["tickets"]:0;
                if ($tickets > 0) {
                    $user["tickets"] = $tickets - 1;
                }
            }
        }

        ftruncate($fp, 0);
        fseek($fp, 0);
        fwrite($fp, json_encode($users, JSON_PRETTY_PRINT));
        fflush($fp);
        flock($fp, LOCK_UN);
    }
    fclose($fp);


}
