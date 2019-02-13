<?php

ob_start("ob_gzhandler");
require_once __DIR__ . '/../../backend/functions.php';
dbconn(false);

loggedinorreturn();

global $CURUSER, $txt;

function maketable($res)
{
    global $txt;

    $ret = "<table class=table_table border=1 cellspacing=0 cellpadding=2>" .
        "<tr><td class=table_head>" . $txt['NAME'] . "</td><td class=table_head align=center>" . $txt['SIZE'] .
        "</td><td class=table_head align=center>" . $txt['UPLOADED'] . "</td>\n" .
        "<td class=table_head align=center>" . $txt['DOWNLOADED'] . "</td><td class=table_head align=center>" .
        $txt['RATIO'] . "</td></tr>\n";
    foreach ($res as $arr) {
        // @todo: subquery
        $arr2 = DB::fetchAssoc('
            SELECT name, size
            FROM torrents
            WHERE id = ' . $arr['torrent'] . '
            ORDER BY name'
        );
        if ($arr["downloaded"] > 0) {
            $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 2);
            $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
        }
        else {
            if ($arr["uploaded"] > 0)
                $ratio = "Inf.";
            else
                $ratio = "---";
        }
        $ret .= "<tr><td class=table_col1><a href=torrents-details.php?id=$arr[torrent]><b>" .
            h($arr2['name']) . "</b></a></td><td align=center class=table_col2>" . mksize($arr2["size"]) .
            "</td><td align=center class=table_col1>" . mksize($arr["uploaded"]) .
            "</td><td align=center class=table_col2>" . mksize($arr["downloaded"]) .
            "</td><td align=center class=table_col1>$ratio</td></tr>\n";
    }
    $ret .= "</table>\n";

    return $ret;
}

$id = (int) ($_GET['id'] ?? 0);

if (!is_valid_id($id)) {
    bark("Can't show details", "Bad ID.");
}

$user = DB::fetchAssoc('SELECT * FROM users WHERE id = ' . $id . ' LIMIT 1');
if (!$user) {
    bark("Can't show details", "No user with ID $id.");
}

if ($user["status"] == "pending") {
    die('User is pending');
}

$_GET["ratings"] = $_GET["ratings"] ?? '';

$num_torrents = DB::fetchColumn('
    SELECT COUNT(*)
    FROM torrents
    WHERE owner = {int:id}
    LIMIT 1',
    ['id' => $id]
);
$torrents = '';
if ($num_torrents > 0) {
    $r = DB::executeQuery('SELECT * FROM torrents WHERE owner = {int:id} ORDER BY name ASC', ['id' => $id]);
    if ($r) {
        $torrents = "
        <table class=table_table border=1 cellspacing=0 cellpadding=2>\n" .
            "<tr>
            <td class=table_head>" . $txt['NAME'] . "</td>
            <td class=table_head>" . $txt['SEEDS'] . "</td>
            <td class=table_head>" . $txt['LEECH'] . "</td>
            </tr>\n";
        while ($a = $r->fetch()) {
            $torrents .= "<tr><td class=table_col1><a href=torrents-details.php?id=" . $a["id"] . "><b>" . h($a["name"]) . "</b></a></td>" .
            "<td align=right class=table_col2>$a[seeders]</td><td align=right class=table_col1>$a[leechers]</td></tr>\n";
        }
        $torrents .= "</table>";
    }
}

if ($user["ip"] && !(get_user_class() < UC_JMODERATOR && $user["class"] >= UC_UPLOADER)) {
    $limited = $CURUSER['id'] != $id && get_user_class() < UC_JMODERATOR;
    if ($limited) {
        $ip = substr($user["ip"], 0, strrpos($user["ip"], ".") + 1) . "xxx";
    } else {
        $ip = $user["ip"];
    }
    $dom = @gethostbyaddr($user["ip"]);
    if ($dom == $user["ip"] || @gethostbyname($dom) != $user["ip"]) {
        $addr = $ip;
    } else {
        $dom = strtoupper($dom);
        $domparts = explode(".", $dom);
        $domain = $domparts[count($domparts) - 2];
        if ($domain == "COM" || $domain == "CO" || $domain == "NET" ||
                $domain == "NE" || $domain == "ORG" || $domain == "OR" ) {
            $l = 2;
        } else {
            $l = 1;
        }
        if ($limited) {
            while (substr_count($dom, ".") > $l) {
                $dom = substr($dom, strpos($dom, ".") + 1);
            }
        }
        $addr = "$ip ($dom)";
    }
}

if ($user['added'] == "0000-00-00 00:00:00")
    $joindate = 'N/A';
else
    $joindate = "$user[added] (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($user["added"])) . " ago)";

$lastseen = $user["last_access"];
if ($lastseen == "0000-00-00 00:00:00")
    $lastseen = "never";
else {
    $lastseen .= " (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($lastseen)) . " ago)";
    $torrentcomments = DB::fetchColumn('SELECT COUNT(*) FROM comments WHERE user = ' . $user["id"]);
}

$forumposts = DB::fetchColumn('SELECT COUNT(*) FROM forum_posts WHERE userid = ' . $user['id']);

if ($user['donated'] > 0) {
    $don = "<img src=pic/starbig.gif>";
}

$country = DB::fetchColumn('SELECT name FROM countries WHERE id = ' . $user['country'] . ' LIMIT 1');

$leeching = null;
$res = DB::fetchAll("SELECT torrent, uploaded, downloaded FROM peers WHERE userid = $id AND seeder='no'");
if ($res) {
    $leeching = maketable($res);
}

$seeding = null;
$res = DB::fetchAll("SELECT torrent, uploaded, downloaded FROM peers WHERE userid = $id AND seeder='yes' group by torrent");
if ($res) {
    $seeding = maketable($res);
}

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

$arr_rws = DB::fetchAssoc('SELECT *, TO_DAYS(NOW()) - TO_DAYS(warntime) as difference FROM ratiowarn WHERE userid = ' . $userid);
if ($arr_rws) {
    if ($arr_rws['warned'] == 'yes') {
        $banned = $arr_rws['banned'];
        if ($banned == 'no') {
            $timeleft = ($arr_rws['difference'] - $RATIOWARN_BAN)/-1;
        } else {
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

<table width=100% border=1 align=center cellpadding=2 cellspacing=1 style='border-collapse: collapse' bordercolor=#646262>
<TR><TD width=100% valign=middle class=table_head height=30><b>Viewing Profile: <?=$user["username"]?> </b>
[<a href="report.php?user=<?= $user['id'] ?>">Report User</a>]</TD></TR>
<TR><TD><DIV style="margin-left: 8pt">

<h1><?= $user['username'] ?></h1>
<img width=80 height=80 src="<?= $avatar ?>" alt="">
<br><b><i><?= $user['title'] ?></b></i>

<?php
if (!$enabled) {
    print("<br><b>" . $txt['ACCOUNT_DISABLED'] . "</b>");
}
?>
<BR>Joined: <?= $joindate ?><br>
<br>
User Class: <?=get_user_class_name($user["class"]) ?>
<?php
print("<br><a href=account-history.php?id=$user[id]&action=viewposts><b>" . $txt['VIEW_POSTS'] . "</b></a><BR>");
print("<a href=account-torrents.php?id=$user[id]><b>View File Upload/Download Details</b></a><BR>");
print("<a href=account-history.php?id=$user[id]&action=viewcomments><b>" . $txt['VIEW_COMMENTS'] . "</b></a>");

print("<BR><br><a href=account-inbox.php?receiver=$user[id]>" . $txt['ACCOUNT_SEND_MSG'] . "</a>");

?>
<BR><BR></div>
<!--  -->
</TD></TR></TABLE>
<Br>
		<table width=100% border=1 align=center cellpadding=2 cellspacing=1 style='border-collapse: collapse' bordercolor=#646262>
        <TR><TD width=100% valign=middle class=table_head height=30><B>Information:</B></TD></TR>
		<TR><TD>
<!--  -->
			<table width=100% border=0 cellspacing=0 cellpadding=3>
			<tr><td><?= $txt['LAST_ACCESS'] ?>: </td><td align=left><?= $lastseen ?></td></tr>
			<tr><td><?= $txt['COUNTRY'] ?>: </td><td align=left><?=$country?></td></tr>
			<tr><td><?= $txt['AGE'] ?>: </td><td align=left><?=$user["age"]?></td></tr>
			<tr><td><?= $txt['GENDER'] ?>: </td><td align=left><?=$user["gender"]?></td></tr>
			<tr><td><?= $txt['CLIENT'] ?>: </td><td align=left><?=$user["client"]?></td></tr>
			<tr><td><?= $txt['COMMENTS'] ?>: </td><td align=left><?=$torrentcomments?></td></tr>
			<tr><td><?= $txt['WARNED'] ?>: </td><td align=left><?=$user["warned"]?></td></tr>
            <?php if ($arr_rws['warned'] == 'yes') { ?><tr><td><?= "Days until ban" ?>: </td><td align=left><?=$timeleft?></td></tr><?php } ?>
			<tr><td><?= $txt['FORUM_POSTS'] ?>: </td><td align=left><?=$forumposts?></td></tr>
			<tr><td><?= $txt['TORRENTS_POSTED'] ?>: </td><td align=left><?= $num_torrents ?></td></tr>
			</TABLE>
		</TD></TR></TABLE>
<!--  -->
	</TD></TR></TABLE>
	<td width=10 valign=top>&nbsp;</td>
</td><td width=50% valign=top>
	<table width=100% border=0 cellpadding=0 cellspacing=0><tr><td width=100% valign=top>
		<table width=100% border=1 align=center cellpadding=2 cellspacing=1 style='border-collapse: collapse' bordercolor=#646262>
        <TR><TD width=100% valign=middle class=table_head height=30><B>Statistics:</B></TD></TR>
		<TR><TD>
		<table width=100% border=0 cellspacing=0 cellpadding=3>
		<?php
        if ($CURUSER['id'] === $id || $privacylevel !== 'strong') {
            $avg_daily = round(strtotime($user["added"]) / (1 * 24 * 60 * 60)); // ежедневно
        ?>
            <tr><td><?= $txt['UPLOADED'] ?>: </td><td align=left><?= mksize($user["uploaded"]) ?></td></tr>
            <tr><td><?= $txt['DOWNLOADED'] ?>: </td><td align=left><?= mksize($user["downloaded"]) ?></td></tr>
            <tr><td>Avg Daily UL:</td><td align=left><?= mksize(round($user["uploaded"]) / $avg_daily) ?></td></tr>
            <tr><td>Avg Daily DL:</td><td align=left><?= mksize(round($user["downloaded"]) / $avg_daily) ?></td></tr>
        <?php
        } else {
        ?>
			<tr><td><?= $txt['UPLOADED'] ?>: </td><td align=left>---</td></tr>
			<tr><td><?= $txt['DOWNLOADED'] ?>: </td><td align=left>---</td></tr>
		<?php
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
    $sr = "<table border=0 cellspacing=0 cellpadding=0>
    <tr><td class=embedded><font color=" . get_ratio_color($sr) . ">" . number_format($sr, 2) .
    "</font></td><td class=embedded>&nbsp;&nbsp;<img src=$SITEURL/images/smilies/$s.gif></td></tr></table>";
    print("<tr><td style='vertical-align: middle'>" . $txt['RATIO'] . ": </td>
    <td align=left valign=center style='padding-top: 1px; padding-bottom: 0px'>
    $sr</td></tr>\n");
  }
  ?>
		</table>
		</TD></TR>
		</TABLE>

		<?php
		//now do the mod only stuff
if (get_user_class() >= UC_JMODERATOR)
{?>
		<br>
		<table width=100% border=1 align=center cellpadding=2 cellspacing=1 style='border-collapse: collapse' bordercolor=#646262>
        <TR><TD width=100% valign=middle bgcolor=green height=30><B>Moderator Only:</B></TD></TR>
		<TR><TD>
		<table width=100% border=0 cellspacing=0 cellpadding=3>
   <?php
	print("<tr><td>Email: </td><td align=left>$user[email] - <a href=account-inbox.php?receiver=$user[username]>Send PM</a></td></tr>\n");
  if ($addr)
    print("<tr><td>IP Address: </td><td align=left>$user[ip]</td></tr>\n");
    print("<tr><td>Host: </td><td align=left>$dom</td></tr>\n");
	?>
	<tr><td><?= $txt['UPLOADED'] ?>: </td><td align=left><?=mksize($user["uploaded"])?></td></tr>
	<tr><td><?= $txt['DOWNLOADED'] ?>: </td><td align=left><?=mksize($user["downloaded"])?></td></tr>
	<tr><td><?= $txt['DONATED'] ?>: </td><td align=left><?=$user["donated"]?></td></tr>
	<?php

	//invite code start
if (get_user_class() >= UC_JMODERATOR && $user['invites'] > 0 || $user["id"] == $CURUSER["id"] && $user['invites'] > 0)
{
print("<tr><td class=rowhead>Invites: </td><td align=left>$user[invites]</a></td></tr>\n");
}
if (get_user_class() >= UC_JMODERATOR && $user['invited_by'] > 0 || $user["id"] == $CURUSER["id"] && $user['invited_by'] > 0)
{
    $invited_by = DB::fetchAssoc('SELECT username FROM users WHERE id = {int:id} LIMIT 1', ['id' => (int) $user['invited_by']]);
    print("<tr><td class=rowhead>Invited by: </td>
    <td align=left><a href=account-details.php?id=$user[invited_by]>$invited_by[username]</a></td></tr>\n");
}
if (get_user_class() >= UC_JMODERATOR && $user['invitees'] > 0 || $user["id"] == $CURUSER["id"] && $user['invitees'] > 0)
{
    $compl_list = explode(' ', $user["invitees"]);
    $compl_list = array_map(function($x){return (int) $x;}, $compl_list);
    // dump($compl_list);
    // @todo: limit - see full list for invited users
    $compl_users = DB::fetchAll('
        SELECT id, username
        FROM users
        WHERE id IN(' . implode(',', $compl_list) . ')
            AND status = ?
        LIMIT 50', ['confirmed']);
    if ($compl_users) {
        $last = count($compl_users[0]);
        echo '<tr><td class=rowhead width=1%>Invited Users: </td><td>';
        $i = 0;
        foreach ($compl_users as $row) {
            $i++;
            echo '<a href="account-details.php?id=' . $row["id"] . '">' .
                    $row["username"] . '</a>' . ($i === $last ? '' : ', ');
        }
        echo '</td></tr>';
    }
}
//invite code end

// rated torrents
if (get_user_class() >= UC_JMODERATOR) {
    if (!$_GET['ratings']) {
        echo '<tr><td valign=top align=left>' . $txt['RATINGS'] . ': </td>
            <td><a href="account-details.php?id=' . $id . '&amp;ratings=1#ratings">
                [See Rated Torrents]</a>
                </td></tr>';
    } else {
        echo '<tr><td valign=top align=left>' . $txt['RATINGS'] . ': </td><td>&nbsp;</td></tr>';

        $s = '<tr><td valign=top align=left colspan=2><table border=0 cellspacing=0 cellpadding=2>';

        $res = DB::query('
            SELECT r.torrent, r.rating, t.name
            FROM ratings AS r
                INNER JOIN torrents AS t ON (t.id = r.torrent)
            WHERE user = ' . $id . '
            ORDER BY user
            LIMIT 100');

        $s .= "<tr><td><B>User</B></td><td align=right><B>Rated This</B></td></tr>\n";

        while ($row = $res->fetch()) {
            $ratingid = $row["torrent"];
            $sd = $row['name'];
            $s .= '<tr><td><a href="torrents-details.php?id=' . $row['torrent'] . '">'
                . $row['name'] . '</a></td><td align="right">' . $row['rating'] . '</td></tr>';
        }

        $s .= '</table></td></tr>';

        echo '<tr>
                <td valign=top align=left>' . $s . '<BR><a name="filelist"></td>
                <td><a href="account-details.php?id=' . $id . '">[Hide list]</a></td>
             </tr>';
    }
}
//end rated torrents

?>

		</table>
		</td></tr></table>
<?php } ?>

	</td></tr></table>
</td></tr></table><BR>
<?php
if ($torrents)
    print("<B>" . $txt['UPLOADED_TORRENTS'] . ":</B><BR>$torrents<BR><BR>");
if ($seeding)
    print("<B>" . $txt['CURRENTLY_SEEDING'] . ":</B><BR>$seeding<BR><BR>");
if ($leeching)
    print("<B>" . $txt['CURRENTLY_LEECHING'] . ":</B><br>$leeching<BR><BR>");
end_frame();

if ($user["about_myself"]) {
    begin_frame("О себе", "left");
    print(format_comment($user["about_myself"]));
    end_frame();
}

echo "<br><br>";

if (get_user_class() >= UC_JMODERATOR && $CURUSER["class"] > $user["class"] || get_user_class() >= UC_ADMINISTRATOR )
{
    begin_frame("Moderator Options", 'center');
    $avatar = h($user["avatar"]);
    $signature = h($user["signature"]);
    $uploaded = $user["uploaded"];
    $downloaded = $user["downloaded"];

?>
    <form method=post action=modtask.php>
    <input type=hidden name='action' value='edituser'>
    <input type=hidden name='userid' value='<?= $id ?>'>
    <table border=0 cellspacing=0 cellpadding=3>
    <tr><td>Title</td><td align=left><input type=text size=60 name=title value="<?= $user['title'] ?>"></tr>
    <tr><td>Signature</td><td align=left><textarea type=text cols=50 rows=10 name=signature><?= h($user["signature"]) ?></textarea></tr>
    <tr><td>About myself</td><td align=left><textarea type=text cols=50 rows=10 name=about_myself><?= h($user["about_myself"]) ?></textarea></tr>
    <tr><td>Uploaded</td>
        <td align=left><input type=text size=30 name=uploaded value="<?= $user['uploaded'] ?>">&nbsp;&nbsp;<?= mksize($user['uploaded']) ?></tr>
    <tr><td>Downloaded</td>
        <td align=left><input type=text size=30 name=downloaded value="<?= $user['downloaded'] ?>">&nbsp;&nbsp;<?= mksize($user['downloaded']) ?></tr>
    <tr><td>Avatar URL</td><td align=left><input type=text size=60 name=avatar value="<?= $avatar ?>"></tr>
    <tr><td>IP Address</td><td align=left><input type=text size=20 name=ip value="<?= $ip ?>"></tr>
    <tr><td>Invites</td><td align=left><input type=text size=4 name=invites value="<?= $user["invites"] ?>"></tr>
    <tr><td>Class</td><td align=left><select name=class>
<?php
$maxclass = get_user_class();
for ($i = 0; $i < $maxclass; ++$i) {
    echo '<option value="'.$i.'"'. ($user["class"] == $i ? ' selected' : '') . '>' . get_user_class_name($i);
}
if (get_user_class() == UC_ADMINISTRATOR) {
    echo '<option value="5">Administrator</option>';
}
print("</select></td></tr>\n");

$modcomment = h($user["modcomment"]);
print("<tr><td>US$&nbsp;Donated</td><td align=left><input type=text size=4 name=donated value=$user[donated]></tr>\n");
print("<tr><td>Password</td><td align=left><input type=password size=60 name=password value=\"$user[password]\"></tr>\n");
print("<tr><td>Change Password:</td><td align=left><input type=checkbox name=chgpasswd value='yes'/></td></tr>");
print("<tr><td>Mod Comment</td><td align=left><textarea cols=60 rows=8 name=modcomment>$modcomment</textarea></td></tr>\n");
print("<tr><td>Account:</td><td align=left><input name=enabled value=yes type=radio" . ($enabled ? " checked" : "") . 
">Enabled <input name=enabled value=no type=radio" . (!$enabled ? " checked" : "") . ">Disabled</td></tr>\n");
print("<tr><td>Warned: </td><td align=left><input name=warned value=yes type=radio" . ($warned ? " checked" : "") . 
">Yes <input name=warned value=no type=radio" . (!$warned ? " checked" : "") . ">No</td></tr>\n");
print("<tr><td>Forum Banned: </td><td align=left><input name=forumbanned value=yes type=radio" . ($forumbanned ? " checked" : "") 
. ">Yes <input name=forumbanned value=no type=radio" . (!$forumbanned ? " checked" : "") . ">No</td></tr>\n");
print("<tr><td colspan=2><input type=submit class=btn value='Okay'></td></tr>
</table>
</form>

<BR><center><a href=admin.php?act=deluser&id=".$user["id"].">DELETE ACCOUNT</a>
<BR>(There will be <b>NO</b> further confirmation)</center>");

end_frame();

echo "<br><br>";

begin_frame("IP Ban", 'center');
?>
	<table border=0 cellspacing=0 cellpadding=3>
	<form method=post action="admin.php?act=bans&do=add">
	<tr><td class=rowhead>First IP</td><td><input type=text name=first size=40 value="<?= $user['ip'] ?>"></td>
	<tr><td class=rowhead>Last IP</td><td><input type=text name=last size=40 value="<?= $user['ip'] ?>"></td>
	<tr><td class=rowhead>Comment</td><td><input type=text name=comment size="40"></td>
	<tr><td colspan=2><input type=submit value="Okay" class="btn"></td></tr>
	</form></table>
<?php
    end_frame();
}

stdfoot();

