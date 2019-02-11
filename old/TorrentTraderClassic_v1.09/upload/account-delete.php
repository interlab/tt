<?php

require "backend/functions.php";
dbconn();

if ($HTTP_SERVER_VARS["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (!$username || !$password)
        bark("Couldn't delete account", "Please fill out the form correctly.");

    $password = md5($password);
    $res = mysql_query("SELECT * FROM users WHERE username = " . sqlesc($username) . " && password=" .
    sqlesc($password)) or sqlerr();
    if (mysql_num_rows($res) != 1)
        bark("Couldn't delete account", "Bad user name or password. Please verify that all entered information is correct.");
    $arr = mysql_fetch_assoc($res);

	//if ($arr["status"] == "confirmed")
    //bark("Couldn't delete account", "Sorry, you can not delete a confirmed account.");

    $id = $arr['id'];
    $res = mysql_query("DELETE FROM users WHERE id=$id") or sqlerr();
    if (mysql_affected_rows() != 1)
        bark("Couldn't delete account", "Unable to delete the account.");
    if ($CURUSER)
        $x = $CURUSER['username'];
    else
        $x = "an anonymous user";
    bark("Success", "The account <b>$username</b> were deleted.", Success);
}

stdhead("Delete account");

begin_frame("Delete Account");
?>
Thanks for using <b><?= $SITENAME ?></b>. If you are no longer in need of your account you can delete it using the form below. All your account details will be deleted but any .torrents which you uploaded will remian in place until all users have finished downloading from them.
Please be aware that deleted accounts cannot be recovered and that any priviledges accumulated on one account cannot be transferred if you choose to sign up again.<br><br>
<table border=0 cellspacing=0 cellpadding=5>
<form method=post action=account-delete.php>
<tr><td class=rowhead><b>Username:</b></td><td><input size=40 name=username></td></tr>
<tr><td class=rowhead><b>Password:</b></td><td><input type=password size=40 name=password></td></tr>
<tr><td colspan=2 align="right"><input type=submit value='Delete Account'></td></tr>
</table>
</form>

<?php
end_frame();
stdfoot();
