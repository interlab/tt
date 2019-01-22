<?
//
// CSS Updated 29.11.05
//

require "backend/functions.php";
dbconn(false);
stdhead("RSS FEEDS");
begin_frame("RSS FEEDS", center);
$rqt = "SELECT id, name FROM categories ORDER BY sort_index, id";
$resqt = mysql_query($rqt);
 
?>
Here you will find instructions on using our RSS Feeds. It is possible to retreive the feed in a number of ways<br><br>
<table border=1 cellpadding=0 cellspacing=0 width=95% class=table_table>
<tr>
<td class=table_head>Link To</td><td class=table_head>Category</td><td class=table_head>Result</td>
</tr>
<tr>
<td class=table_col1><a href="<? echo "$SITEURL/rss.php";?>"><? echo "$SITEURL/rss.php";?></a></td><td class=table_col2><b><CENTER>All</CENTER></b></td><td class=table_col1>This will give the last 15 torrents uploaded</td>
</tr>
<?
while ($row = mysql_fetch_array($resqt))
{
 extract ($row);
   echo "<tr><td class=table_col1><a href=\"$SITEURL/rss.php?cat=$id\">$SITEURL/rss.php?cat=$id</td><td class=table_col2><b><CENTER>$name</CENTER></b></td><td class=table_col1>Shows last 15 torrents uploaded from $name category </td></tr>\n";
}
?>
</table>
<br><br><br>
<?
end_frame();
stdfoot();
?>