<?php

// If anyone wants the old stats page back, it's called extra-stats.old.php

dbconn(false);
loggedinorreturn();
loadLanguage();

function donortable($res, $frame_caption)
{
    global $txt;

    print ("<div align=center><B>$frame_caption </B><BR>");
    if ($res) {
        print("<table border=1 cellspacing=0 cellpadding=2 class=table_table>\n");
        echo "<tr>";
        echo "<td class=table_head width=40>" . $txt['ACCOUNT_RANK'] . "</td>";
        echo "<td class=table_head align=left>" . $txt['ACCOUNT_USER'] . "</td>";
        echo "<td class=table_head align=right width=100>Donated</td>";
        echo "</tr>";
        $num = 0;
        foreach ($res as $a) {
            ++$num;
            print("<tr>
            <td class=table_col1>$num</td>
            <td class=table_col2 align=left><a href=account-details.php?id=$a[id]><b>$a[username]</b></td>
            <td align=right class=table_col1>$a[donated]</td></tr>\n");
        }
        echo "</table></div>";
    } else {
        echo "<font color=red>" . $txt['NOTHING_TO_SHOW'] . "</font></div>";
    }
}


function usertable($res, $frame_caption)
{
    global $CURUSER;

    begin_frame($frame_caption, true);
    begin_table();
    ?>
    <tr>
    <td class=ttable_head width=60 align=center>Rank</td>
    <td class=ttable_head align="left">User</td>
    <td class=ttable_head align="right">Uploaded</td>
    <td class=ttable_head align="right">UL speed</td>
    <td class=ttable_head align="right">Downloaded</td>
    <td class=ttable_head align="right">DL speed</td>
    <td class=ttable_head align="right">Ratio</td>
    <td class=ttable_head align="center">Joined</td>

    </tr>
    <?php
    $num = 0;
    while ($a = $res->fetch()) {
        ++$num;
        $highlight = $CURUSER["id"] == $a["userid"] ? "" : "";
        if ($a["downloaded"]) {
            $ratio = $a["uploaded"] / $a["downloaded"];
            $color = get_ratio_color($ratio);
            $ratio = number_format($ratio, 2);
            if ($color)
                $ratio = "<font color=\"$color\">$ratio</font>";
        } else
            $ratio = "Inf.";

        print("<tr$highlight><td class=\"rowhead\" align=\"center\">$num</td>
            <td class=\"rowhead\" align=\"left\" $highlight><a href=\"account-details.php?id="
                . $a["userid"] . "\" /><b>" . $a["username"] . "</b>" . "</td>
            <td class=\"rowhead\" align=\"right\" $highlight>" . mksize($a["uploaded"]) . "</td>
            <td class=\"rowhead\" align=\"right\" $highlight>" . mksize($a["upspeed"]) . "/s" . "</td>
            <td class=\"rowhead\" align=\"right\" $highlight>" . mksize($a["downloaded"]) . "</td>
            <td class=\"rowhead\" align=\"right\" $highlight>" . mksize($a["downspeed"]) . "/s" . "</td>
            <td class=\"rowhead\" align=\"right\" $highlight>" . $ratio . "</td>
            <td class=\"rowhead\" align=\"center\">" . gmdate("Y-m-d",strtotime($a["added"])) . " (" .
            get_elapsed_time(sql_timestamp_to_unix_timestamp($a["added"])) . " ago)</td></tr>");
    }
    end_table();
    end_frame();
}

function _torrenttable($res, $frame_caption)
{
    begin_frame($frame_caption, true);
    begin_table();
    ?>
    <tr>
    <td class=ttable_head width=60 align=center>Rank</td>
    <td class=ttable_head align="left">Name</td>
    <td class=ttable_head align="right">Snatched</td>
    <td class=ttable_head align="right">Data</td>
    <td class=ttable_head align="right">Seeders</td>
    <td class=ttable_head align="right">Leechers</td>
    <td class=ttable_head align="right">Peers</td>
    <td class=ttable_head align="right">Ratio</td>
    </tr>
    <?php
    $num = 0;
    while ($a = $res->fetch()) {
        ++$num;
        if ($a["leechers"]) {
            $r = $a["seeders"] / $a["leechers"];
            $ratio = "<font color=\"" . get_ratio_color($r) . "\">" . number_format($r, 2) . "</font>";
        } else
            $ratio = "Inf.";
        print("<tr>
            <td class=\"rowhead\" align=\"center\">$num</td>
            <td class=\"rowhead\" align=\"left\">
                <a href=\"torrents-details.php?id=" . $a["id"] . "\"><b>" . $a["name"] . "</b></a>
            </td>
            <td class=\"rowhead\" align=right>" . number_format($a["times_completed"]) . "</td>
            <td class=\"rowhead\" align=\"right\">" . mksize($a["data"]) . "</td>
            <td class=\"rowhead\" align=\"right\">" . number_format($a["seeders"]) . "</td>
            <td class=\"rowhead\" align=\"right\">" . number_format($a["leechers"]) . "</td>
            <td class=\"rowhead\" align=\"right\">" . ($a["leechers"] + $a["seeders"]) . "</td>
            <td class=\"rowhead\" align=\"right\">$ratio</td>\n");
    }

    end_table();
    end_frame();
}

function countriestable($res, $frame_caption, $what)
{
    global $CURUSER;

    begin_frame($frame_caption, true);
    begin_table();
    ?>
    <tr>
    <td class=ttable_head width=60 align=center>Rank</td>
    <td class=ttable_head align="left">Country</td>
    <td class=ttable_head align="right"><?=$what?></td>
    </tr>
    <?php
    $num = 0;
    while ($a = $res->fetch()) {
        ++$num;
        if ($what == "Users")
            $value = number_format($a["num"]);
        elseif ($what == "Uploaded")
            $value = mksize($a["ul"]);
        elseif ($what == "Average")
            $value = mksize($a["ul_avg"]);
        elseif ($what == "Ratio")
            $value = number_format($a["r"],2);
        print("<tr><td class=\"rowhead\" align=\"center\">$num</td>
            <td class=\"rowhead\" align=\"left\">
            <table border=\"0\" class=\"main\" cellspacing=\"0\" cellpadding=\"0\"><tr>
                <td class=\"embedded\">" . "<img align=\"middle\" src=\"images/flag/$a[flagpic]\" alt=\"\" /></td>
                <td class=\"embedded\"><b>$a[name]</b></td>" . "</tr>
            </table>
            </td>
            <td class=\"rowhead\" align=\"right\">$value</td></tr>\n");
    }
    end_table();
    end_frame();
}

function postertable($res, $frame_caption)
{
    global $txt;

    print ("<div align=center><B>$frame_caption </B><BR>");
    if ($res) {
        print("<table border=1 cellspacing=0 cellpadding=2 class=table_table>\n");
        echo "<tr>";
        echo "<td class=table_head width=40>" . $txt['ACCOUNT_RANK'] . "</td>";
        echo "<td class=table_head align=left>" . $txt['ACCOUNT_USER'] . "</td>";
        echo "<td class=table_head align=right width=100>Torrents</td>";
        echo "</tr>";
        $num = 0;
        foreach ($res as $a) {
            ++$num;
            print("<tr>
            <td class=table_col1>$num</td>
            <td class=table_col2 align=left><a href=account-details.php?id=$a[id]><b>$a[username]</b></td>
            <td align=right class=table_col1>$a[num]</td></tr>\n");
        }
        echo "</table></div>";
    } else {
        echo "<font color=red>" . $txt['NOTHING_TO_SHOW'] . "</font></div>";
    }
}


//main stats here
$a = DB::fetchAssoc('
    SELECT id, username
    FROM users
    WHERE status = \'confirmed\'
    ORDER BY id DESC
    LIMIT 1');
if ($CURUSER)
    $latestuser = "<a href=account-details.php?id=" . $a["id"] . ">" . $a["username"] . "</a>";
else
    $latestuser = "<b>$a[username]</b>";

$registered = number_format(get_row_count("users"));
$torrents = number_format(get_row_count("torrents"));

$totaldownloaded = DB::fetchColumn('SELECT SUM(downloaded) AS totaldl FROM users');
$totaluploaded = DB::fetchColumn('SELECT SUM(uploaded) AS totalul FROM users');

$seeders = get_row_count("peers", "WHERE seeder='yes'");
$leechers = get_row_count("peers", "WHERE seeder='no'");
$usersactive = 0;
if ($leechers == 0)
    $ratio = "100";
else
    $ratio = round($seeders / $leechers * 100);
if ($ratio < 20)
    $ratio = "<font class=red>" . $ratio . "%</font>";
else
	$ratio .= "%";
$peers = number_format($seeders + $leechers);
$seeders = number_format($seeders);
$leechers = number_format($leechers);
// start count visited today
$totaltoday = DB::fetchColumn("SELECT COUNT(*) FROM users WHERE UNIX_TIMESTAMP(" . get_dt_num() . ") - UNIX_TIMESTAMP(last_access) < 86400");

// start count registered today
$regtoday = DB::fetchColumn("SELECT COUNT(*) FROM users WHERE UNIX_TIMESTAMP(" . get_dt_num() . ") - UNIX_TIMESTAMP(added) < 86400");

// start count online now
$totalnow = DB::fetchColumn("SELECT COUNT(*) FROM users WHERE UNIX_TIMESTAMP(" . get_dt_num() . ") - UNIX_TIMESTAMP(last_access) < 900");


if ($CURUSER) {
	guestadd();
}
if (! isset($activepeople)) {
    $activepeople = $txt['NO_USERS'];
}

if (! isset($todayactive)) {
    $todayactive = $txt['NO_USERS'];
}

$guests = getguests();
if (!$guests) {
	$guests = "0";
}

$time_start = getmicrotime();
//end here

stdhead("Top 10");

///////////////////////////////////////// PAGE LAYOUT //////////////////////////////

$type = (int) ($_GET["type"] ?? 0);
$limit = (int) ($_GET["lim"] ?? 0);
$subtype = (int) ($_GET["subtype"] ?? 0);

if ($type == 4 || empty($type)) {
    begin_frame($txt['STATS'], 'center');

    if (!in_array($type, array(1, 2, 3, 4))) {
        $type = 4;
    }

    print("<p align=\"center\">"  .
        ($type == 4 && !$limit ? "<b>{$GLOBALS['SITENAME']}</b>"
            : "<a href=\"extras-stats.php?type=4\">{$GLOBALS['SITENAME']}</a>") .    " | " .
        ($type == 1 && !$limit ? "<b>Users</b>" : "<a href=\"extras-stats.php?type=1\">Users</a>") .    " | " .
        ($type == 2 && !$limit ? "<b>Torrents</b>" : "<a href=\"extras-stats.php?type=2\">Torrents</a>") . " | " .
        ($type == 3 && !$limit ? "<b>Countries</b>" : "<a href=\"extras-stats.php?type=3\">Countries</a>") . "</p>\n");

    echo "<div align=left><font class=stats>" . $txt['WELCOME_NEW'] . ": " . $latestuser . "</font><br>";
    if (!$activepeople)
        echo "<br><font class=stats>" . $txt['ONLINE_USERS'] . ": (" . $totalnow . ")<b>0 Members</b></font>";
    else
        echo "<br><font class=stats>" . $txt['ONLINE_USERS'] . ": (" . $totalnow . ")</font>";
    $totalusers = $totalnow + $guests;
    echo "<br><font class=stats>" . $txt['GUESTS_ONLINE'] . ": (" . $guests . ")</font>";
    echo "<br><font class=stats>" . $txt['TOTAL_ONLINE'] . ": (" . $totalusers . ")</font>";
    if (!$todayactive)
        echo "<br><font class=stats>" . $txt['VISITORS_TODAY'] . ": (<!--<a href='visitorstoday.php'>-->"
            . $totaltoday . "<!--</a>-->)<br>0 Members</font>";
    else
        echo "<br><font class=stats>" . $txt['VISITORS_TODAY'] . ": (<!--<a href='visitorstoday.php'>-->"
            . $totaltoday . "<!--</a>-->)<!--<br>" . $todayactive . "--></font>";
    echo "<br><font class=stats>" . $txt['TOTAL_USERS'] . ": " . $registered . "</font>";
    echo "<br><font class=stats>" . $txt['NEWUSERS_TODAY'] . ": " . $regtoday . "</font>";
    echo "<br><font class=stats>" . $txt['ACTIVE_TRANSFERS'] . ": " . $peers . "</font>";
    ECHO "<br><font class=stats>" . $txt['DOWNLOADED'] . ": " . mksize($totaldownloaded) . "</FONT>";
    ECHO "<br><font class=stats>" . $txt['UPLOADED'] . ": " . mksize($totaluploaded) . "</FONT>";
    echo "<br><font class=stats>" . $txt['TRACKING'] . " " . $torrents . " Torrents</font>";
    echo "<br><font class=stats>" . $txt['SEEDS'] . ": " . $seeders . "</font>";
    echo "<br><font class=stats>" . $txt['LEECH'] . ": " . $leechers . "</font>";
    echo "<br><font class=stats>" . $txt['SEED_RATIO'] . ": " . $ratio . "</font>";
    echo "<br><br></div>";
    end_frame();
}
/////////////////////////////////////////

begin_frame("Site Statistics, Top Ten");
if ($type != 4 && !empty($type)) {
    $type = (int) ($_GET["type"] ?? 0);
    if (!in_array($type, array(1, 2, 3, 4)))
        $type = 4;

    print("<p align=\"center\">"  .
        ($type == 4 && !$limit ? "<b>{$GLOBALS['SITENAME']}</b>"
            : "<a href=\"extras-stats.php?type=4\">{$GLOBALS['SITENAME']}</a>") .    " | " .
        ($type == 1 && !$limit ? "<b>Users</b>" : "<a href=\"extras-stats.php?type=1\">Users</a>") .    " | " .
        ($type == 2 && !$limit ? "<b>Torrents</b>" : "<a href=\"extras-stats.php?type=2\">Torrents</a>") . " | " .
        ($type == 3 && !$limit ? "<b>Countries</b>" : "<a href=\"extras-stats.php?type=3\">Countries</a>") . "</p>\n");
}

// $pu = get_user_class() >= UC_POWER_USER; // UC_POWER_USER not found
$pu = true; // ???
$limit = $limit ?? 10;

if (!$pu) {
    $limit = 10;
}

if ($type == 4) {

    begin_frame("Hall Of Fame");
    $r = DB::fetchAll("
        SELECT users.id, users.username, COUNT(torrents.owner) as num
        FROM torrents LEFT JOIN users ON users.id = torrents.owner
        GROUP BY owner
        ORDER BY num DESC
        LIMIT 10");
    postertable($r, "Top 10 Posters</font>");
    echo "<br>";

    $r = DB::fetchAll("SELECT * FROM users ORDER BY donated DESC, username LIMIT 10");
    donortable($r, "Top 10 Donors");
    echo "<br>";

    $r = DB::fetchAll("
        SELECT users.id, users.username, COUNT(peers.seeder) as num
        FROM peers
            LEFT JOIN users ON users.id=peers.userid
        WHERE peers.seeder = 'yes'
        GROUP BY peers.userid
        ORDER BY num DESC
        LIMIT 10");
    postertable($r, "Top 10 Seeders (Based on the number of seeded torrents.)</font>");

    $r = DB::fetchAll("
        SELECT users.id, users.username, COUNT(peers.seeder) as num
        FROM peers
            LEFT JOIN users ON users.id = peers.userid
        WHERE peers.seeder = 'no'
        GROUP BY peers.userid
        ORDER BY num DESC
        LIMIT 10");
    echo "<br>";
    postertable($r, "Top 10 Leechers (Based on the number of leeching torrents.)</font>");
    end_frame();
    echo "<br><br>";

    begin_frame("Site Stats");
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

    $seeders = get_row_count("peers", "WHERE seeder='yes'");
    $leechers = get_row_count("peers", "WHERE seeder='no'");

    if ($leechers == 0)
        $totratio = 0;
    else
        $totratio = round($seeders / $leechers * 100);

    $peers = number_format($seeders + $leechers);
    $seeders = number_format($seeders);
    $leechers = number_format($leechers);

    $totaldownloaded = DB::fetchColumn("SELECT SUM(downloaded) AS totaldl FROM users");
    $totaluploaded = DB::fetchColumn("SELECT SUM(uploaded) AS totalul FROM users");
    $totaldonated = DB::fetchColumn("SELECT SUM(donated) AS totaldon FROM users");

    print("<table width=560><tr><td class=tabletitle align=left><b>User Info</b></td></tr></table>\n");
    ?>
    <table width=560 class=tableb border=0 cellspacing=0 cellpadding=3>
    <?php
    print("<tr><td class=tableb>Registered Users</td><td class=tableb> $registered</td></tr>\n");
    print("<tr><td class=tableb> Pending users</td><td class=tableb> $unverified</td></tr>\n");
    print("<tr><td class=tableb> Male users</td><td class=tableb> $male</td></tr>\n");
    print("<tr><td class=tableb> Female users</td><td class=tableb> $female</td></tr>\n");
    print("<tr><td class=tableb> Secret Class</td><td class=tableb> $secret</td></tr>\n");
    print("<tr><td class=tableb> Leechers Class</td><td class=tableb> $leechers123</td></tr>\n");
    print("<tr><td class=tableb> Banned Users<img src=images/disabled.gif></td><td class=tableb> $banned</td></tr>\n");
    print("<tr><td class=tableb> Warned Users<img src=images/warned.gif></td><td class=tableb> $warn</td></tr>\n");
    print("<tr><td class=tableb> Total Donations</td><td class=tableb> $$totaldonated</td></tr>\n");
    print("<tr><td class=tableb> Total upload</td><td class=tableb> ".mksize($totaluploaded)."</td></tr>\n");
    ?>
    </table>
    <br>
    <?php
    print("<table width=560><tr><td class=tabletitle align=left><b>Torrent Info</b></td></tr></table>\n"); ?>
    <table width=560 class=tableb border=0 cellspacing=0 cellpadding=3>
    <?php
    print("<tr><td class=tableb> " . $txt['TORRENTS'] . "</td><td class=tableb> $torrents</td></tr>\n");
    print("<tr><td class=tableb> Peers</td><td class=tableb> $peers</td></tr>\n");
    print("<tr><td class=tableb> Clever Users</td><td class=tableb> $smart</td></tr>\n");
    print("<tr><td class=tableb> Dumb Users</td><td class=tableb> $stupid</td></tr>\n");
    print("<tr><td class=tableb> Seeders</td><td class=tableb> $seeders</td></tr>\n");
    print("<tr><td class=tableb> Leechers</td><td class=tableb> $leechers</td></tr>\n");
    ?>
    </table>
    <br>
    <?php
    print("<table width=560><tr><td class=tabletitle align=left><b>Monthly Registration Chart</b></td></tr></table>\n");
    echo '<table width=560 cellpadding=3><tr><td><b>'.(isset($month) ? 'Day':'Month').'</b></td><td><b>Users</b></td></tr>';
    $res = DB::query('SELECT RPAD(added,'.
        (isset($month) ? '10' : '7').
        ',"") AS date, COUNT(RPAD(added,'.(isset($month) ? '10':'7')
        .',"")) AS count FROM users '.
        (isset($month) ? 'WHERE status = confirmed AND added LIKE "'.$month.'-%" ' : '').
        ' GROUP BY date ORDER BY date DESC');
    while ($users = $res->fetch()) {
        echo '<tr width=560><td class=tableb width=50% align=left>'.$users['date'].'</td><td class=tableb>'.$users['count'].'</td></tr>';
    }
    echo '</table>';
    end_frame();
}

if ($type == 1) {
    $mainquery = "
    SELECT id as userid, username, added, uploaded, downloaded,
        uploaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS upspeed,
        downloaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS downspeed
    FROM users
    WHERE enabled = 'yes'";

    if (!$limit || $limit > 250)
        $limit = 10;

    if ($limit == 10 || $subtype == "ul") {
        $order = "uploaded DESC";
        $r = DB::query($mainquery . " ORDER BY $order " . " LIMIT $limit");
        usertable($r, "Top $limit Uploaders" . ($limit == 10 && $pu ? " <font class=\"small\"> - 
            [<a href=\"extras-stats.php?type=1&lim=100&subtype=ul\">Top 100</a>] - 
            [<a href=\"extras-stats.php?type=1&lim=250&subtype=ul\">Top 250</a>]</font>" : ""));
    }

    if ($limit == 10 || $subtype == "dl") {
        $order = "downloaded DESC";
        $r = DB::query($mainquery . " ORDER BY $order " . " LIMIT $limit");
        usertable($r, "Top $limit Downloaders" . ($limit == 10 && $pu ? " <font class=\"small\"> -
            [<a href=\"extras-stats.php?type=1&lim=100&subtype=dl\">Top 100</a>] -
            [<a href=\"extras-stats.php?type=1&lim=250&subtype=dl\">Top 250</a>]</font>" : ""));
    }

    if ($limit == 10 || $subtype == "uls") {
        $order = "upspeed DESC";
        $r = DB::query($mainquery . " ORDER BY $order " . " LIMIT $limit");
        usertable($r, "Top $limit Fastest Uploaders <font class=\"small\">(average, includes inactive time)</font>"
            . ($limit == 10 && $pu ? " <font class=\"small\"> - 
            [<a href=\"extras-stats.php?type=1&lim=100&subtype=uls\">Top 100</a>] -
            [<a href=\"extras-stats.php?type=1&lim=250&subtype=uls\">Top 250</a>]</font>" : ""));
    }

    if ($limit == 10 || $subtype == "dls") {
        $order = "downspeed DESC";
        $r = DB::query($mainquery . " ORDER BY $order " . " LIMIT $limit");
        usertable($r, "Top $limit Fastest Downloaders <font class=\"small\">(average, includes inactive time)</font>"
            . ($limit == 10 && $pu ? " <font class=\"small\"> - 
            [<a href=\"extras-stats.php?type=1&lim=100&subtype=dls\">Top 100</a>] - 
            [<a href=\"extras-stats.php?type=1&lim=250&subtype=dls\">Top 250</a>]</font>" : ""));
    }

    if ($limit == 10 || $subtype == "bsh") {
        $order = "uploaded / downloaded DESC";
        $extrawhere = " AND downloaded > 1073741824";
        $r = DB::query($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit");
        usertable($r, "Top $limit Best Sharers <font class=\"small\">(with minimum 1 GB downloaded)</font>"
            .($limit == 10 && $pu ? " <font class=\"small\"> - 
            [<a href=\"extras-stats.php?type=1&lim=100&subtype=bsh\">Top 100</a>] -
            [<a href=\"extras-stats.php?type=1&lim=250&subtype=bsh\">Top 250</a>]</font>" : ""));
    }

    if ($limit == 10 || $subtype == "wsh") {
        $order = "uploaded / downloaded ASC, downloaded DESC";
        $extrawhere = " AND downloaded > 1073741824";
        $r = DB::query($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit");
        usertable($r, "Top $limit Worst Sharers <font class=\"small\">(with minimum 1 GB downloaded)</font>"
            . ($limit == 10 && $pu ? " <font class=\"small\"> - 
            [<a href=\"extras-stats.php?type=1&lim=100&subtype=wsh\">Top 100</a>] -
            [<a href=\"extras-stats.php?type=1&lim=250&subtype=wsh\">Top 250</a>]</font>" : ""));
    }
}

elseif ($type == 2) {
    if (!$limit || $limit > 50)
        $limit = 10;

    if ($limit == 10 || $subtype == "act") {
        $r = DB::query("
            SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data
            FROM torrents AS t
                LEFT JOIN peers AS p ON t.id = p.torrent
            WHERE p.seeder = 'no'
            GROUP BY t.id
            ORDER BY seeders + leechers DESC, seeders DESC, added ASC
            LIMIT $limit");
        _torrenttable($r, "Top $limit Most Active Torrents" . ($limit == 10 && $pu ? " <font class=\"small\"> - 
            [<a href=\"extras-stats.php?type=2&lim=25&subtype=act\">Top 25</a>] - 
            [<a href=\"extras-stats.php?type=2&lim=50&subtype=act\">Top 50</a>]</font>" : ""));
    }

    if ($limit == 10 || $subtype == "sna") {
        // $r = mysql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data 
        // FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' 
        // GROUP BY t.id ORDER BY times_completed DESC LIMIT $limit") or sqlerr();
        $r = DB::query("
            SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data
            FROM `torrents` AS t
                LEFT JOIN peers AS p ON t.id = p.torrent
            GROUP BY t.id
            ORDER BY t.`times_completed` DESC
            LIMIT 10");
        _torrenttable($r, "Top $limit Most Snatched Torrents" . ($limit == 10 && $pu ? " <font class=\"small\"> - 
            [<a href=\"extras-stats.php?type=2&lim=25&subtype=sna\">Top 25</a>] - 
            [<a href=\"extras-stats.php?type=2&lim=50&subtype=sna\">Top 50</a>]</font>" : ""));
    }

    if ($limit == 10 || $subtype == "bse") {
        // $r = mysql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' AND seeders >= 5 GROUP BY t.id ORDER BY seeders / leechers DESC, seeders DESC, added ASC LIMIT $limit") or sqlerr();
        $r = DB::query("
            SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data
            FROM torrents AS t
                LEFT JOIN peers AS p ON t.id = p.torrent
            WHERE t.seeders >= 5
            GROUP BY t.id
            ORDER BY t.seeders / t.leechers DESC, t.seeders DESC, t.added ASC
            LIMIT $limit");
        _torrenttable($r, "Top $limit Best Seeded Torrents <font class=\"small\">(with minimum 5 seeders)</font>"
            . ($limit == 10 && $pu ? " <font class=\"small\"> - 
            [<a href=\"extras-stats.php?type=2&lim=25&subtype=bse\">Top 25</a>] - 
            [<a href=\"extras-stats.php?type=2&lim=50&subtype=bse\">Top 50</a>]</font>" : ""));
    }

    if ($limit == 10 || $subtype == "wse") {
        $r = DB::query("
            SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data
            FROM torrents AS t
                LEFT JOIN peers AS p ON t.id = p.torrent
            WHERE p.seeder = 'no'
                AND t.leechers >= 5
                AND t.times_completed > 0
            GROUP BY t.id
            ORDER BY t.seeders / t.leechers ASC, t.leechers DESC
            LIMIT $limit");
        _torrenttable($r, "Top $limit Worst Seeded Torrents <font class=\"small\">
            (with minimum 5 leechers, excluding unsnatched torrents)</font>"
            . ($limit == 10 && $pu ? " <font class=\"small\"> - 
            [<a href=\"extras-stats.php?type=2&lim=25&subtype=wse\">Top 25</a>] - 
            [<a href=\"extras-stats.php?type=2&lim=50&subtype=wse\">Top 50</a>]</font>" : ""));
    }
} elseif ($type == 3) {
    if (!$limit || $limit > 25)
        $limit = 10;

    if ($limit == 10 || $subtype == "us") {
        $r = DB::query("
            SELECT name, flagpic, COUNT(users.country) as num
            FROM countries
                LEFT JOIN users ON users.country = countries.id
            GROUP BY name
            ORDER BY num DESC
            LIMIT $limit");
        countriestable($r, "Top $limit Countries<font class=\"small\"> (users)</font>"
            . ($limit == 10 && $pu ? " <font class=\"small\"> - 
            [<a href=\"extras-stats.php?type=3&lim=25&subtype=us\">Top 25</a>]</font>" : ""), "Users");
    }

    if ($limit == 10 || $subtype == "ul") {
        $r = DB::query("
            SELECT c.name, c.flagpic, sum(u.uploaded) AS ul
            FROM users AS u
                LEFT JOIN countries AS c ON u.country = c.id
            WHERE u.enabled = 'yes'
            GROUP BY c.name
            ORDER BY ul DESC
            LIMIT $limit");
        countriestable($r, "Top $limit Countries<font class=\"small\"> (total uploaded)</font>"
            . ($limit == 10 && $pu ? " <font class=\"small\"> - 
            [<a href=\"extras-stats.php?type=3&lim=25&subtype=ul\">Top 25</a>]</font>" : ""), "Uploaded");
    }
}

end_frame();

stdfoot();
