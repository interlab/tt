<?
require "backend/functions.php";
dbconn();
loggedinorreturn();

if ($msg) {
  $msg = trim($msg);

  $res = mysql_query("SELECT id, acceptpms, notifs, email, UNIX_TIMESTAMP(last_access) as la FROM users WHERE username=".sqlesc($receiver)."");
  $user = mysql_fetch_assoc($res);
  if (!$user)
    $message = "Username not found.";

  if ($user["acceptpms"] == "no" && get_user_class() < UC_MODERATOR)
    $message = "PM Rejected";
  
  if($message == "") {
    mysql_query("INSERT INTO messages (poster, sender, receiver, added, msg) VALUES ('" . $CURUSER["id"] . "', '" .
     $CURUSER["id"] . "', '" . $user["id"] . "', '" . get_date_time() . "', " . sqlesc($msg) . ")") or bark("", mysql_error());

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

    if (is_valid_id($origmsg) && $delete == "yes")
       mysql_query("DELETE FROM messages WHERE id=$origmsg") or sqlerr();

    bark("Message Sent", "Message was sent successfully!", Success);
  }
}

//if ($receiver) {
//  $res = mysql_query("SELECT * FROM users WHERE username='$receiver'") or die(mysql_error());
//  $user = mysql_fetch_assoc($res);
//}

if ($replyto) {
  $res = mysql_query("SELECT * FROM messages WHERE id=".intval($replyto)) or sqlerr();
  $msga = mysql_fetch_assoc($res);
  if ($msga["receiver"] != $CURUSER["id"])
    bark("Failed", "Weird things going on with your ID!");
  $res = mysql_query("SELECT username FROM users WHERE id=" . $msga["sender"]) or sqlerr();
  $usra = mysql_fetch_assoc($res);
  $body = "\n\n\n-------- ".$usra["username"]." wrote: --------\n".format_comment($msga["msg"])."\n";
}

if ($deleteid) {
  if (!is_numeric($deleteid) || $deleteid < 1 || floor($deleteid) != $deleteid)
    bark("Failed", "The ID is invalid!");
  // make sure message is owned by CURUSER
  $res = mysql_query("SELECT receiver FROM messages WHERE id=" . sqlesc($deleteid)) or die("barf");
  $arr = mysql_fetch_array($res) or die("Bad message ID");
  if ($arr["receiver"] != $CURUSER["id"])
    bark("Access Denied", "That file is not yours!");
  mysql_query("DELETE FROM messages WHERE id=" . sqlesc($deleteid)) or die('Delete Failed => database Crashed!');
  header("Refresh: 0; url=account.php?deleted=1");
  die;
}

if ($deleteall=="yes") {
 mysql_query("DELETE FROM messages WHERE receiver=" . $CURUSER["id"]) or die('Delete Failed => database Crashed!');
 header("Refresh: 0; url=account.php?alldeleted=yes");
 die;
}

stdhead("Send message", false);
begin_frame("Send a Message", center);
if ($message)
  genbark("Failed", $message);
?>

<form method=post action=account-inbox.php>
<table border=0 cellspacing=0 cellpadding=5>
  <tr>
	<td valign=top>Receiver:</td>
	<td><? if (!$receiver) { ?><input type=text name=receiver /><? } else { ?><input type=hidden name=receiver value="<?=$receiver?>"?><B><?=$receiver?></B><? } ?></td>
  </tr>
  <tr>
	<td valign=top>Message:</td>
	<td><textarea name=msg cols=60 rows=12><?=stripslashes($body)?></textarea></td>
  </tr>
<? if ($replyto) { ?>
  <tr>
	<td align=center colspan=2>
      <input type=checkbox name='delete' value='yes' checked>Delete message you are replying to
	  <input type=hidden name=origmsg value="<?=$replyto?>">
 	</td>
  </tr>
<? } ?>
  <tr>
  	<td align=center colspan=2>
  	  <input type=submit value="Send it!" class=btn>
  	</td>
  </tr>
</table>
</form>

<?
end_frame();
stdfoot();
?>