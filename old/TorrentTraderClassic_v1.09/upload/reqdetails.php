<?
//
// CSS And Lang Updated 4.12.05
//
require "backend/functions.php";

dbconn();

loggedinorreturn();

stdhead("Request Details");
$id = (int)$_GET["id"];
$res = mysql_query("SELECT * FROM requests WHERE id = $id") or sqlerr();
if (mysql_num_rows($res) != 1) stderr(ID_NOT_FOUND, "That request doesn't exist.");
$num = mysql_fetch_array($res);

$s = $num["request"];

begin_frame("Request: $s");

print("<center><table width=500 border=0 cellspacing=0 cellpadding=3>\n");
print("<tr><td align=left><B>" . REQUEST . ": </B></td><td width=70% align=left>$num[request]</td></tr>");
if ($num["descr"])
print("<tr><td align=left><B>" . COMMENTS . ": </B></td><td width=70% align=left>$num[descr]</td></tr>");
print("<tr><td align=left><B>" . DATE_ADDED  . ": </B></td><td width=70% align=left>$num[added]</td></tr>");

$cres = mysql_query("SELECT username FROM users WHERE id=$num[userid]");
   if (mysql_num_rows($cres) == 1)
   {
     $carr = mysql_fetch_assoc($cres);
     $username = "$carr[username]";
   }
print("<tr><td align=left><B>" . ADDED_BY . ": </B></td><td width=70% align=left>$username</td></tr>");
print("<tr><td align=left><B>" . VOTE_FOR_THIS . ": </B></td><td width=50% align=left><a href=addrequest.php?id=$id><b>" . VOTES . "</b></a></tr></tr>");

if ($num["filled"] == NULL)
{
print("<form method=get action=reqfilled.php>");
print("<tr><td align=left><B>To Fill This Request:</B> </td><td>Enter the <b>full</b> direct URL of the torrent i.e. http://www.mysite.com/torrents-details.php?id=134 (just copy/paste from another window/tab) or modify the existing URL to have the correct ID number</td></tr>");
print("</table>");
print("<input type=text size=80 name=filledurl value=TYPE-DIRECT-URL-HERE>\n");
print("<input type=hidden value=$id name=requestid>");
print("<input type=submit value=Fill Request >\n</form>");
}

print("<p><hr></p><form method=get action=requests.php#add>OR <input type=submit value=\"Add A New Request\"></form></center></table>");
end_frame();
stdfoot();
die;

?>