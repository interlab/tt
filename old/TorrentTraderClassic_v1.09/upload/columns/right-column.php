
<?php

begin_block("Site Stats");
//$a = @mysql_fetch_assoc(@mysql_query("SELECT connectable FROM peers WHERE status='confirmed' ORDER BY id DESC LIMIT 1")) or die(mysql_error());
$male = number_format(get_row_count("users", "WHERE gender='Male'"));
$female = number_format(get_row_count("users", "WHERE gender='Female'"));
$registered = number_format(get_row_count("users", "WHERE status='confirmed'"));
$peers = number_format(get_row_count("peers"));
$unverified = number_format(get_row_count("users", "WHERE status='pending'"));
$torrents = number_format(get_row_count("torrents", "WHERE visible='yes'"));
$smart = number_format(get_row_count("peers", "WHERE connectable='yes'"));
$stupid = number_format(get_row_count("peers", "WHERE connectable='no'"));
$leechers123 = number_format(get_row_count("users", "WHERE class='1'"));
$secret = number_format(get_row_count("users", "WHERE class='4'"));
$warn = number_format(get_row_count("users", "WHERE warned='yes'"));
$banned = number_format(get_row_count("users", "WHERE enabled='no'"));
$seeders = DB::fetchColumn("SELECT value_u FROM avps WHERE arg = 'seeders'");
$leechers = DB::fetchColumn("SELECT value_u FROM avps WHERE arg='leechers'");

$seeders = get_row_count("peers", "WHERE seeder='yes'");
$leechers = get_row_count("peers", "WHERE seeder='no'");

if ($leechers == 0)
$totratio = 0;
else
$totratio = round($seeders / $leechers * 100);

$peers = number_format($seeders + $leechers);
$seeders = number_format($seeders);
$leechers = number_format($leechers);

$totaldownloaded = DB::fetchColumn("SELECT SUM(downloaded) AS totaldl FROM users LIMIT 1");
$totaluploaded = DB::fetchColumn("SELECT SUM(uploaded) AS totalul FROM users LIMIT 1");
$totaldonated = DB::fetchColumn("SELECT SUM(donated) AS totaldon FROM users");

print("<table width=100%><tr><td class=tabletitle align=center><b>User Info</b></td></tr></table>\n"); ?>
<table width=100% class=tableb border=0 cellspacing=0 cellpadding=3>
<?php
print("<tr><td class=tableb>&nbsp;Active users</td><td class=tableb>&nbsp;$registered</td></tr>\n");
print("<tr><td class=tableb>&nbsp;Pending users</td><td class=tableb>&nbsp;$unverified</td></tr>\n");
print("<tr><td class=tableb>&nbsp;Male users</td><td class=tableb>&nbsp;$male</td></tr>\n");
print("<tr><td class=tableb>&nbsp;Female users</td><td class=tableb>&nbsp;$female</td></tr>\n");
//print("<tr><td class=tableb>&nbsp;Secret Class</td><td class=tableb>&nbsp;$secret</td></tr>\n");
print("<tr><td class=tableb>&nbsp;VIP users</td><td class=tableb>&nbsp;$leechers123</td></tr>\n");
print("<tr><td class=tableb>&nbsp;Banned Users</td><td class=tableb>&nbsp;$banned</td></tr>\n");
print("<tr><td class=tableb>&nbsp;Warned Users</td><td class=tableb>&nbsp;$warn</td></tr>\n");
//print("<tr><td class=tableb>&nbsp;Donations</td><td class=tableb>&nbsp;$$totaldonated</td></tr>\n");
print("<tr><td class=tableb>&nbsp;Total Upload</td><td class=tableb>&nbsp;".mksize($totaluploaded)."</td></tr>\n");
?>
</table> <br>
<?php
print("<table width=100%><tr><td class=tabletitle align=center><b>Torrent Info</b></td></tr></table>\n"); ?>
<table width=100% class=tableb border=0 cellspacing=0 cellpadding=3>
<?php
global $txt;
print("<tr><td class=tableb>&nbsp;" . $txt['TORRENTS'] . "</td><td class=tableb>&nbsp;$torrents</td></tr>\n");
print("<tr><td class=tableb>&nbsp;Peers</td><td class=tableb>&nbsp;$peers</td></tr>\n");
print("<tr><td class=tableb>&nbsp;Connected Users</td><td class=tableb>&nbsp;$smart</td></tr>\n");
print("<tr><td class=tableb>&nbsp;Dumb Users</td><td class=tableb>&nbsp;$stupid</td></tr>\n");
print("<tr><td class=tableb>&nbsp;Seeders</td><td class=tableb>&nbsp;$seeders</td></tr>\n");
print("<tr><td class=tableb>&nbsp;Leechers</td><td class=tableb>&nbsp;$leechers</td></tr>\n");; ?>
</table>
<br>
<?php
print("<table width=100%><tr><td class=tabletitle align=center><b>Registration by Month</b></td></tr></table>\n");
echo '<table width=100% cellpadding=3><tr><td><b>'.(isset($month) ? 'Day' : 'Month').'</b></td><td><b>Users</b></td></tr>';
$res = DB::query('
    SELECT RPAD(added,'.(isset($month) ? '10' : '7').',"") AS date, COUNT(RPAD(added,'. (isset($month) ? '10':'7').',"")) AS count
    FROM users '. (isset($month) ? '
    WHERE status = confirmed AND added LIKE "'.$month.'-%" ':'').'
    GROUP BY date
    ORDER BY date DESC');
while($users = $res->fetch()) {
    echo '<tr width=100%><td class=tableb>'.$users['date'].'</td><td class=tableb>'.$users['count'].'</td></tr>';
}
echo '</table>';


end_block();
?>
	</TD>
	</tr>
    </table></td>
  </tr>
</table>
