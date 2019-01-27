<?php
//
// CSS and Language updated 29.NOV.05
//
require_once("backend/functions.php");
dbconn(false);
loggedinorreturn();

global $CURUSER, $txt;

//GET ID
$id = $CURUSER["id"];

//DO SOME SQL CALC'S ETC
$user = DB::fetchAssoc('
    SELECT *
    FROM users
    WHERE id = '.$id);

if ($user["downloaded"] > 0) {
      $ratio = number_format($user["uploaded"] / $user["downloaded"], 2);
      //$ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
} elseif ($user["uploaded"] > 0) {
    $ratio = "Inf.";
} else {
    $ratio = "---";
}

$arr = DB::fetchAssoc('
    SELECT name, flagpic
    FROM countries
    WHERE id = '.$user["country"].'
    LIMIT 1');
if ($arr) {
    $country = "<img src=\"/images/flag/$arr[flagpic]\" alt=\"$arr[name]\" style=\"margin-left: 8pt\" />";
    $country1 = "$arr[name]";
}

$joindate = "$user[added] (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($user["added"])) . " ago)";
$lastseen = $user["last_access"];
if ($lastseen == "0000-00-00 00:00:00") {
	$lastseen = "never";
} else {
    $lastseen .= " (".get_elapsed_time(sql_timestamp_to_unix_timestamp($lastseen))." ago)";
    $torrentcomments = get_row_count('comments', 'WHERE user = '.$CURUSER['id']);
    $forumposts = get_row_count('forum_posts', 'WHERE userid = '.$CURUSER['id']);
    $torrentratings = get_row_count('ratings', 'WHERE user = '.$CURUSER['id']);
    $torrenttorrents = get_row_count('torrents', 'WHERE owner = '.$CURUSER['id']);
}

//get days until ban, if warned by ratioban system
$userid = $user['id'];
$arr_rws = DB::fetchAssoc('
    SELECT *, TO_DAYS(NOW()) - TO_DAYS(warntime) as difference
    FROM ratiowarn
    WHERE userid = ' . $userid);

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

//
/////////// BEGIN PAGE LAYOUT ///////////
//

stdhead("User CP");
begin_frame($txt['LOGGEDINAS'].' '.$CURUSER['username']);

//DO SOME ERROR CHECKS AND POST MSG'S
if (!empty($_GET['message'])) {
    bark2("Editting Failed", $_GET['message']);
} elseif (!empty($_GET['edited'])) {
    if (!empty($_GET['mailsent'])) {
        bark2('Success', $txt['PROFILE_UPDATED'] . '!<br>Confirmation email has been sent!');
    } else {
        bark2('Success', $txt['PROFILE_UPDATED'] . '!');
    }
} elseif (!empty($_GET['emailch'])) {
    bark2('Success', 'Email address changed!');
} elseif (!empty($_GET['deleted'])) {
	bark2('Success', 'The message has been deleted!');
} elseif (isset($_GET['alldeleted']) && $_GET['alldeleted'] === 'yes') {
	bark2('Success', 'All messages has been deleted!');
}
?>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%" valign="top">
<table cellspacing="2" cellpadding="5" border="0" width="100%" id="table21">
<tr>
<td width="50%" class="text">
<?php
$avatar = $CURUSER["avatar"];
if (!$avatar) {
	$avatar = "images/default_avatar.gif";
}
print("<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"2\">\n");
print("<tr>\n");
print("<td rowspan=\"2\" valign=\"top\"><a href=\"account-settings.php\"><img src=\"$avatar\" border=\"0\" width=\"80\" height=\"80\"></a><br><br></td>\n");
?>
<td width="90%" valign="top">
<?= $country ?><br><br>
<table width="100%">
<tr><td><?php
print ($txt['USERNAME'].": <b>" . $CURUSER["username"] . "</b><br>");
print ($txt['CUSTOMTITLE'].": <b>" . strip_tags($user["title"]) . "</b><br>");
print ($txt['WORD_CLASS'].": <b>" . get_user_class_name($CURUSER["class"]) . "</b><br>");
print ($txt['EMAIL_ADDRESS'].": <b>" . $CURUSER["email"] . "</b><br>");
print ($txt['IP_ADDRESS'].": <b>" . $CURUSER["ip"] . "</b><br>");
print ($txt['JOIN_DATE'].": <b>" . $CURUSER["added"] . "</b><br>");
print ($txt['LAST_ACCESS'].": <b>" . $lastseen . "</b><br>");
?>
</td></tr>
</table>
<br>
<table width="100%">
<tr>
<?php
print("<td>".$txt['AGE'].": " . $user["age"] . "</td>");
print("<td>".$txt['GENDER'].": " . $user["gender"] . "</td>");
print("<td>".$txt['COUNTRY'].": " . $country1 . "</td>");
ECHO "</td></tr>";
print("<td>".$txt['CLIENT'].": " . $user["client"] . "</td>");
print("<td>".$txt['UPLOADED'].": " . mksize($CURUSER["uploaded"]) . "</td>");
print("<td>".$txt['DOWNLOADED'].": " . mksize($CURUSER["downloaded"]) . "</td>");
?>
</tr><tr>
<?php
print("<td>".$txt['RATIO'].": " . $ratio . "</td>");
print("<td>".$txt['FORUM_POSTS'].": " . $forumposts . "</td>");
print("<td>".$txt['TORRENTS_POSTED'].": " . $torrenttorrents . "</td>");
?>
</tr><tr>
<?php
print("<td>".$txt['TORRENT_COMMENTS'].": " . $torrentcomments . "</td>");
print("<td>".$txt['RATINGS'].": " . $torrentratings . "</td>");
print("<td>".$txt['ACCOUNT_PRIVACY_LV'].": " . $user["privacy"] . "</td>");
?>
</tr><tr><?php
print("<td>".$txt['DONATED'].": " . $user["donated"] . "</td>");
print("<td>".$txt['WARNED'].": " . $user["warned"] . "</td>");
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
<br>
<FORM METHOD="LINK" ACTION="account-settings.php">
<input type="submit" value="<?= $txt['ACCOUNT_ADJUST_SETTINGS'] ?>" style="font-family: Verdana; font-size: 7pt; color: #000000; border: 1px solid #808080; background-color: #C0C0C0">
</FORM>
<?php
print("<a href=account-history.php?id=$CURUSER[id]&action=viewposts><b>".$txt['VIEW_POSTS']."</b></a><br>");
print("<a href=account-history.php?id=$CURUSER[id]&action=viewcomments><b>".$txt['VIEW_COMMENTS']."</b></a><br>");
print("<a href=account.php?action=mytorrents><b>".$txt['VIEW_MYTORRENT']."</b></a><br>");
?>
<br>
</td>
</tr>
</table></td></tr></table>
		</td>
<td></td>
	</tr>
</table>

<?php
end_frame();

/*
// del this block
//PRIVATE MESSAGES FRAME STARTS HERE
begin_frame($txt['ACCOUNT_YOUR_MESSAGES']);

$res = DB::fetchAll('
    SELECT * , UNIX_TIMESTAMP(added) as utadded
    FROM messages
    WHERE receiver=' . $CURUSER['id'] . '
    ORDER BY added DESC');
if (empty($res)) {
	echo '<br><p align=center><b>'.$txt['ACCOUNT_YOU_HAVE'].
        '<font color="#CC0000"><b> 0 </b></font>'.$txt['ACCOUNT_MESSAGES'].
        '</b></p>';
} else {
    foreach ($res as $arr) {
        if (is_valid_id($arr["sender"])) {
            // todo: subquery 
            $arr2 = DB::fetchAssoc('SELECT id AS user_id, username FROM users WHERE id = ' . $arr["sender"]);
            $sender = "<a href=account-details.php?id=" . $arr["sender"] . ">" . $arr2["username"] . "</a>";
        }
        else {
            $sender = "System";
        }

        print ("<p align='right'><a href='account-inbox.php?deleteall=yes'>Delete ALL messages</a></p>");

        print("<table border=0 width=100% cellspacing=0 cellpadding=2><tr><td bgcolor=#CCCCCC><img border=0 src=images/envelope.gif></td><td width=80% bgcolor=#ADACAC>\n");
        print($txt['FROM']." <b>$sender</b> ".$txt['AT']."\n" . get_date_time($arr["utadded"] , $CURUSER['tzoffset'] ) . " GMT\n");
        if ($arr["unread"] === "yes") {
            print("<b>(".$txt['ACCOUNT_NEW'].")</b>");
            DB::update('messages', ['unread' => 'no'], ['id' => $arr["id"]]);
        }

        print("</td><td bgcolor=#ADACAC width=20% align=right>");
        if ($arr["sender"] != "0") {
            print("<a href=account-inbox.php?receiver=". $arr2["user_id"] ."&replyto=". $arr["id"].">".$txt['ACCOUNT_REPLY']."</a> | ");
        }

        print("<a href=account-inbox.php?deleteid=" . $arr["id"] . ">".$txt['ACCOUNT_DELETE']."</a></td></tr><tr><td colspan=3>\n");
        print(format_comment($arr["msg"]));
        print("<br>\n" . "<br></td></tr></table>\n");
    }
}
print("<p align=\"center\"><a href=account-inbox.php>".$txt['ACCOUNT_SEND_MSG']."</a></p>\n");

end_frame();
// del this end
*/


//MY TORRENTS PAGE STARTS HERE
if (isset($_GET['action']) && $_GET['action'] === "mytorrents") {
    begin_frame($txt['ACCOUNT_YOUR_TORRENTS'], 'center');

$where = 'WHERE owner = ' . $CURUSER['id'];
$count = DB::fetchColumn('SELECT COUNT(*) FROM torrents ' . $where);

if (!$count) {
?>
<b><font color="#CC0000"><?= $txt['ACCOUNT_NO_UPLOADS_FOUND'] ?></font></b><br>
<?= $txt['WHEN_YOU'] ?> <a href="torrents-upload.php"><?= $txt['UPLOAD'] ?></a><?= $txt['ACCOUNT_A_TORRENT_FILE'] ?>.<br>
<?php
}
else {
	list($pagertop, $pagerbottom, $limit) = pager(40, $count, "account.php?action=mytorrents&");
	$res = DB::query("
        SELECT torrents.type, torrents.comments, torrents.leechers, torrents.nfo,
                torrents.seeders,torrents.owner,torrents.banned, IF(torrents.numratings < $minvotes,
                NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating,
                torrents.id, categories.name AS cat_name, categories.image AS cat_pic,
                torrents.name, save_as, numfiles, added, size, views, visible, hits,
                times_completed, category
        FROM torrents
            LEFT JOIN categories ON torrents.category = categories.id
        $where
        ORDER BY id DESC
        $limit");
	torrenttable($res, "mytorrents");
	print($pagerbottom);
}

end_frame();
}

stdfoot();

