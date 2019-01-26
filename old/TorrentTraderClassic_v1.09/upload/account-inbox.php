<?php

require "backend/functions.php";
dbconn();
loggedinorreturn();

loadLanguage();

$msg = $_REQUEST['msg'] ?? '';
$replyto = $_REQUEST['replyto'] ?? '';
$deleteid = $_REQUEST['deleteid'] ?? '';
$deleteall = $_REQUEST['deleteall'] ?? '';
$body = $_REQUEST['body'] ?? '';
$receiver = $_REQUEST['receiver'] ?? '';

if (!empty($msg) && !empty($receiver)) {
    $msg = trim($msg);

    $user = DB::fetchAssoc('SELECT id, acceptpms, notifs, email, UNIX_TIMESTAMP(last_access) as la FROM users WHERE username = ?', [$receiver]);
    if (empty($user)) {
        $message = "Username not found.";
    }

    if ($user["acceptpms"] === "no" && get_user_class() < UC_MODERATOR)
        $message = "PM Rejected";
  
    if (empty($message)) {
        DB::executeQuery('INSERT INTO messages (poster, sender, receiver, added, msg) VALUES (?, ?, ?, ?, ?)',
            [$CURUSER["id"], $CURUSER["id"], $user["id"], get_date_time(), $msg]);

        if (strpos($user['notifs'], '[pm]') !== false) {
            if (gmtime() - $user["la"] >= 300) {
                $username = $CURUSER["username"];
                $body = <<<EOD
You have received a PM from $username!

You can use the URL below to view the message (you may have to login).

$SITEURL/account.php

--
$SITENAME
EOD;
                ini_set("sendmail_from", ""); // Null envelope (Return-Path: <>)
                mail($user["email"], "You have received a PM from " . $username . "!", $body, "From: $SITENAME <$SITEEMAIL>");
            }
        }

        if (isset($_REQUEST['origmsg'], $_REQUEST['delete'])
            && is_valid_id($_REQUEST['origmsg'])
            && $_REQUEST['delete'] === 'yes'
        ) {
            $_REQUEST['origmsg'] = (int) $_REQUEST['origmsg'];
            DB::query('DELETE FROM messages WHERE id = ' . $_REQUEST['origmsg']);
        }

        bark("Message Sent", "Message was sent successfully!", $txt['SUCCESS']);
    }
}

//if ($receiver) {
//  $res = mysql_query("SELECT * FROM users WHERE username='$receiver'") or die(mysql_error());
//  $user = mysql_fetch_assoc($res);
//}

if (!empty($replyto)) {
    $msga = DB::fetchAssoc('SELECT * FROM messages WHERE id = '.intval($replyto));
    if ($msga["receiver"] != $CURUSER["id"])
        bark("Failed", "Weird things going on with your ID!");
    $usra = DB::fetchAssoc("SELECT username FROM users WHERE id = " . $msga["sender"]);
    $body = "\n\n\n-------- ".$usra["username"]." wrote: --------\n" . $msga["msg"]."\n";
}

// todo: вроде бы не используется нигде
if (!empty($deleteid)) {
    if (!is_numeric($deleteid) || $deleteid < 1 || floor($deleteid) != $deleteid)
        bark("Failed", "The ID is invalid!");
    // make sure message is owned by CURUSER
    $arr = DB::fecthAssoc("SELECT receiver FROM messages WHERE id = " . $deleteid);
    if (empty($arr)) {
        die("Bad message ID");
    }
    if ($arr["receiver"] != $CURUSER["id"])
        bark("Access Denied", "That file is not yours!");
    DB::query("DELETE FROM messages WHERE id = " . ($deleteid));
    header("Refresh: 0; url=account.php?deleted=1");
    die;
}

// todo: вроде бы не используется нигде
if ($deleteall === "yes") {
    DB::query("DELETE FROM messages WHERE receiver=" . $CURUSER["id"]);
    header("Refresh: 0; url=account.php?alldeleted=yes");
    die;
}

stdhead("Send message", false);
begin_frame("Send a Message", 'center');
if (!empty($message)) {
    genbark("Failed", $message);
}

?>

<form method=post action=account-inbox.php>
<table border=0 cellspacing=0 cellpadding=5>
  <tr>
	<td valign=top>Receiver:</td>
	<td><?php if (!$receiver) { ?><input type=text name=receiver /><?php } else {
        ?><input type=hidden name=receiver value="<?=$receiver?>"?><B><?=$receiver?></B><?php } ?></td>
  </tr>
  <tr>
	<td valign=top>Message:</td>
	<td><textarea name=msg cols=60 rows=12><?= stripslashes($body) ?></textarea></td>
  </tr>
<?php if ($replyto) { ?>
  <tr>
	<td align=center colspan=2>
      <input type=checkbox name='delete' value='yes' checked>Delete message you are replying to
	  <input type=hidden name=origmsg value="<?=$replyto?>">
 	</td>
  </tr>
<?php } ?>
  <tr>
  	<td align=center colspan=2>
  	  <input type=submit value="Send it!" class=btn>
  	</td>
  </tr>
</table>
</form>

<?php
end_frame();
stdfoot();
