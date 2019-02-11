<?php

require "backend/functions.php";
dbconn(false);
loggedinorreturn();
jmodonly();

$action = $_POST["action"];

if ($action == 'edituser')
{
  $userid = (int) $_POST["userid"];
  $title = $_POST["title"];
  $downloaded = $_POST["downloaded"];
  $uploaded = $_POST["uploaded"];
  $signature = $_POST["signature"];
  $avatar = $_POST["avatar"];
  $ip = $_POST["ip"];
  $class = $_POST["class"];
  $donated = $_POST["donated"];
  $password = $_POST["password"];
  $warned = $_POST["warned"];
  $forumbanned = $_POST["forumbanned"];
  $modcomment = $_POST["modcomment"];
  $enabled = $_POST["enabled"];
  $invites = $_POST["invites"];
  if (!is_valid_id($userid) || !is_valid_user_class($class))
    genbark("Editing Failed", "Invalid UserID");
  // check target user class
  $res = mysql_query("SELECT class FROM users WHERE id=$userid") or sqlerr();
  $arr = mysql_fetch_row($res) or genbark("Mysql error");
  $uc = $arr[0];
  // skip if class is same as current
  if ($uc != $class)
  {
    // You cant demote admins!
    if ((get_user_class() == "5") && ($userid == $CURUSER["id"]))
      genbark("Editing Failed", "You can't demote admins for security reasons.");
    // User may not demote someone with same or higher class than himself!
    elseif ($uc >= get_user_class())
      genbark("Editing Failed", "You may not demote someone with same or higher class than yourself");
    // All ok, update db
    else {
      @mysql_query("UPDATE users SET class=$class WHERE id=$userid") or sqlerr();
      // Notify user
      $prodemoted = ($class > $uc ? "promoted" : "demoted");
      $msg = sqlesc("You have been $prodemoted to '" . get_user_class_name($class) . "' by " . $CURUSER["username"] . ".");
      $added = sqlesc(get_date_time());
      @mysql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES(0, $userid, $msg, $added)") or sqlerr();
    }
  }
mysql_query("UPDATE users SET title='$title', downloaded='$downloaded', uploaded='$uploaded', signature='$signature', avatar='$avatar', ip='$ip', donated='$donated', forumbanned='$forumbanned', warned='$warned',  modcomment='$modcomment', enabled='$enabled', invites='$invites' WHERE id=$userid") or sqlerr();
  write_log($CURUSER['username']." has editied user: $userid details");
  
  $chgpasswd = $_POST['chgpasswd']=='yes' ? true : false;
  if ($chgpasswd) {
    $passreq = mysql_query("SELECT password FROM users WHERE id=$userid");
    $passres = mysql_fetch_assoc($passreq);
    // if($password != $passres['password']){
    if (! password_verify($password, $passres['password'])) {
      // $password = md5($password);
      $password = password_hash($password, PASSWORD_DEFAULT);
      DB::executeUpdate("UPDATE users SET password = ? WHERE id = $userid", [$password]);
      write_log($CURUSER['username']." has changed password for user: $userid");
    }
  }
  
  header("Location: account-details.php?id=$userid");
  die;
}

if ($action == "banuser")
{
  $userid = $_POST["userid"];
  $what = $_POST["what"];
  if (!is_valid_id($userid))
    genbark("Not a vaild Userid");
  $comment = $_POST['comment'];
  if (!$comment)
    genbark("Error:", "Please explain why you are banning this user!");
  $r = mysql_query("SELECT username,ip FROM users WHERE id=$userid") or sqlerr();
  $a = mysql_fetch_assoc($r);
  $username = $a["username"];
  $ip = $a["ip"];
  if ($what == "subnet")
  	$ip = substr($ip, 0, strrpos($ip, ".")) . ".*";
  else
    if ($what == 'ip')
      $extra = " OR ip='" . substr($ip, 0, strrpos($ip, ".")) . ".*'";
    else
      genbark("Heh", "Select what to ban!");
  $r = mysql_query("SELECT * FROM bans WHERE ip='$ip'$extra") or sqlerr();
  if (mysql_num_rows($r) > 0)
    genbark("Error", "IP/subnet is already banned");
  else {
    $dt = get_date_time();
    $comment = sqlesc($comment);
    mysql_query("INSERT INTO bans (userid, first, last, added, addedby, comment) VALUES($userid, '$ip', '$ip', '$dt', $CURUSER[id], $comment)") or sqlerr();
    mysql_query("UPDATE users SET secret='' WHERE id=$userid") or sqlerr();
    $returnto = $_POST["returnto"];
    header("Location: $returnto");
    die;
  }
}

if ($action == "enableaccount")
{
  $userid = $_POST["id"];
  $res = mysql_query("SELECT * FROM users WHERE id='$userid'") or sqlerr();
  if (mysql_num_rows($res) != 1)
    genbark("User $userid not found!");
  $secret = sqlesc(mksecret());
  mysql_query("UPDATE users SET secret=" . $secret . " WHERE id=$userid") or sqlerr();
  header("Location: account-details.php?id=$userid");
  die;
}

genbark("Error","This task is not found");

?>