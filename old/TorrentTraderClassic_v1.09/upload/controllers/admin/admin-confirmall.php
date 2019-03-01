<?php

dbconn(false);
loggedinorreturn();
adminonly();

stdhead("Auto Confirm");
require_once TT_BACKEND_DIR . '/admin-functions.php';

adminmenu();
begin_frame("Auto Confirm");

$res = DB::fetchAll("SELECT id, username, status FROM users WHERE status = 'pending'");

if (!$res) {
    echo"<BR><BR>None Found, please remember that pending accounts expire after 3 days<BR><BR>";
    end_frame();
    stdfoot();
    die;
}

foreach ($res as $row) {
    $id = $row["id"];
    $name = $row["username"];
    DB::executeUpdate('UPDATE users SET status = \'confirmed\' WHERE id = '. $row["id"]);
    echo "User " . $row["username"] . " Changed to 'confirmed'";
}

end_frame();

stdfoot();
