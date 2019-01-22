<?

require_once("backend/functions.php");
dbconn();
loggedinorreturn();

if (!$INVITEONLY){
stdhead("Invite");
begin_frame("Invite");
echo "<BR><BR>Invites are disabled, please use the register link.<BR><BR>";
end_frame();
stdfoot();
exit;
}


stdhead("Invite");
begin_frame("Invite");

$res = mysql_query("SELECT COUNT(*) FROM users") or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_row($res);

if ($arr[0] >= $invites){
	print("Sorry, The current user account limit (" . number_format($invites) . ") has been reached. Inactive accounts are pruned all the time, please check back again later...");
	end_frame();
	exit;
}

if($CURUSER["invites"] == 0){
	print("Sorry, No invites!");
	end_frame();
	exit;
}
?>

<p>
<form method="post" action="takeinvite.php">
<table border="0" cellspacing=0 cellpadding="3">
<tr valign=top><td align="right" class="heading"><B>Email Address:</B></td><td align=left><input type="text" size="40" name="email" />
<table width=250 border=0 cellspacing=0 cellpadding=0><tr><td class=embedded><font class=small>Please make sure this is a valid email address, the recipient will receive a confirmation email.</td></tr>
</font></td></tr></table>
<tr><td align="right" class="heading"><B>Message:</B></td><td align=left><textarea name="mess" rows="10" cols="80"></textarea>
</td></tr>
<tr><td colspan="2" align="center"><input type=submit value="Send Invite (PRESS ONLY ONCE)" style='height: 25px'></td></tr>
</table>
</form>
<?
end_frame();
stdfoot();

?>