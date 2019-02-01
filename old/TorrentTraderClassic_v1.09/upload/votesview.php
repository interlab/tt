<?
//
// - Theme And Language Updated 25.Nov.05
//
require "backend/functions.php";
dbconn(false);
loggedinorreturn();
ob_start("ob_gzhandler");

$requestid = (int)$_GET[requestid];

$res2 = mysql_query("select count(addedrequests.id) from addedrequests inner join users on addedrequests.userid = users.id inner join requests on addedrequests.requestid = requests.id WHERE addedrequests.requestid =$requestid") or die(mysql_error());
$row = mysql_fetch_array($res2);
$count = $row[0];


$perpage = 50;

 list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] ."?" );

$res = mysql_query("select users.id as userid,users.username, users.downloaded,users.uploaded, requests.id as requestid, requests.request from addedrequests inner join users on addedrequests.userid = users.id inner join requests on addedrequests.requestid = requests.id WHERE addedrequests.requestid =$requestid $limit") or sqlerr();

stdhead("Votes");

$res2 = mysql_query("select request from requests where id=$requestid");
$arr2 = mysql_fetch_assoc($res2);

begin_frame("" . VOTES . ": <a href=requests.php?details=$requestid>$arr2[request]</a>");
print("<p>" . VOTE_FOR_THIS . "<a href=addrequest.php?id=$requestid><b>" . REQUEST . "</b></a></p>");

echo $pagertop;

if (mysql_num_rows($res) == 0)
 print("<p align=center><b>" . NOTHING_FOUND . "</b></p>\n");
else
{
 print("<center><table cellspacing=0 cellpadding=3 class=table_table>\n");
 print("<tr><td class=table_head>" . USERNAME . "</td><td class=table_head align=left>" . UPLOADED . "</td><td class=table_head align=left>" . DOWNLOADED . "</td>".
   "<td class=table_head align=left>" . RATIO . "</td>\n");

 while ($arr = mysql_fetch_assoc($res))
 {

if ($arr["downloaded"] > 0)
{
       $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
       $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
    }
    else
       if ($arr["uploaded"] > 0)
         $ratio = "Inf.";
 else
  $ratio = "---";
$uploaded =mksize($arr["uploaded"]);
$joindate = "$arr[added] (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"])) . " ago)";
$downloaded = mksize($arr["downloaded"]);
if ($arr["enabled"] == 'no')
 $enabled = "<font color = red>No</font>";
else
 $enabled = "<font color = green>Yes</font>";

 print("<tr><td class=table_col1><a href=account-details.php?id=$arr[userid]><b>$arr[username]</b></a></td><td align=left class=table_col2>$uploaded</td><td align=left class=table_col1>$downloaded</td><td align=left class=table_col2>$ratio</td></tr>\n");
 }
 print("</table></center><BR><BR>\n");
}

end_frame();

stdfoot();

?>