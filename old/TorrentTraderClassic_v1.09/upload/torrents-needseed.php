<?

ob_start("ob_gzhandler");
require "backend/functions.php";
dbconn();
loggedinorreturn();


stdhead("Torrents Needing Seeds");
/* * ************************************************** * */
/* *            Torrents Needing Seeds Mod              * */
/* *             By TorrentialStorm                     * */
/* *                PHP & SQL Code By                   * */
/* *              TorrentialStorm                       * */
/* *             HTML Layout By Nightmare               * */
/* * Modified & ported to TTClassic Beta 4 by Nightmare * */
/* * Last Edited: TorrentialStorm 24/5/07@00:35         * */
/* * ************************************************** * */
begin_frame("". TORRENT_NEED_SEED ."", 'center');
$need_seeds = mysql_query("SELECT torrents.*, users.username FROM torrents LEFT JOIN users ON torrents.owner=users.id WHERE banned = 'no' AND leechers >= 5 AND seeders <= 1 ORDER BY seeders");

if (mysql_num_rows($need_seeds) > 0) {
echo("<font color=\"#FF0000\">". IF_YOU_HAVE ."</font>");
echo("<br></br>");
echo("<table class=table_table align=center cellpadding=0 cellspacing=0 style='border-collapse: collapse' bordercolor=#646262 width=100% border=1>
<td>
<table align=center cellpadding=0 cellspacing=0 style='border-collapse: collapse' bordercolor=#D6D9DB width=100% border=1>
<td class=table_head align=center><font size=1 face=Verdana color=black>" .TNAME. "</td>
<td class=table_head align=center><font size=1 face=Verdana color=black>" .UPLOADER. "</td>");
echo("<td class=table_head align=center><font size=1 face=Verdana color=black>" .SIZE."</td>
<td class=table_head align=center><font size=1 face=Verdana color=black>" .SEEDS. "</td>
<td class=table_head align=center><font size=1 face=Verdana color=black>" .LEECH. "</td>
<td class=table_head align=center><font size=1 face=Verdana color=black>" . COMPLETE . "</td>
<td class=table_head widht=100% align=center><font size=1 face=Verdana color=black>" .ADDED. "</td>");
while ($row = mysql_fetch_array($need_seeds)) {

$torrname = htmlspecialchars($row["name"]);
   if (strlen($torrname) > 40)
   $torrname = substr($torrname, 0, 40) . "...";
echo "<tr>";
echo "<td class=table_col2 align=left><a href=\"torrents-details.php?id=$row[id]\">$torrname</a></td>";
echo "<td class=table_col1 align=center><a href=\"account-details.php?id=$row[owner]\">$row[username]</a></td>";
echo "<td class=table_col2 align=right><font size=1 face=Verdana>" . mksize($row[size]) . "</td>\n";
echo "<td class=table_col1 align=center><font color=green>$row[seeders]</td>";
echo "<td class=table_col2 align=center><font color=red>$row[leechers]</td>";
echo "<td class=table_col1 align=center><font color=black>$row[times_completed]</td>";
echo "<td class=table_col2 widht=100% align=center><font color=purple>$row[added]</td>";
echo "</tr>";
}
echo "</table></table>";
} else {
echo "<table align=center cellpadding=0 cellspacing=0 style='border-collapse: collapse' bordercolor=#D6D9DB width=100% border=1 >";
echo "<td align=center><font color=\"#000000\"><b>". NO_TORRENT_NEED_SEED ."</b></font></td>";
echo "</td></table>";
echo "<br></br>";
}
echo "<br></br>";
end_frame();
stdfoot();

?>
