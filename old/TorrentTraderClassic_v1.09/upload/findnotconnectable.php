<?php

require "backend/functions.php";

dbconn(false);

loggedinorreturn();
adminonly();

$_GET['action'] = $_GET['action'] ?? '';

if ($_GET['action'] == "") {
    $res2 = DB::fetchAll("SELECT distinct ip FROM peers WHERE connectable = 'no' ORDER BY ip DESC");

    stdhead("Peers that are unconnectable");
    require_once("backend/admin-functions.php");
    adminmenu();
    begin_frame("Unconnectable");
    print("<center><a href=findnotconnectable.php?action=sendpm><b>Send All not connectable Users A PM</b></a>");
    print("<BR><b>Peers that are Not Connectable</b><BR><BR>");
    print("This is only users that are active on the torrents right now.");

    print("<br><p>");
    // todo: check query on real data
    $count = DB::fetchColumn("select COUNT(DISTINCT ip) from peers where connectable = 'no'");
    print ("$count unique users that are not connectable.");

    if (! $res2) {
        print("<p align=center><b>All Peers Are Connectable!</b></p>\n");
    } else {
        print("<table border=1 cellspacing=0 cellpadding=5>\n");
        print("<tr><td class=colhead>UserName</td></tr>\n");

        foreach ($res2 as $arr2) {
            // todo: subquery
            $a22 = DB::fetchAssoc("SELECT DISTINCT username FROM users WHERE ip = '$arr2[ip]' ORDER BY username");
            echo '<tr><td>'.$a22['username'].'</td></tr>';
        }
        print("</table>\n");
    }
    end_frame();
}

if ($HTTP_SERVER_VARS["REQUEST_METHOD"] === "POST") {
    // $dt = sqlesc(get_date_time());
    $msg = $_POST['msg'];
    if (!$msg)
        stderr("Error","Please Type In Some Text");

    $query = DB::query("SELECT distinct ip, userid FROM peers WHERE connectable = 'no'");
    while ($dat = $query->fetch()) {
        // todo: subquery
        DB::executeQuery('
            INSERT INTO messages (poster, sender, receiver, added, msg)
            VALUES (?, ?, ?, ?, ?)',
            [0, 0, $dat['userid'], get_date_time(), $msg]
        );
    }
    //mysql_query("INSERT INTO notconnectablepmlog ( user , date ) VALUES ( $CURUSER[id], $dt)") or sqlerr(__FILE__,__LINE__);
    header("Refresh: 0; url=findnotconnectable.php");
}

if ($_GET['action'] == "sendpm") {
    stdhead("Peers that are unconnectable");
    require_once("backend/admin-functions.php");
    adminmenu();
    begin_frame("Send PM");
?>
<table width=750 border=0 cellspacing=0 cellpadding=0><tr><td>
<div align=center>
<b>Mass Message to All Non Connectable Users</b>
<form method=post action=findnotconnectable.php>
<?php

if ($_GET["returnto"] || $_SERVER["HTTP_REFERER"])
{
?>
<input type=hidden name=returnto value=<?= $_GET["returnto"] ? $_GET["returnto"] : $_SERVER["HTTP_REFERER"]?>>
<?php
}
// default message
$body = "The tracker has determined that you are firewalled or NATed and cannot "
        . "accept incoming connections.\n\nThis means that other peers in the "
        . "swarm will be unable to connect to you, only you to them. Even worse,"
        . " if two peers are both in this state they will not be able to connect"
        . " at all. This has obviously a detrimental effect on the overall speed."
        . " \n\nThe way to solve the problem involves opening the ports used for"
        . " incoming connections (the same range you defined in your client) on "
        . "the firewall and/or configuring your NAT server to use a basic form "
        . "of NAT for that range instead of NAPT (the actual process differs "
        . "widely between different router models. Check your router "
        . "documentation and/or support forum. You will also find lots of "
        . "information on the subject at PortForward). \n\nAlso if you need help"
        . " please come into our IRC chat room or post in the forums your "
        . "problems. We are always glad to help out.\n\nThank You";
?>
<CENTER><table cellspacing=0 cellpadding=3>
<tr>
<td>Send Mass Messege To All Non Connectable Users<br>
</td>
</tr>
<tr><td><textarea name=msg cols=80 rows=18><?= $body ?></textarea></td></tr>
<tr>
<tr><td colspan=2 align=center><input type=submit value="Send" class=btn></td></tr>
</table>
<input type=hidden name=receiver value=<?= $receiver ?>>
</form>

</div></td></tr></table></CENTER>
<br>
NOTE: <B>No</B> HTML Code Allowed.
<?php
end_frame();
}

stdfoot();

