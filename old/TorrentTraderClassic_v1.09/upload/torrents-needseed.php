<?php

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

begin_frame($txt['TORRENT_NEED_SEED'], 'center');

$need_seeds = DB::fetchAll('
    SELECT torrents.*, users.username
    FROM torrents
        LEFT JOIN users ON torrents.owner = users.id
    WHERE banned = \'no\'
        AND leechers >= 5
        AND seeders <= 1
    ORDER BY seeders');

if ($need_seeds) {
    echo "<font color=\"#FF0000\">" . $txt['IF_YOU_HAVE'] . "</font>
    <br></br>
    <table class=table_table align=center cellpadding=0 cellspacing=0 style='border-collapse: collapse' bordercolor=#646262 width=100% border=1>
        <td>
        <table align=center cellpadding=0 cellspacing=0 style='border-collapse: collapse' bordercolor=#D6D9DB width=100% border=1>
            <td class=table_head align=center><font size=1 face=Verdana color=black>" . $txt['TNAME'] . "</td>
            <td class=table_head align=center><font size=1 face=Verdana color=black>" . $txt['UPLOADER'] . "</td>
            <td class=table_head align=center><font size=1 face=Verdana color=black>" . $txt['SIZE'] . "</td>
            <td class=table_head align=center><font size=1 face=Verdana color=black>" . $txt['SEEDS'] . "</td>
            <td class=table_head align=center><font size=1 face=Verdana color=black>" . $txt['LEECH'] . "</td>
            <td class=table_head align=center><font size=1 face=Verdana color=black>" . $txt['COMPLETE'] . "</td>
            <td class=table_head widht=100% align=center><font size=1 face=Verdana color=black>" . $txt['ADDED'] . "</td>";

    while ($row = $need_seeds->fetch()) {
        $torrname = h($row["name"]);
        if (strlen($torrname) > 40) {
            $torrname = substr($torrname, 0, 40) . "...";
        }

        echo "
        <tr>
        <td class=table_col2 align=left><a href=\"torrents-details.php?id=$row[id]\">$torrname</a></td>
        <td class=table_col1 align=center><a href=\"account-details.php?id=$row[owner]\">$row[username]</a></td>
        <td class=table_col2 align=right><font size=1 face=Verdana>" . mksize($row[size]) . "</td>
        <td class=table_col1 align=center><font color=green>$row[seeders]</td>
        <td class=table_col2 align=center><font color=red>$row[leechers]</td>
        <td class=table_col1 align=center><font color=black>$row[times_completed]</td>
        <td class=table_col2 widht=100% align=center><font color=purple>$row[added]</td>
        </tr>";
    }

    echo "</table></table>";
} else {
    echo "<table align=center cellpadding=0 cellspacing=0 style='border-collapse: collapse' bordercolor=#D6D9DB width=100% border=1>
    <td align=center><font color=\"#000000\"><b>" . $txt['NO_TORRENT_NEED_SEED'] . "</b></font></td>
    </td></table>
    <br></br>";
}
echo "<br></br>";
end_frame();
stdfoot();

