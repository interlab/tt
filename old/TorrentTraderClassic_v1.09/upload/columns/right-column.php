

<?php begin_block("Погода"); ?>

<div style="text-align:center;">
<a href="https://clck.yandex.ru/redir/dtype=stred/pid=7/cid=1228/*https://yandex.ru/pogoda/10317" target="_blank">
<img src="https://info.weather.yandex.net/10317/4.ru.png?domain=ru" border="0" alt="Яндекс.Погода"/>
<img style="display: none;" width="1" height="1" src="https://clck.yandex.ru/click/dtype=stred/pid=7/cid=1227/*https://img.yandex.ru/i/pix.gif" alt="" border="0"/></a>
</div>
<?php
end_block();

begin_block("Site Stats");

$arr = Cache::rise('right-column-site-stats', function() {
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
    // $seeders = DB::fetchColumn("SELECT value_u FROM avps WHERE arg = 'seeders'");
    // $leechers = DB::fetchColumn("SELECT value_u FROM avps WHERE arg='leechers'");

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

    return [
        'male' => $male,
        'female' => $female,
        'registered' => $registered,
        'peers' => $peers,
        'unverified' => $unverified,
        'torrents' => $torrents,
        'smart' => $smart,
        'stupid' => $stupid,
        'leechers123' => $leechers123,
        'secret' => $secret,
        'warn' => $warn,
        'banned' => $banned,
        'peers' => $peers,
        'seeders' => $seeders,
        'leechers' => $leechers,
        'totaldownloaded' => $totaldownloaded,
        'totaluploaded' => $totaluploaded,
        'totaldonated' => $totaldonated,
    ];
});

// dump($arr);

?>

<table width=100%><tr><td class=tabletitle align=center><b>User Info</b></td></tr></table>
<table width=100% class=tableb border=0 cellspacing=0 cellpadding=3>
<tr><td class=tableb>&nbsp;Active users</td><td class=tableb>&nbsp;<?= $arr['registered'] ?></td></tr>
<tr><td class=tableb>&nbsp;Pending users</td><td class=tableb>&nbsp;<?= $arr['unverified'] ?></td></tr>
<tr><td class=tableb>&nbsp;Male users</td><td class=tableb>&nbsp;<?= $arr['male'] ?></td></tr>
<tr><td class=tableb>&nbsp;Female users</td><td class=tableb>&nbsp;<?= $arr['female'] ?></td></tr>
<tr><td class=tableb>&nbsp;VIP users</td><td class=tableb>&nbsp;<?= $arr['leechers123'] ?></td></tr>
<tr><td class=tableb>&nbsp;Banned Users</td><td class=tableb>&nbsp;<?= $arr['banned'] ?></td></tr>
<tr><td class=tableb>&nbsp;Warned Users</td><td class=tableb>&nbsp;<?= $arr['warn'] ?></td></tr>
<tr><td class=tableb>&nbsp;Total Upload</td><td class=tableb>&nbsp;<?= mksize($arr['totaluploaded']) ?></td></tr>
</table>
<br>

<table width=100%><tr><td class=tabletitle align=center><b>Torrent Info</b></td></tr></table>
<table width=100% class=tableb border=0 cellspacing=0 cellpadding=3>
<?php
global $txt;
?>
<tr><td class=tableb>&nbsp;<?= $txt['TORRENTS'] ?></td><td class=tableb>&nbsp;<?= $arr['torrents'] ?></td></tr>
<tr><td class=tableb>&nbsp;Peers</td><td class=tableb>&nbsp;<?= $arr['peers'] ?></td></tr>
<tr><td class=tableb>&nbsp;Connected Users</td><td class=tableb>&nbsp;<?= $arr['smart'] ?></td></tr>
<tr><td class=tableb>&nbsp;Dumb Users</td><td class=tableb>&nbsp;<?= $arr['stupid'] ?></td></tr>
<tr><td class=tableb>&nbsp;Seeders</td><td class=tableb>&nbsp;<?= $arr['seeders'] ?></td></tr>
<tr><td class=tableb>&nbsp;Leechers</td><td class=tableb>&nbsp;<?= $arr['leechers'] ?></td></tr>
</table>
<br>
<table width=100%><tr><td class=tabletitle align=center><b>Registration by Month</b></td></tr></table>

<?php
echo '<table width=100% cellpadding=3><tr><td><b>'.(isset($month) ? 'Day' : 'Month').'</b></td><td><b>Users</b></td></tr>';
$res = DB::query('
    SELECT RPAD(added,'.(isset($month) ? '10' : '7').',"") AS date, COUNT(RPAD(added,'. (isset($month) ? '10':'7').',"")) AS count
    FROM users '. (isset($month) ? '
    WHERE status = confirmed AND added LIKE "'.$month.'-%" ':'').'
    GROUP BY date
    ORDER BY date DESC');
while ($users = $res->fetch()) {
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
