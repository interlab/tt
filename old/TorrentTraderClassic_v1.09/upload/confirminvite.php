<?
//
// Confirm Invite
//
require_once("backend/functions.php");

$id = 0 + $HTTP_GET_VARS["id"];
$md5 = $HTTP_GET_VARS["secret"];

if (!$id)
httperr();

dbconn();

$res = mysql_query("SELECT COUNT(*) FROM users") or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_row($res);

if ($arr[0] >= $invites)
stderr("Sorry", "The current user account limit (" . number_format($invites) . ") has been reached. Inactive accounts are pruned all the time, please check back again later...");

$res = mysql_query("SELECT editsecret, secret, status FROM users WHERE id = $id");
$row = mysql_fetch_array($res);

if (!$row)
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
<form method="post" action="takeconfirminvite.php?id=<?= $id?>&secret=<?= $psecret ?>">
<CENTER><table border="0" cellspacing=0 cellpadding="3" width=90%>
<tr><td align="right" class="heading">Desired Username:</td><td align=left><input type="text" size="40" name="wantusername" /></td></tr>
<tr><td align="right" class="heading">Pick a password:</td><td align=left><input type="password" size="40" name="wantpassword" /></td></tr>
<tr><td align="right" class="heading">Enter password again:</td><td align=left><input type="password" size="40" name="passagain" /></td></tr>

</td></tr>
<tr><td align="right" class="heading"></td><td align=left><input type=checkbox name=rulesverify value=yes> I have read the site <a href=/rules.php/ target=_blank font color=red>rules</a> page.<br>
<input type=checkbox name=faqverify value=yes> I agree to read the <a href=/faq.php/ target=_blank font color=red>FAQ</a> before asking questions.<br>
<input type=checkbox name=ageverify value=yes> I am at least 13 years old.</td></tr>
<tr><td colspan="2" align="center"><input type=submit value="Sign up! (PRESS ONLY ONCE)" style='height: 25px'></td></tr>
</table></CENTER>
</form>
<?
end_frame();
stdfoot();

?>