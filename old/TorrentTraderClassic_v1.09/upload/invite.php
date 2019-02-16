<?php

require_once("backend/functions.php");
dbconn();

// Confirm Invite
if (isset($_GET['confirm'])) {
    $id = (int) ($_GET["id"] ?? 0);
    $md5 = $_GET["secret"] ?? '';

    if (! $id)
        httperr();

    $count = DB::fetchColumn("SELECT COUNT(*) FROM users");

    if ($count >= $invites)
        stderr("Sorry", "The current user account limit (" . number_format($invites) .
            ") has been reached. Inactive accounts are pruned all the time, please check back again later...");

    $row = DB::fetchAssoc("SELECT editsecret, secret, status FROM users WHERE id = $id");

    if (! $row)
        httperr();

    if ($row["status"] != "pending") {
        header("Refresh: 0; url=account-confirm-ok.php?type=confirmed");
        exit();
    }

    $sec = hash_pad($row["editsecret"]);
    if ($md5 != md5($sec))
        httperr();

    $secret = $row["secret"];
    $psecret = md5($row["editsecret"]);
    stdhead("Confirm Invite");
    begin_frame("Confirm Invite");
    ?>

    Note: You need cookies enabled to sign up or log in.
    <p>
    <form method="post" action="takeconfirminvite.php?id=<?= $id ?>&secret=<?= $psecret ?>">
    <CENTER><table border="0" cellspacing=0 cellpadding="3" width=90%>
    <tr><td align="right" class="heading">Desired Username:</td>
    <td align=left><input type="text" size="40" name="wantusername" /></td></tr>
    <tr><td align="right" class="heading">Pick a password:</td>
    <td align=left><input type="password" size="40" name="wantpassword" /></td></tr>
    <tr><td align="right" class="heading">Enter password again:</td>
    <td align=left><input type="password" size="40" name="passagain" /></td></tr>

    </td></tr>
    <tr><td align="right" class="heading"></td>
    <td align=left><input type=checkbox name=rulesverify value=yes> I have read the site 
    <a href=/rules.php/ target=_blank font color=red>rules</a> page.<br>
    <input type=checkbox name=faqverify value=yes> I agree to read the 
    <a href=/faq.php/ target=_blank font color=red>FAQ</a> before asking questions.<br>
    <input type=checkbox name=ageverify value=yes> I am at least 13 years old.</td></tr>
    <tr><td colspan="2" align="center">
        <input type=submit value="Sign up! (PRESS ONLY ONCE)" style='height: 25px'>
    </td></tr>
    </table></CENTER>
    </form>
    <?php
    end_frame();
    stdfoot();

    die('');
}


loggedinorreturn();

if (! $INVITEONLY) {
    stdhead("Invite");
    begin_frame("Invite");
    echo "<BR><BR>Invites are disabled, please use the register link.<BR><BR>";
    end_frame();
    stdfoot();
    exit;
}


stdhead("Invite");
begin_frame("Invite");

$num = DB::fetchColumn("SELECT COUNT(*) FROM users");

if ($num >= $invites) {
	print("Sorry, The current user account limit (" . number_format($invites)
        . ") has been reached. Inactive accounts are pruned all the time, please check back again later...");
	end_frame();
	exit;
}

if (! $CURUSER["invites"]) {
	print("Sorry, No invites!");
	end_frame();
	exit;
}
?>

<p>
<form method="post" action="takeinvite.php">
<table border="0" cellspacing=0 cellpadding="3">
<tr valign=top><td align="right" class="heading"><B>Email Address:</B></td>
<td align=left><input type="text" size="40" name="email" />
<table width=250 border=0 cellspacing=0 cellpadding=0>
<tr><td class=embedded><font class=small>Please make sure this is a valid 
email address, the recipient will receive a confirmation email.</td></tr>
</font></td></tr></table>
<tr><td align="right" class="heading"><B>Message:</B></td>
<td align=left><textarea name="mess" rows="10" cols="80"></textarea>
</td></tr>
<tr><td colspan="2" align="center"><input type=submit value="Send Invite (PRESS ONLY ONCE)" style='height: 25px'></td></tr>
</table>
</form>
<?php
end_frame();
stdfoot();
