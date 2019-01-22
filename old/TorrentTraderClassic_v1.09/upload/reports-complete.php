<?
require_once("backend/functions.php");
dbconn(false);
loggedinorreturn();
jmodonly();
stdhead("Completed Reports");
begin_frame("Reported Items That Have Been Dealt With");

// Start reports block
$type = $_GET["type"];
if ($type == "user")
$where = " WHERE type = 'user'";
else if ($type == "torrent")
$where = " WHERE type = 'torrent'";
else if ($type == "forum")
$where = " WHERE type = 'forum'";
else
$where = "";

$res = mysql_query("SELECT count(id) FROM reports $where") or die(mysql_error());
//Edited by ROMAHi4
$row = mysql_fetch_array($res);

$count = $row[0];
$perpage = 25;
list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] . "?type=" . $_GET["type"] . "&" );

echo $pagertop;

print("<table border=1 cellspacing=0 cellpadding=1 align=center width=95%>\n");
print("<tr><td class=colhead align=center>By</td><td class=colhead align=center>Reported</td><td class=colhead align=center>Type</td><td class=colhead align=center>Reason</td><td class=colhead align=center>Dealt With</td><td class=colhead align=center>Mark Dealt With</td>");
if (get_user_class() >= UC_MODERATOR)
printf("<td class=colhead align=center>Delete</td>");
print("</tr>");
print("<form method=post action=takedelreport.php>");

$res = mysql_query("SELECT reports.id, reports.dealtwith,reports.dealtby, reports.addedby, reports.votedfor, reports.reason, reports.type, users.username FROM reports INNER JOIN users on reports.addedby = users.id $where ORDER BY id desc $limit");

while ($arr = mysql_fetch_assoc($res))
{
if ($arr[dealtwith])
{
$res3 = mysql_query("SELECT username FROM users WHERE id=$arr[dealtby]");
$arr3 = mysql_fetch_assoc($res3);
$dealtwith = "<font color=green><b>Yes - <a href=account-details.php?id=$arr[dealtby]><b>$arr3[username]</b></a></b></font>";

}
else
$dealtwith = "<font color=red><b><div align=center>No</div></b></font>";
if ($arr[type] == "user")
{
$type = "userdetails";
$res2 = mysql_query("SELECT username FROM users WHERE id=$arr[votedfor]");
$arr2 = mysql_fetch_assoc($res2);
$name = $arr2[username];
}
else if ($arr[type] == "forum")
{
$type = "forums";
$res2 = mysql_query("SELECT subject FROM forum_topics WHERE id=$arr[votedfor]");
$arr2 = mysql_fetch_assoc($res2);
$subject = $arr2[subject];
}
else if ($arr[type] == "torrent")
{
$type = "torrents-details";
$res2 = mysql_query("SELECT name FROM torrents WHERE id=$arr[votedfor]");
$arr2 = mysql_fetch_assoc($res2);
$name = $arr2[name];
if ($name == "")
$name = "<b>[Deleted]</b>";
}

if ($arr[type] == "forum")
{ print("<tr><td align=center><a href=account-details.php?id=$arr[addedby]><b>$arr[username]</b></a></td><td align=center><a href=$type.php?action=viewtopic&topicid=$arr[votedfor]&page=p#$arr[votedfor_xtra]><b>$subject</b></a></td><td align=center>$arr[type]</td><td align=center>$arr[reason]</td><td align=center>$dealtwith</td><td align=center><input type=\"checkbox\" name=\"delreport[]\" value=\"" . $arr[id] . "\" /></td></tr>\n");
}
else {
print("<tr><td align=center><a href=account-details.php?id=$arr[addedby]><b>$arr[username]</b></a></td><td align=center><a href=$type.php?id=$arr[votedfor]><b>$name</b></a></td><td align=center>$arr[type]</td><td align=center>$arr[reason]</td><td align=center>$dealtwith</td><td align=center><input type=\"checkbox\" name=\"delreport[]\" value=\"" . $arr[id] . "\" /></td>\n");
if (get_user_class() >= UC_MODERATOR)
printf("<td align=center><a href=delreport.php?id=$arr[id]>Delete</a></td>");
print("</tr>");
}}

print("</table>\n");

print("<p align=right><input type=submit value=Confirm></p>");
print("</form>");

echo $pagerbottom;
end_frame();

stdfoot();
?>