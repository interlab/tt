<?php
require_once("backend/functions.php");
dbconn();
loggedinorreturn();

stdhead("Confirm");

begin_frame();


$takeuser = (int)$_POST["user"];
$taketorrent = (int)$_POST["torrent"];
$takeforumid = (int)$_POST["forumid"];
$takeforumpost = (int)$_POST["forumpost"];
$takereason = mysql_real_escape_string($_POST["reason"]);

$user = (int)$_GET["user"];
$torrent = (int)$_GET["torrent"];
$forumid = (int)$_GET["forumid"];
$forumpost = (int)$_GET["forumpost"];

if (!empty($takeuser))
{
if (empty($takereason)){
bark("Error", "You must enter a reason.");
die;
}
$res = mysql_query("SELECT id FROM reports WHERE addedby = $CURUSER[id] AND votedfor = $takeuser AND type = 'user'") or sqlerr();
if (mysql_num_rows($res) == 0)
{
mysql_query("INSERT into reports (addedby,votedfor,type,reason) VALUES ($CURUSER[id],$takeuser,'user', '$takereason')") or sqlerr();
print("User: $takeuser, Reason: $takereason<p></p>Successfully Reported");
end_frame();
stdfoot();
die();
}
else
{
print("You have already reported user $takeuser");
end_frame();
stdfoot();
die();
}
}
if (($taketorrent !="") && ($takereason !=""))
{
if (!$takereason){
bark("Error", "You must enter a reason.");
die;
}
$res = mysql_query("SELECT id FROM reports WHERE addedby = $CURUSER[id] AND votedfor = $taketorrent AND type = 'torrent'") or sqlerr();
if (mysql_num_rows($res) == 0)
{
mysql_query("INSERT into reports (addedby,votedfor,type,reason) VALUES ($CURUSER[id],$taketorrent,'torrent', '$takereason')") or sqlerr();
print("Torrent: $taketorrent, Reason: $takereason<p></p>Successfully Reported");
end_frame();
stdfoot();
die();
}
else
{
print("You have already reported torrent $taketorrent");
end_frame();
stdfoot();
die();
}
}

if ($user !="")
{
$res = mysql_query("SELECT username, class FROM users WHERE id=$user") or sqlerr();
if (mysql_num_rows($res) == 0)
{
print("Invalid UserID");
end_frame();
stdfoot();
die();
}

$arr = mysql_fetch_assoc($res);
if ($arr["class"] >= JMODERATOR)
{
print("Can't report this user, sorry.");
end_frame();
stdfoot();
die();
}

else
{
print("<h2>Are you sure you would like to report user <a href=userdetails.php?id=$user><b>$arr[username]</b></a>?</h2><p></p>");
print("<p>Please note, this is <b>not</b> to be used to report leechers, we have scripts in place to deal with them</p>");
print("<b>Reason</b> (required): <form method=post action=report.php><input type=hidden name=user value=$user><input type=text size=100 name=reason><p></p><input type=submit class=btn value=Confirm></form>");
}
}
if ($torrent !="")
{
$res = mysql_query("SELECT name FROM torrents WHERE id=$torrent");

if (mysql_num_rows($res) == 0)
{
print("Invalid TorrentID");
end_frame();
stdfoot();
die();
}
$arr = mysql_fetch_array($res);
print("<h2>Are you sure you would like to report torrent <a href=details.php?id=$torrent><b>$arr[name]</b></a>?</h2><p></p>");
print("<b>Reason</b> (required): <form method=post action=report.php><input type=hidden name=torrent value=$torrent><input type=text size=100 name=reason><p></p><input type=submit class=btn value=Confirm></form>"); }


if (($forumid !="") && ($forumpost !=""))
{
$res = mysql_query("SELECT subject FROM forum_topics WHERE id=$forumid");

if (mysql_num_rows($res) == 0)
{
print("Invalid Forum ID");
end_frame();
stdfoot();
die();
}
$arr = mysql_fetch_array($res);
print("<h2>Are you sure you would like to report the following forum post <a href=forums.php?action=viewtopic&topicid=$forumid&page=p#$forumpost><b>$arr[subject]</b></a>?</h2><p></p>");
print("<b>Reason</b> (required): <form method=post action=report.php><input type=hidden name=forumid value=$forumid><input type=hidden name=forumpost value=$forumpost><input type=text size=100 name=reason><p></p><input type=submit class=btn value=Confirm></form>");
}


if (($takeforumid !="") && ($takereason !=""))

$res = mysql_query("SELECT id FROM reports WHERE addedby = $CURUSER[id] AND votedfor= $takeforumid AND votedfor_xtra= $takeforumpost AND type = 'forum'") or sqlerr();
if (mysql_num_rows($res) == 0)
{
if (!$takereason){
bark("Error", "You must enter a reason.");
die;
}


mysql_query("INSERT into reports (addedby,votedfor,votedfor_xtra,type,reason) VALUES ($CURUSER[id],$takeforumid,$takeforumpost ,'forum', '$takereason')") or sqlerr();
print("User: $takeuser, Reason: $takereason<p></p>Successfully Reported");
end_frame();
stdfoot();
die();
}
else
{
print("");
end_frame();
stdfoot();
die();
}

if (($user !="") && ($torrent !=""))
print("<h1>Missing Info</h1>");

end_frame();
stdfoot();
?>