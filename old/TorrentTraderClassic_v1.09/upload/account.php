<?
//
// CSS and Language updated 29.NOV.05
//
require_once("backend/functions.php");
dbconn(false);
loggedinorreturn();

//GET ID
$id = $CURUSER["id"];

//DO SOME SQL CALC'S ETC
($r = @mysql_query("SELECT * FROM users WHERE id=$id"));
($user = mysql_fetch_array($r));

$res = mysql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"]) or print(mysql_error());
$arr = mysql_fetch_row($res);
$messages = $arr[0];
$res = mysql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " and unread='yes'") or print(mysql_error());
$arr = mysql_fetch_row($res);
$unread = $arr[0];

if ($user["downloaded"] > 0)
    {
      $ratio = number_format($user["uploaded"] / $user["downloaded"], 2);
      $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
    }
    else
      if ($user["uploaded"] > 0)
        $ratio = "Inf.";
      else
        $ratio = "---";

$res = mysql_query("SELECT name,flagpic FROM countries WHERE id=" . $user["country"] . " LIMIT 1") or sqlerr();
if (mysql_num_rows($res) == 1) {
	$arr = mysql_fetch_assoc($res);
	$country = "<img src=\"images/flag/$arr[flagpic]\" alt=\"$arr[name]\" style=\"margin-left: 8pt\" />";
	$country1 = "$arr[name]";

}

$joindate = "$user[added] (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($user["added"])) . " ago)";
$lastseen = $user["last_access"];
if ($lastseen == "0000-00-00 00:00:00") {
	$lastseen = "never";
}
else
{
	$lastseen .= " (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($lastseen)) . " ago)";
	$res = mysql_query("SELECT COUNT(*) FROM comments WHERE user=" . $CURUSER["id"]) or sqlerr();
	$arr3 = mysql_fetch_row($res);
	$torrentcomments = $arr3[0];

	$res = mysql_query("SELECT COUNT(*) FROM forum_posts WHERE userid=" . $CURUSER["id"]) or sqlerr();
	$arr3 = mysql_fetch_row($res);
	$forumposts = $arr3[0];

	$res = mysql_query("SELECT COUNT(*) FROM ratings WHERE user=" . $CURUSER["id"]) or sqlerr();
	$arr3 = mysql_fetch_row($res);
	$torrentratings = $arr3[0];

	$res = mysql_query("SELECT COUNT(*) FROM torrents WHERE owner=" . $CURUSER["id"]) or sqlerr();
	$arr3 = mysql_fetch_row($res);
	$torrenttorrents = $arr3[0];
}

//get days until ban, if warned by ratioban system
$userid = $user['id'];
$res_rws = mysql_query("SELECT *, TO_DAYS(NOW()) - TO_DAYS(warntime) as difference FROM ratiowarn WHERE userid=$userid");
$num_row_rws = mysql_num_rows($res_rws);
if ($num_row_rws > 0){
    $arr_rws = mysql_fetch_array($res_rws);
    if($arr_rws['warned'] == 'yes'){
        $banned = $arr_rws['banned'];
        if($banned == 'no'){
            $timeleft = ($arr_rws['difference'] - $RATIOWARN_BAN)/-1;
        }else{
            $timeleft = "null";
        }
    }
}

//
/////////// BEGIN PAGE LAYOUT ///////////
//

stdhead("User CP");
begin_frame("" . LOGGEDINAS . " $CURUSER[username]");

//DO SOME ERROR CHECKS AND POST MSG'S
if ($message != "")
	bark2("Editting Failed", $message);
elseif ($edited) {
	if ($mailsent)
		bark2("Success", "Profile updated!<br />Confirmation email has been sent!");
	else
		bark2("Success", "Profile updated!");
}
elseif ($_GET["emailch"])
	bark2("Success", "Email address changed!");
elseif ($deleted)
	bark2("Success", "The message has been deleted!");
elseif ($alldeleted=="yes")
	bark2("Success", "All messages has been deleted!")
?>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%" valign="top">
<table cellspacing="2" cellpadding="5" border="0" width="100%" id="table21">
<tr>
<td width="50%" class="text">
<?
$avatar = $CURUSER["avatar"];
if (!$avatar) {
	$avatar = "images/default_avatar.gif";
}
print("<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"2\">\n");
print("<tr>\n");
print("<td rowspan=\"2\" valign=\"top\"><a href=\"account-settings.php\"><img src=\"$avatar\" border=\"0\" width=\"80\" height=\"80\"></a><br><br></td>\n");
?>
<td width="90%" valign="top">
<?=$country?><br><br>
<table width="100%">
<tr><td><?
print ("" . USERNAME . ": <b>" . $CURUSER["username"] . "</b><br>");
print ("" . CUSTOMTITLE . ": <b>" . strip_tags($user["title"]) . "</b><br>");
print ("" . WORD_CLASS . ": <b>" . get_user_class_name($CURUSER["class"]) . "</b><br>");
print ("" . EMAIL_ADDRESS . ": <b>" . $CURUSER["email"] . "</b><br>");
print ("" . IP_ADDRESS . ": <b>" . $CURUSER["ip"] . "</b><br>");
print ("" . JOIN_DATE . ": <b>" . $CURUSER["added"] . "</b><br>");
print ("" . LAST_ACCESS . ": <b>" . $lastseen . "</b><br>");
?>
</td></tr>
</table>
<br>
<table width="100%">
<tr>
<? 
print("<td>" . AGE . ": " . $user["age"] . "</td>");
print("<td>" . GENDER . ": " . $user["gender"] . "</td>");
print("<td>" . COUNTRY . ": " . $country1 . "</td>");
ECHO "</td></tr>";
print("<td>" . CLIENT . ": " . $user["client"] . "</td>");
print("<td>" . UPLOADED . ": " . mksize($CURUSER["uploaded"]) . "</td>");
print("<td>" . DOWNLOADED . ": " . mksize($CURUSER["downloaded"]) . "</td>");
?>
</tr><tr>
<?
print("<td>" . RATIO . ": " . $ratio . "</td>");
print("<td>" . FORUM_POSTS . ": " . $forumposts . "</td>");
print("<td>" . TORRENTS_POSTED . ": " . $torrenttorrents . "</td>");
?>
</tr><tr>
<?
print("<td>" . TORRENT_COMMENTS . ": " . $torrentcomments . "</td>");
print("<td>" . RATINGS . ": " . $torrentratings . "</td>");
print("<td>" . ACCOUNT_PRIVACY_LV . ": " . $user["privacy"] . "</td>");
?>
</tr><tr><?
print("<td>" . DONATED . ": " . $user["donated"] . "</td>");
print("<td>" . WARNED . ": " . $user["warned"] . "</td>");
if($arr_rws['warned'] == 'yes'){
    print("<td>Days until ban: $timeleft</td>");
}
?>
<td>&nbsp;</td>
</tr><tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
</table>
<br />
<FORM METHOD="LINK" ACTION="account-settings.php">
<input type="submit" value="<? print("" . ACCOUNT_ADJUST_SETTINGS . "\n"); ?>" style="font-family: Verdana; font-size: 7pt; color: #000000; border: 1px solid #808080; background-color: #C0C0C0">
</FORM>
<?
print("<a href=account-history.php?id=$CURUSER[id]&action=viewposts><b>" . VIEW_POSTS . "</b></a><br>");
print("<a href=account-history.php?id=$CURUSER[id]&action=viewcomments><b>" . VIEW_COMMENTS . "</b></a><br>");
print("<a href=account.php?action=mytorrents><b>" . VIEW_MYTORRENT . "</b></a><br>");
?>
<br>
</td>
</tr>
</table></td></tr></table>
		</td>
<td></td>
	</tr>
</table>

<?
end_frame();

//PRIVATE MESSAGES FRAME STARTS HERE
begin_frame("" . ACCOUNT_YOUR_MESSAGES . "\n");

$res = mysql_query("SELECT * , UNIX_TIMESTAMP(added) as utadded FROM messages WHERE receiver=" . $CURUSER["id"] . " ORDER BY added DESC") or die("barf!");
if (mysql_num_rows($res) == 0)
	print("<br /><p align=center><b>" . ACCOUNT_YOU_HAVE . "<font color=\"#CC0000\"><b> 0 </b></font>" . ACCOUNT_MESSAGES . "</b></p>\n");

else

while ($arr = mysql_fetch_assoc($res))
{
	if (is_valid_id($arr["sender"]))
    	{
      		$res2 = mysql_query("SELECT username FROM users WHERE id=" . $arr["sender"]) or sqlerr();
		$arr2 = mysql_fetch_assoc($res2);
		$sender = "<a href=account-details.php?id=" . $arr["sender"] . ">" . $arr2["username"] . "</a>";
	}
    	else
		$sender = "System";

	print ("<p align='right'><a href='account-inbox.php?deleteall=yes'>Delete ALL messages</a></p>");

	print("<table border=0 width=100% cellspacing=0 cellpadding=2><tr><td bgcolor=#CCCCCC><img border=0 src=images/envelope.gif></td><td width=80% bgcolor=#ADACAC>\n");
	print("" . FROM . " <b>$sender</b> " . AT . "\n" . get_date_time($arr["utadded"] , $CURUSER[tzoffset] ) . " GMT\n");
	if ($arr["unread"] == "yes")
	{
		print("<b>(" . ACCOUNT_NEW . ")</b>");
		mysql_query("UPDATE messages SET unread='false' WHERE id=" . $arr["id"]) or die("arghh");
   	}
    	print("</td><td bgcolor=#ADACAC width=20% align=right>");
        if ($arr["sender"] != "0") { print("<a href=account-inbox.php?receiver=". $arr2["username"] ."&replyto=". $arr["id"].">" . ACCOUNT_REPLY . "</a> | "); }
        print("<a href=account-inbox.php?deleteid=" . $arr["id"] . ">" . ACCOUNT_DELETE . "</a></td></tr><tr><td colspan=3>\n");
	print(format_comment($arr["msg"]));
	print("<br />\n"
	    	."<br /></td></tr></table>\n");
}
print("<p align=\"center\"><a href=account-inbox.php>" . ACCOUNT_SEND_MSG . "</a></p>\n");

end_frame();


//MY TORRENTS PAGE STARTS HERE
if ($action=="mytorrents")
{
begin_frame("" . ACCOUNT_YOUR_TORRENTS . "\n", center);

$where = "WHERE owner = " . $CURUSER["id"] ."";
$res = mysql_query("SELECT COUNT(*) FROM torrents $where");
$row = mysql_fetch_array($res);
$count = $row[0];

if (!$count) {
?>
<b><font color="#CC0000"><? print("" . ACCOUNT_NO_UPLOADS_FOUND . "\n"); ?></font></b><br />
<? print("" . WHEN_YOU . "\n"); ?> <a href="torrents-upload.php"><? print("" . UPLOAD . "\n"); ?></a><? print("" . ACCOUNT_A_TORRENT_FILE  . "\n"); ?>.<br />
<?
}
else {
	list($pagertop, $pagerbottom, $limit) = pager(40, $count, "account.php?action=mytorrents&");
	$res = mysql_query("SELECT torrents.type, torrents.comments, torrents.leechers, torrents.nfo, torrents.seeders,torrents.owner,torrents.banned, IF(torrents.numratings < $minvotes, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.id, categories.name AS cat_name, categories.image AS cat_pic, torrents.name, save_as, numfiles, added, size, views, visible, hits, times_completed, category FROM torrents LEFT JOIN categories ON torrents.category = categories.id $where ORDER BY id DESC $limit");
	torrenttable($res, "mytorrents");
	print($pagerbottom);
}

end_frame();
}


stdfoot();

?>