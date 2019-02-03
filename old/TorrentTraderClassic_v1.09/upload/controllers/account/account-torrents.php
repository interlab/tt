<?php

require_once __DIR__ . '/../../backend/functions.php';
dbconn(false);

loggedinorreturn();

//start table function
function maketable($res)
{
    $ret = '
    <table cellpadding=2 cellspacing=0 style=:border-collapse: collapse" bordercolor=#646262 width=95% border=1>
    <tr><td class=colhead>File Name</td>
    <td class=colhead align=center>Size</td>
    <td class=colhead align=center>Uploaded</td>
    <td class=colhead align=center>Downloaded</td>
    <td class=colhead align=center>Ratio</td></tr>';

    while ($arr = $res->fetch()) {
        // todo: subquery
        $arr2 = DB::fetchAssoc('
            SELECT name, size
            FROM torrents
            WHERE id = ?
            ORDER BY name
            LIMIT 1', [$arr['torrent']]
        );
        if ($arr["downloaded"] > 0) {
            $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 2);
            $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
        } elseif ($arr["uploaded"] > 0) {
            $ratio = "Inf.";
        } else {
            $ratio = "---";
        }

        $ret .= "
        <tr>
    <td><a href=torrents-details.php?id=$arr[torrent]><b>" . h($arr2['name']) . "</b></a></td>
    <td align=center>" . mksize($arr2["size"]) . "</td>
    <td align=center>" . mksize($arr["uploaded"]) . "</td>
    <td align=center>" . mksize($arr["downloaded"]) . "</td>
    <td align=center>$ratio</td>
        </tr>\n";
    }
    $ret .= "</table>\n";

    return $ret;
}
// end table function


// start SQL Queries for data generation
$id = (int) ($_GET["id"] ?? 0);
$finished = '';
$torrents = '';

if (!is_valid_id($id)) {
    bark("Can't show details", "Bad ID.");
}

// get user details
$user = DB::fetchAssoc('SELECT * FROM users WHERE id = ' . $id);
if (! $user) {
    bark("Can't show details", "No user with ID $id.");
}

// get torrents
// todo: limit? pager?
$r = DB::fetchAll("SELECT * FROM torrents WHERE owner = $id ORDER BY name ASC");
if (!empty($r)) {
    $torrents = "<table cellpadding=2 cellspacing=0 style='border-collapse: collapse' bordercolor=#646262 width=95% border=1>\n" .
        "<tr><td class=colhead>File Name</td><td class=colhead>Seeders</td><td class=colhead>Leechers</td></tr>\n";
    foreach ($r as $a) {
        $smallname = substr(h($a["name"]) , 0, 100);
        if ($smallname != h($a["name"])){
            $smallname .= '...';
        }
        $torrents .= "<tr><td><a href=torrents-details.php?id=" . $a["id"] . "><b>" . $smallname . "</b></a></td>" .
            "<td align=center><font color=green>$a[seeders]</font></td><td align=center><font color=red>$a[leechers]</font></td></tr>\n";
    }
    $torrents .= "</table>";
}

// get leeching info
$leeching = null;
$res = DB::fetchAll("SELECT torrent, uploaded, downloaded FROM peers WHERE userid = $id AND seeder = 'no'");
if (! empty($res)) {
    $leeching = maketable($res);
}

// get seeding info
$seeding = null;
$res = DB::fetchAll("SELECT torrent, uploaded, downloaded FROM peers WHERE userid = $id AND seeder='yes'");
if (! empty($res)) {
    $seeding = maketable($res);
}

// ****** start page generation *******//

stdhead();

begin_frame("File Transfer Details For " . $user['username']);
echo '<br><br><CENTER><a href="account-details.php?id=' . $user["id"] . '">Return To Account Details</a></CENTER><BR>';


echo "<table border=0 width=80%>";

$completedls = DB::fetchColumn('SELECT COUNT(*) FROM downloaded WHERE user = ' . $id . ' LIMIT 1');

if (! $completedls) {
    echo '<tr valign=top><td><B>Downloaded Torrents: </B> <br></td><td align=left>This member has not downloaded any Torrents</td></tr>';
} else {
    $finished = '
        <table cellpadding=2 cellspacing=0 style="border-collapse: collapse" bordercolor=#646262 width=95% border=1>
            <tr><td class=colhead>Torrent Name</td><td class=colhead>Torrent Ratio</td></tr>';

    $res = DB::executeQuery('SELECT * FROM snatched WHERE userid = ? AND finished  = ? ORDER BY torrent ASC', [$id, 'yes']);
    while($tor = $res->fetch()) {
        if ($tor['downloaded'] == 0) {
            $ratio = "Inf.";
        } elseif ($tor['uploaded'] == 0) {
            $ratio = "<font color=red>0.00</font>";
        } else {
            $ratio = number_format($tor["uploaded"] / $tor["downloaded"], 2);
            $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
        }

        // todo: subquery
        $tor2 = DB::fetchAssoc('SELECT * FROM torrents WHERE id = '.$tor['torrent'].' LIMIT 1');
        $smallname = substr(h($tor2['name']) , 0, 60);
        if ($smallname != h($tor2['name'])) {
           $smallname .= '...';
        }
        $finished .= '<tr><td><b><a href="torrents-details.php?id='.$tor['torrent'].'">'.$smallname.'</a></b></td>' .
           '<td align=center>'.$ratio.'</td></tr>';
    }

    $finished .= '</table>';

    echo '
        <tr>&nbsp;</tr><tr valign=top><td><B>Downloaded Torrents:</B> </td><td align=left>' . $finished . '</td></tr>';
}

if ($torrents) {
    print("<tr valign=top><td><B>Posted Torrents:</B> </td><td align=left>$torrents</td></tr>\n");
} else {
    print("<tr valign=top><td><B>Posted Torrents:</B> </td><td align=left>No Torrents Have Been Posted By This User</td></tr>\n");
}

if (get_user_class() >= UC_JMODERATOR) {
    print("<tr valign=top><td><B>&nbsp;</B> <br></td><td align=left>&nbsp;</td></tr>\n");
    print("<tr valign=top><td><B>&nbsp;</B> <br></td><td align=left><I><font color=green>Seeding / Leeching information is only viewable by staff.</font></I></td></tr>\n");
    print("<tr valign=top><td><B>&nbsp;</B> <br></td><td align=left>&nbsp;</td></tr>\n");
    if ($seeding) {
        print("<tr valign=top><td><B>Currently Seeding:</B> <br></td><td align=left>$seeding</td></tr>\n");
    } else {
        print("<tr valign=top><td><B>Currently Seeding:</B> <br></td><td align=left>No Torrents Are Currently Being Seeded By This User</td></tr>\n");
    }

    if ($leeching) {
        print("<tr valign=top><td><B>Currently Leeching:</B> <br></td><td align=left>$leeching</td></tr>\n");
    } else {
        print("<tr valign=top><td><B>Currently Leeching:</B> <br></td><td align=left>No Torrents Are Currently Being Leeched By This User</td></tr>\n");
    }
}

echo "</table><br>";
end_frame();

stdfoot();
