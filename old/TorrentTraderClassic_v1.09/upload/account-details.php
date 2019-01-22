<?
ob_start("ob_gzhandler");
require "backend/functions.php";
dbconn(false);

loggedinorreturn();

//SOME FUNCTIONS AND SQL
function maketable($res)
{
  $ret = "<table class=table_table border=1 cellspacing=0 cellpadding=2>" .
    "<tr><td class=table_head>" . NAME . "</td><td class=table_head align=center>" . SIZE . "</td><td class=table_head align=center>" . UPLOADED . "</td>\n" .
    "<td class=table_head align=center>" . DOWNLOADED . "</td><td class=table_head align=center>" . RATIO . "</td></tr>\n";
  while ($arr = mysql_fetch_assoc($res))
  {
    $res2 = mysql_query("SELECT name,size FROM torrents WHERE id=$arr[torrent] ORDER BY name");
    $arr2 = mysql_fetch_assoc($res2);
    if ($arr["downloaded"] > 0)
    {
      $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 2);
      $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
    }
    else
      if ($arr["uploaded"] > 0)
        $ratio = "Inf.";
      else
        $ratio = "---";
    $ret .= "<tr><td class=table_col1><a href=torrents-details.php?id=$arr[torrent]&amp;hit=1><b>" . htmlspecialchars($arr2[name]) . "</b></a></td><td align=center class=table_col2>" . mksize($arr2["size"]) . "</td><td align=center class=table_col1>" . mksize($arr["uploaded"]) . "</td><td align=center class=table_col2>" . mksize($arr["downloaded"]) . "</td><td align=center class=table_col1>$ratio</td></tr>\n";
  }
  $ret .= "</table>\n";
  return $ret;
}

$id = (int)$_GET["id"];

if (!is_valid_id($id))
  bark("Can't show details", "Bad ID.");

$r = @mysql_query("SELECT * FROM users WHERE id=$id") or sqlerr();
$user = mysql_fetch_array($r) or  bark("Can't show details", "No user with ID $id.");
if ($user["status"] == "pending") die;
$r = mysql_query("SELECT * FROM torrents WHERE owner=$id ORDER BY name ASC") or sqlerr();
if (mysql_num_rows($r) > 0)
{
  $torrents = "<table class=table_table border=1 cellspacing=0 cellpadding=2>\n" .
    "<tr><td class=table_head>" . NAME . "</td><td class=table_head>" . SEEDS . "</td><td class=table_head>" . LEECH . "</td></tr>\n";
  while ($a = mysql_fetch_assoc($r))
  {
      $torrents .= "<tr><td class=table_col1><a href=torrents-details.php?id=" . $a["id"] . "&hit=1><b>" . htmlspecialchars($a["name"]) . "</b></a></td>" .
        "<td align=right class=table_col2>$a[seeders]</td><td align=right class=table_col1>$a[leechers]</td></tr>\n";
  }
  $torrents .= "</table>";
}

$res87 = mysql_query("SELECT COUNT(*) FROM torrents WHERE owner=$id") or sqlerr();
$arr387 = mysql_fetch_row($res87);
$torrenttorrents = $arr387[0];

if ($user["ip"] && !(get_user_class() < UC_JMODERATOR && $user["class"] >= UC_UPLOADER))
{
	$limited = $CURUSER['id'] != $id && get_user_class() < UC_JMODERATOR;
  if ($limited)
    $ip = substr($user["ip"], 0, strrpos($user["ip"], ".") + 1) . "xxx";
  else
    $ip = $user["ip"];
  $dom = @gethostbyaddr($user["ip"]);
  if ($dom == $user["ip"] || @gethostbyname($dom) != $user["ip"])
    $addr = $ip;
  else
  {
    $dom = strtoupper($dom);
    $domparts = explode(".", $dom);
    $domain = $domparts[count($domparts) - 2];
    if ($domain == "COM" || $domain == "CO" || $domain == "NET" || $domain == "NE" || $domain == "ORG" || $domain == "OR" )
      $l = 2;
    else
      $l = 1;
    if ($limited)
      while (substr_count($dom, ".") > $l)
        $dom = substr($dom, strpos($dom, ".") + 1);
    $addr = "$ip ($dom)";
  }
}
if ($user[added] == "0000-00-00 00:00:00")
  $joindate = 'N/A';
else
  $joindate = "$user[added] (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($user["added"])) . " ago)";
$lastseen = $user["last_access"];
if ($lastseen == "0000-00-00 00:00:00")
  $lastseen = "never";
else
{
  $lastseen .= " (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($lastseen)) . " ago)";
  $res = mysql_query("SELECT COUNT(*) FROM comments WHERE user=" . $user["id"]) or sqlerr();
  $arr3 = mysql_fetch_row($res);
  $torrentcomments = $arr3[0];
}

// post count 
 $res = mysql_query("SELECT COUNT(*) FROM forum_posts WHERE userid=" . $user[id]) or sqlerr();
  $arr3 = mysql_fetch_row($res);
  $forumposts = $arr3[0];
// end post count

if ($user['donated'] > 0)
  $don = "<img src=pic/starbig.gif>";

$res = mysql_query("SELECT name,flagpic FROM countries WHERE id=$user[country] LIMIT 1") or sqlerr();
if (mysql_num_rows($res) == 1)
{
  $arr = mysql_fetch_assoc($res);
  $country = "$arr[name]";
}

$res = mysql_query("SELECT torrent,uploaded,downloaded FROM peers WHERE ip='$user[ip]' AND seeder='no'");
if (mysql_num_rows($res) > 0)
  $leeching = maketable($res);
$res = mysql_query("SELECT torrent,uploaded,downloaded FROM peers WHERE ip='$user[ip]' AND seeder='yes'");
if (mysql_num_rows($res) > 0)
  $seeding = maketable($res);

$avatar = $user["avatar"];
if (!$avatar) {
	$avatar = "images/default_avatar.gif";
}

$enabled = $user["enabled"] == 'yes';
$warned = $user["warned"] == 'yes';
$forumbanned = $user["forumbanned"] == 'yes';
$privacylevel = $user["privacy"];
//END PRE SQL's

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

//Table formatting starts here ***************************
stdhead("User Details for " . $user["username"]);
begin_frame("User Details for " . $user["username"] . "");
?>
<table width=100% border=0><tr><td width=50% valign=top>
	<table width=100% border=0 cellpadding=0 cellspacing=0><tr><td width=100% valign=top>

<table width=100% border=1 align=center cellpadding=2 cellspacing=1 style='border-collapse: collapse' bordercolor=#646262><TR><TD width=100% valign=middle class=table_head height=30><b>Viewing Profile: <?=$user["username"]?> </b>
<?print("[<a href=report.php?user=$user[id]>Report User</a>]");?></TD></TR>
<TR><TD><DIV style="margin-left: 8pt">
<!--  -->
<?
print("<h1>$user[username]</h1><img width=80 height=80 src=$avatar alt=$title><br><b><i>$user[title]</b></i>");
if (!$enabled)
  print("<br><b>" . ACCOUNT_DISABLED . "</b>");

?>
<BR>Joined: <?=$joindate?><br>
<br>
User Class: <?=get_user_class_name($user["class"]) ?>
<?
print("<br><a href=account-history.php?id=$user[id]&action=viewposts><b>". VIEW_POSTS ."</b></a><BR>");
print("<a href=account-torrents.php?id=$user[id]><b>View File Upload/Download Details</b></a><BR>");
print("<a href=account-history.php?id=$user[id]&action=viewcomments><b>" . VIEW_COMMENTS . "</b></a>");

print("<BR><br><a href=account-inbox.php?receiver=$user[username]>" . ACCOUNT_SEND_MSG . "</a>");

?>
<BR><BR></div>
<!--  -->
</TD></TR></TABLE>
<Br>
		<table width=100% border=1 align=center cellpadding=2 cellspacing=1 style='border-collapse: collapse' bordercolor=#646262><TR><TD width=100% valign=middle class=table_head height=30><B>Information:</B></TD></TR>
		<TR><TD>
<!--  -->
			<table width=100% border=0 cellspacing=0 cellpadding=3>
			<tr><td><?echo "" . LAST_ACCESS . "";?>: </td><td align=left><?=$lastseen?></td></tr>
			<tr><td><?echo "" . COUNTRY . "";?>: </td><td align=left><?=$country?></td></tr>
			<tr><td><?echo "" . AGE . "";?>: </td><td align=left><?=$user["age"]?></td></tr>
			<tr><td><?echo "" . GENDER . "";?>: </td><td align=left><?=$user["gender"]?></td></tr>
			<tr><td><?echo "" . CLIENT . "";?>: </td><td align=left><?=$user["client"]?></td></tr>
			<tr><td><?echo "" . COMMENTS . "";?>: </td><td align=left><?=$torrentcomments?></td></tr>
			<tr><td><?echo "" . WARNED . "";?>: </td><td align=left><?=$user["warned"]?></td></tr>
            <? if($arr_rws['warned'] == 'yes'){?><tr><td><?echo "Days until ban";?>: </td><td align=left><?=$timeleft?></td></tr><?}?>
			<tr><td><?echo "" . FORUM_POSTS . "";?>: </td><td align=left><?=$forumposts?></td></tr>
			<tr><td><?echo "" . TORRENTS_POSTED . "";?>: </td><td align=left><?=$torrenttorrents?></td></tr>
			</TABLE>
		</TD></TR></TABLE>
<!--  -->
	</TD></TR></TABLE>
	<td width=10 valign=top>&nbsp;</td>
</td><td width=50% valign=top>
	<table width=100% border=0 cellpadding=0 cellspacing=0><tr><td width=100% valign=top>
		<table width=100% border=1 align=center cellpadding=2 cellspacing=1 style='border-collapse: collapse' bordercolor=#646262><TR><TD width=100% valign=middle class=table_head height=30><B>Statistics:</B></TD></TR>
		<TR><TD>
		<table width=100% border=0 cellspacing=0 cellpadding=3>
		<?
		if ($privacylevel == "strong"){?>
			<tr><td><?echo "" . UPLOADED . "";?>: </td><td align=left>---</td></tr>
			<tr><td><?echo "" . DOWNLOADED . "";?>: </td><td align=left>---</td></tr>
		<?}else{?>
<tr><td><?echo "" . UPLOADED . "";?>: </td><td align=left><?=mksize($user["uploaded"])?></td></tr>
<tr><td><?echo "" . DOWNLOADED . "";?>: </td><td align=left><?=mksize($user["downloaded"])?></td></tr>
<tr><td><?echo "Avg Daily DL:";?></td><td align=left><?=mksize($user["downloaded"] / $user["added"])?></td></tr>
<tr><td><?echo "Avg Daily UL:";?></td><td align=left><?=mksize($user["uploaded"] / $user["added"])?></td></tr>
<?
		}

  if ($user["downloaded"] > 0)
  {
    $sr = $user["uploaded"] / $user["downloaded"];
    if ($sr >= 4)
      $s = "w00t";
    else if ($sr >= 2)
      $s = "grin";
    else if ($sr >= 1)
      $s = "smile1";
    else if ($sr >= 0.5)
      $s = "noexpression";
    else if ($sr >= 0.25)
      $s = "sad";
    else
      $s = "cry";
    $sr = "<table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded><font color=" . get_ratio_color($sr) . ">" . number_format($sr, 2) . "</font></td><td class=embedded>&nbsp;&nbsp;<img src=$SITEURL/images/smilies/$s.gif></td></tr></table>";
    print("<tr><td style='vertical-align: middle'>" . RATIO . ": </td><td align=left valign=center style='padding-top: 1px; padding-bottom: 0px'>$sr</td></tr>\n");
  }
  ?>
		</table>
		</TD></TR>
		</TABLE>

		<?
		//now do the mod only stuff
if (get_user_class() >= UC_JMODERATOR)
{?>
		<br>
		<table width=100% border=1 align=center cellpadding=2 cellspacing=1 style='border-collapse: collapse' bordercolor=#646262><TR><TD width=100% valign=middle bgcolor=green height=30><B>Moderator Only:</B></TD></TR>
		<TR><TD>
		<table width=100% border=0 cellspacing=0 cellpadding=3>
   <?
	print("<tr><td>Email: </td><td align=left>$user[email] - <a href=account-inbox.php?receiver=$user[username]>Send PM</a></td></tr>\n");
  if ($addr)
    print("<tr><td>IP Address: </td><td align=left>$user[ip]</td></tr>\n");
    print("<tr><td>Host: </td><td align=left>$dom</td></tr>\n");
	?>
	<tr><td><?echo "" . UPLOADED . "";?>: </td><td align=left><?=mksize($user["uploaded"])?></td></tr>
	<tr><td><?echo "" . DOWNLOADED . "";?>: </td><td align=left><?=mksize($user["downloaded"])?></td></tr>
	<tr><td><?echo "" . DONATED . "";?>: </td><td align=left><?=$user["donated"]?></td></tr>
	<?

	//invite code start
if (get_user_class() >= UC_JMODERATOR && $user[invites] > 0 || $user["id"] == $CURUSER["id"] && $user[invites] > 0)
{
print("<tr><td class=rowhead>Invites: </td><td align=left>$user[invites]</a></td></tr>\n");
}
if (get_user_class() >= UC_JMODERATOR && $user[invited_by] > 0 || $user["id"] == $CURUSER["id"] && $user[invited_by] > 0)
{
$invited_by = mysql_query("SELECT username FROM users WHERE id=$user[invited_by]");
$invited_by2 = mysql_fetch_array($invited_by);
print("<tr><td class=rowhead>Invited by: </td><td align=left><a href=account-details.php?id=$user[invited_by]>$invited_by2[username]</a></td></tr>\n");
}
if (get_user_class() >= UC_JMODERATOR && $user[invitees] > 0 || $user["id"] == $CURUSER["id"] && $user[invitees] > 0)
{
$compl = $user["invitees"];
$compl_list = explode(" ", $compl);
$arr = array();

foreach($compl_list as $array_list)
$arr[] = $array_list;

$compl_arr = array_reverse($arr, TRUE);
$f=0;
foreach($compl_arr as $user_id)
{

$compl_user = mysql_query("SELECT id, username FROM users WHERE id='$user_id' and status='confirmed'");
$compl_users = mysql_fetch_array($compl_user);

if ($compl_users["id"] > 0)
{
echo("<tr><td class=rowhead width=1%>Invited Users: </td><td>");

$compl = $user["invitees"];
$compl_list = explode(" ", $compl);
$arr = array();

foreach($compl_list as $array_list)
$arr[] = $array_list;

$compl_arr = array_reverse($arr, TRUE);

$i = 0;
foreach($compl_arr as $user_id)
{

$compl_user = mysql_query("SELECT id, username FROM users WHERE id='$user_id' and status='confirmed' ORDER BY username");
$compl_users = mysql_fetch_array($compl_user);
echo("<a href=account-details.php?id=" . $compl_users["id"] . ">" . $compl_users["username"] . "</a>&nbsp;");

if ($i == "9")
break;
$i++;
}
echo ("</td></tr>");
$f = 1;
}
if ($f == "1")
break;
}
}
//invite code end
// rated torrents
if (get_user_class() >= UC_JMODERATOR)
{        
if (!$_GET["ratings"])

             print("<tr><td valign=top align=left>" . RATINGS . ": </td><td><a  href=\"account-details.php?id=$id&amp;ratings=1$keepget#ratings\">[See Rated Torrents]</a></td></tr>");
          else {
			  print("<tr><td valign=top align=left>" . RATINGS . ": </td><td>&nbsp;</td></tr>");


                $s = "<tr><td valign=top align=left colspan=2><table border=0 cellspacing=0 cellpadding=2>\n";


                $subres = mysql_query("SELECT * FROM ratings WHERE user = $id ORDER BY user");

$s.="<tr><td><B>User</B></td><td align=right><B>Rated This</B></td></tr>\n";
                while ($subrow = mysql_fetch_array($subres)) {
$ratingid=$subrow["torrent"];
$sd=mysql_query("SELECT name FROM torrents WHERE id=$ratingid");
$fetched_result = mysql_fetch_array($sd);
$sd = $fetched_result['name'];
            $s .= "<tr><td><a href=torrents-details.php?id=$ratingid>". $sd ."</a></td><td align=\"right\">" . $subrow["rating"] . "</td></tr>\n";
                }
                $s .= "</table></td></tr>\n";
            print("<tr><td valign=top align=left>" .  $s . "<BR><a name=\"filelist\"></td><td><a href=\"account-details.php?id=$id$keepget\">[Hide list]</a></td></tr>");
            }
		}
//end rated torrents

?>



		</table>
		</td></tr></table>
<?}?>

	</td></tr></table>
</td></tr></table><BR>
<?
if ($torrents)
  print("<B>" . UPLOADED_TORRENTS . ":</B><BR>$torrents<BR><BR>");
if ($seeding)
  print("<B>" . CURRENTLY_SEEDING . ":</B><BR>$seeding<BR><BR>");
if ($leeching)
  print("<B>" . CURRENTLY_LEECHING . ":</B><br>$leeching<BR><BR>");
end_frame();


echo "<br /><br />";

if (get_user_class() >= UC_JMODERATOR && $CURUSER["class"] > $user["class"] || get_user_class() >= UC_ADMINISTRATOR )
{
  begin_frame("Moderator Options", center);
  print("<form method=post action=modtask.php>\n");
  print("<input type=hidden name='action' value='edituser'>\n");
  print("<input type=hidden name='userid' value='$id'>\n");
  print("<table border=0 cellspacing=0 cellpadding=3>\n");
  print("<tr><td>Title</td><td align=left><input type=text size=60 name=title value=\"$user[title]\"></tr>\n");
$avatar = htmlspecialchars($user["avatar"]);
  print("<tr><td>Signature</td><td align=left><textarea type=text cols=50 rows=10 name=signature>".htmlspecialchars($user["signature"])."</textarea></tr>\n");
$signature = htmlspecialchars($user["signature"]);
  print("<tr><td>Uploaded</td><td align=left><input type=text size=30 name=uploaded value=\"$user[uploaded]\">&nbsp;&nbsp;".mksize($user[uploaded])."</tr>\n");
$uploaded = $user["uploaded"];
  print("<tr><td>Downloaded</td><td align=left><input type=text size=30 name=downloaded value=\"$user[downloaded]\">&nbsp;&nbsp;".mksize($user[downloaded])."</tr>\n");
$downloaded = $user["downloaded"];
  print("<tr><td>Avatar URL</td><td align=left><input type=text size=60 name=avatar value=\"$avatar\"></tr>\n");
  print("<tr><td>IP Address</td><td align=left><input type=text size=20 name=ip value=\"$ip\"></tr>\n");
  print("<tr><td>Invites</td><td align=left><input type=text size=4 name=invites value=".$user["invites"]."></tr>\n");

  print("<tr><td>Class</td><td align=left><select name=class>\n");
 $maxclass = get_user_class();
  for ($i = 0; $i < $maxclass; ++$i)
  print("<option value=$i" . ($user["class"] == $i ? " selected" : "") . ">$prefix" . get_user_class_name($i) . "\n");
  if (get_user_class() == UC_ADMINISTRATOR)
	{
		print("<option value=5>Administrator</option>");
	}
  print("</select></td></tr>\n");

	$modcomment = htmlspecialchars($user["modcomment"]);
  print("<tr><td>US$&nbsp;Donated</td><td align=left><input type=text size=4 name=donated value=$user[donated]></tr>\n");
  print("<tr><td>Password</td><td align=left><input type=password size=60 name=password value=\"$user[password]\"></tr>\n");
  print("<tr><td>Change Password:</td><td align=left><input type=checkbox name=chgpasswd value='yes'/></td></tr>");
  print("<tr><td>Mod Comment</td><td align=left><textarea cols=60 rows=8 name=modcomment>$modcomment</textarea></td></tr>\n");
  print("<tr><td>Account:</td><td align=left><input name=enabled value=yes type=radio" . ($enabled ? " checked" : "") . ">Enabled <input name=enabled value=no type=radio" . (!$enabled ? " checked" : "") . ">Disabled</td></tr>\n");
  print("<tr><td>Warned: </td><td align=left><input name=warned value=yes type=radio" . ($warned ? " checked" : "") . ">Yes <input name=warned value=no type=radio" . (!$warned ? " checked" : "") . ">No</td></tr>\n");
  print("<tr><td>Forum Banned: </td><td align=left><input name=forumbanned value=yes type=radio" . ($forumbanned ? " checked" : "") . ">Yes <input name=forumbanned value=no type=radio" . (!$forumbanned ? " checked" : "") . ">No</td></tr>\n");
  print("<tr><td colspan=2><input type=submit class=btn value='Okay'></td></tr>\n");
  print("</table>\n");
  print("</form>\n");

  print("<BR><center><a href=admin.php?act=deluser&id=".$user["id"].">DELETE ACCOUNT</a><BR>(There will be <b>NO</b> further confirmation)</center>");

  end_frame();


  echo "<br /><br />";

begin_frame("IP Ban", center);
	print("<table border=0 cellspacing=0 cellpadding=3>\n");
	print("<form method=post action=admin.php?act=bans&do=add>\n");
	print("<tr><td class=rowhead>First IP</td><td><input type=text name=first size=40 value=$user[ip]></td>\n");
	print("<tr><td class=rowhead>Last IP</td><td><input type=text name=last size=40 value=$user[ip]></td>\n");
	print("<tr><td class=rowhead>Comment</td><td><input type=text name=comment size=40></td>\n");
	print("<tr><td colspan=2><input type=submit value='Okay' class=btn></td></tr>\n");
	print("</form>\n</table>\n");
	end_frame();

}
stdfoot();

?>