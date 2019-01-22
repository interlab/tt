<?

require "backend/functions.php";

dbconn(false);

loggedinorreturn();

ob_start("ob_gzhandler");


stdhead("Completed Details");

$id = (int) $_GET[id];


$res3 = mysql_query("select count(snatched.id) from snatched left join users on snatched.userid = users.id left join torrents on snatched.torrentid = torrents.id where snatched.finished='yes'AND snatched.torrentid = $id") or die(mysql_error());
$row = mysql_fetch_array($res3);

$count = $row[0];
$perpage = 30;
list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] . "?id=$id&" );

$res3 = mysql_query("select name from torrents where id = $id");
$arr3 = mysql_fetch_assoc($res3);
$dt = gmtime() - 180;
$dt = sqlesc(get_date_time($dt));

begin_frame("<a href=torrents-details.php?id=$id><b>$arr3[name]</b></a>");

print("<p align=center>The users at the top finished the download most recently</p>");

echo $pagertop;

print("<table border=1 cellspacing=0 cellpadding=1 align=center>\n");
print("<tr><td class=table_head align=center>Username</td><td class=table_head align=center>Uploaded</td><td class=table_head align=center>Downloaded</td><td class=table_head align=center>Ratio</td><td class=table_head align=center>When Completed</td><td class=table_head align=center>Last Action</td><td class=table_head align=center>Seeding</td><td class=table_head align=center>PM User</td><td class=table_head align=center><font color=red>Report</font></td><td class=table_head align=center>On/Off</td></tr>");

$res = mysql_query("select users.id, users.username, users.title, users.uploaded, users.downloaded, snatched.completedat, snatched. last_action, snatched.seeder, snatched.userid from snatched left join users on snatched.userid = users.id left join torrents on snatched.torrentid = torrents.id where snatched.finished='yes' AND snatched.torrentid = $id ORDER BY snatched.id desc $limit");
$res2 = mysql_query("select users.last_access, snatched.uploaded, snatched.downloaded, snatched.userid from snatched left join users on snatched.userid = users.id left join torrents on snatched.torrentid = torrents.id where snatched.finished='yes' AND snatched.torrentid = $id ORDER BY snatched.id desc $limit");
while ($arr = mysql_fetch_assoc($res))
{
$arr2 = mysql_fetch_assoc($res2);
//start Global
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
$downloaded = mksize($arr["downloaded"]);
//start torrent
if ($arr2["downloaded"] > 0)
{
$ratio2 = number_format($arr2["uploaded"] / $arr2["downloaded"], 3);
$ratio2 = "<font color=" . get_ratio_color($ratio2) . ">$ratio2</font>";
}
else
if ($arr2["uploaded"] > 0)
$ratio2 = "Inf.";
else
$ratio2 = "---";
$uploaded2 =mksize($arr2["uploaded"]);
$downloaded2 = mksize($arr2["downloaded"]);
//end
$highlight = $CURUSER["id"] == $arr["id"] ? " bgcolor=#00A527" : "";
if (empty($arr['username'])) $arr['username'] = "Unknown";
print("<td align=center class=table_col1><a href=account-details.php?id=$arr[userid]><b>$arr[username]</b></a></td><td align=left class=table_col2>$uploaded Global<br>$uploaded2 Torrent</td><td align=left class=table_col1>$downloaded Global<br>$downloaded2 Torrent</td><td align=left class=table_col2>$ratio Global<br>$ratio2 Torrent</td><td align=center class=table_col1>$arr[completedat]</td><td align=center class=table_col2>$arr[last_action]</td><td align=center class=table_col1>" . ($arr["seeder"] == "yes" ? "<b><font color=green>Yes</font>" : "<font color=red>No</font></b>") . "</td>
<td align=center class=table_col2><a href=$SITEURL/account-inbox.php?receiver=$arr[userid]><img src=images/button_pm.gif></a></td><td align=center class=table_col1><a href=$SITEURL/report.php?user=$arr[userid]><img src=images/button_report.gif></a></td><td align=center class=table_col2>".("'".$arr2['last_access']."'">$dt?"<img src=images/button_online.gif border=0 alt=\"Online\">":"<img src=images/button_offline.gif border=0 alt=\"Offline\">" )."</td>"."
</tr>\n");
}
print("</table>\n");

echo $pagerbottom;
end_frame();
stdfoot();

?>