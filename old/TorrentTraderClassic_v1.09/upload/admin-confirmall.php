<?php

require_once("backend/functions.php");
dbconn(false);
loggedinorreturn();
adminonly();

stdhead("Auto Confirm");
require_once("backend/admin-functions.php");
adminmenu();
begin_frame("Auto Confirm");

$res = mysql_query("SELECT id,username,status FROM users WHERE status = 'pending'");

if (mysql_num_rows($res) < 1){
        echo"<BR><BR>None Found, please remember that pending accounts expire after 3 days<BR><BR>";
        end_frame();
        stdfoot();
        die;
}

while ($row = mysql_fetch_array($res)) {
        $id = $row["id"];
        $name = $row["username"];
        mysql_query("UPDATE users SET status='confirmed' WHERE id=$id");
        echo "User " . $name . " Changed to 'confirmed' \n";
        echo " \n";
}

End_frame();

stdfoot();
?>