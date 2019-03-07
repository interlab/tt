<?php

require_once '../../backend/functions.php';

dbconn(false);
loggedinorreturn();
jmodonly();

stdhead('Multiple IPs');
require_once '../../backend/admin-functions.php';
adminmenu();


$count = DB::fetchColumn('SELECT count(ip) FROM users GROUP BY ip HAVING COUNT(ip) > 1 AND sum(downloaded) > 0');

$perpage = 100;

list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER['PHP_SELF'] .'?' );

$res = DB::fetchAll('
    SELECT sum(uploaded) as uploaded, sum(downloaded) as downloaded, ip, sum(uploaded) / sum(downloaded) as ratio,
        count(ip) as count
    FROM users
    GROUP BY ip
    HAVING COUNT(ip) > 1
        AND sum(downloaded) > 0
    ORDER BY ratio asc
    ' . $limit
);

begin_frame('Duplicate IP Checker');

if (! $res) {
	echo '<br><BR><center><b><font color=red>Nothing to show</font></b><BR></center>';
} else {
    echo $pagertop;

    echo '<center><table border=1 cellspacing=0 cellpadding=2 class=table_table>
        <tr><td class=table_head align=left>IP</td>
        <td class=table_head align=left>Combined Ratio</td>
        <td class=table_head align=left>Count</td>
        <td class=table_head align=left>Enabled</td>
        <td class=table_head align=left>Disabled</td></tr>';
    foreach ($res as $arr) {
        if ($arr['ip'] != '') {
            $host = @gethostbyaddr($arr['ip']);
            if (!(stristr($host, 'aol')) && !(stristr($host, 'cache'))&& !(stristr($host, 'proxy'))) {
                $a = DB::fetchAll("
                    SELECT count(id) AS num
                    FROM users
                    WHERE enabled = 'no'
                        AND ip = '$arr[ip]'
                    UNION
                        SELECT count(id)
                        FROM users
                        WHERE enabled = 'yes'
                            AND ip = '$arr[ip]'");
                // dump($a);
                $disabled = number_format(0 + $a[0]['num']);
                $enabled = number_format(0 + $a[1]['num']);
                $nip = ip2long($arr['ip']);
                $bans = DB::fetchColumn("SELECT COUNT(*) FROM bans WHERE $nip >= first AND $nip <= last");
                if (! $bans)
                    $ipstr = "<a href='/admin-iptest.php?ip=" . $arr['ip'] . "'><font color=darkgreen><b>Not Banned</b></font></a>";
                else
                    $ipstr = "<a href='/admin-iptest.php?ip=" . $arr['ip'] . "'><font color='#FF0000'><b>IP Banned</b></font></a>";

                if ($arr['downloaded'] > 0) {
                    $ratio = number_format($arr['uploaded'] / $arr['downloaded'], 3);
                    $ratio = '<font color=' . get_ratio_color($ratio) . '>' . $ratio . '</font>';
                } elseif ($arr['uploaded'] > 0) {
                    $ratio = 'Inf.';
                } else {
                    $ratio = '---';
                }
                print("<tr><td class=table_col1><a href=admin-search.php?ip=$arr[ip]>$arr[ip]</a> ($host) - $ipstr</td>
                    <td class=table_col2>$ratio<td class=table_col1>$arr[count]</td>
                    <td class=table_col2><font color=darkgreen><b>$enabled</b></font></td>
                    <td class=table_col1><font color=red><b>$disabled</b></font></td></tr>\n");
            }
        }
    }
    echo '</table></center><br>';
}

echo $pagerbottom;

end_frame();

stdfoot();

