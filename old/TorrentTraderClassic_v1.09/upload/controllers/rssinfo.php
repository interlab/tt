<?php

dbconn(false);
stdhead("RSS FEEDS");
begin_frame("RSS FEEDS", 'center');
$rqt = "SELECT id, name FROM categories ORDER BY sort_index, id";
$res = DB::query($rqt);
 
?>
Here you will find instructions on using our RSS Feeds. It is possible to retreive the feed in a number of ways
<br><br>
<table border=1 cellpadding=0 cellspacing=0 width=95% class=table_table>
<tr>
<td class=table_head>Link To</td><td class=table_head>Category</td>
<td class=table_head>Result</td>
</tr>
<tr>
<td class=table_col1><a href="<?= "$SITEURL/rss.php" ?>"><?= "$SITEURL/rss.php" ?></a></td>
<td class=table_col2><b><CENTER>All</CENTER></b></td>
<td class=table_col1>This will give the last 15 torrents uploaded</td>
</tr>
<?php 
while ($row = $res->fetch()) {
    echo "<tr><td class=table_col1>
    <a href=\"$SITEURL/rss.php?cat=$row[id]\">$SITEURL/rss.php?cat=$row[id]</td>
    <td class=table_col2><b><CENTER>$row[name]</CENTER></b></td>
    <td class=table_col1>Shows last 15 torrents uploaded from $row[name] category </td></tr>\n";
}
?>
</table>
<br><br><br>
<?php 
end_frame();
stdfoot();
