<?php

// if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    // die('Bad request');
    // die(json_encode(['fail' => 'Bad request'], JSON_UNESCAPED_UNICODE));
// }

dbconn(false);

if (isset($_GET['filelist'], $_GET['id'])) {
    echo tt_file_list($_GET['id']);
}

elseif (isset($_GET['peerslist'], $_GET['id'])) {
    echo tt_peers_list($_GET['id']);
}

else {
    echo '';
}


function tt_file_list(int $id)
{
    $data = Cache::rise('tt-filelist-'.$id, function() use ($id) {
        $d = [];
        $res = DB::query('SELECT * FROM files WHERE torrent = ' . $id . ' ORDER BY filename ASC');
        $i = 0;
        $allsize = 0;
        while ($row = $res->fetch()) {
            $d[$i++] = [$i, $row['filename'], mksize($row['size'])];
            $allsize += $row['size'];
        }
        $ret = [$d, mksize($allsize), $allsize];

        return $ret;
    });
    // dump($data);

    // sleep(5);

    return json_encode($data, JSON_UNESCAPED_UNICODE);
}

// AGENT DETECT
function getagent($httpagent, $peer_id="")
{
if (preg_match("/^Azureus ([0-9]+\.[0-9]+\.[0-9]+\.[0-9]\_B([0-9][0-9|*])(.+)$)/", $httpagent, $matches))
    return "Azureus/$matches[1]";
elseif (preg_match("/^Azureus ([0-9]+\.[0-9]+\.[0-9]+\.[0-9]\_CVS)/", $httpagent, $matches))
    return "Azureus/$matches[1]";
elseif (preg_match("/^Java\/([0-9]+\.[0-9]+\.[0-9]+)/", $httpagent, $matches))
    return "Azureus/<2.0.7.0";
elseif (preg_match("/^Azureus ([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $httpagent, $matches))
    return "Azureus/$matches[1]";
elseif (preg_match("/BitTorrent\/S-([0-9]+\.[0-9]+(\.[0-9]+)*)/", $httpagent, $matches))
    return "Shadow's/$matches[1]";
elseif (preg_match("/BitTorrent\/U-([0-9]+\.[0-9]+\.[0-9]+)/", $httpagent, $matches))
    return "UPnP/$matches[1]";
elseif (preg_match("/^BitTor(rent|nado)\\/T-(.+)$/", $httpagent, $matches))
    return "BitTornado/$matches[2]";
elseif (preg_match("/^BitTornado\\/T-(.+)$/", $httpagent, $matches))
    return "BitTornado/$matches[1]";
elseif (preg_match("/^BitTorrent\/ABC-([0-9]+\.[0-9]+(\.[0-9]+)*)/", $httpagent, $matches))
    return "ABC/$matches[1]";
elseif (preg_match("/^ABC ([0-9]+\.[0-9]+(\.[0-9]+)*)\/ABC-([0-9]+\.[0-9]+(\.[0-9]+)*)/", $httpagent, $matches))
    return "ABC/$matches[1]";
elseif (preg_match("/^ABC\/ABC-([0-9]+\.[0-9]+(\.[0-9]+)*)/", $httpagent, $matches))
    return "ABC $matches[1]";
elseif (preg_match("/^Python-urllib\/.+?, BitTorrent\/([0-9]+\.[0-9]+(\.[0-9]+)*)/", $httpagent, $matches))
    return "BitTorrent/$matches[1]";
elseif (preg_match('~^BitTorrent\/BitSpirit$~', $httpagent))
    return "BitSpirit";
elseif (substr($peer_id, 0, 5) == "-BB09")
    return "BitBuddy/0.9xx";
elseif (preg_match('~^DansClient~', $httpagent))
    return "XanTorrent";
elseif (substr($peer_id, 0, 8) == "-KT1100-")
    return "KTorrent/1.1";
elseif (preg_match("/^BitTorrent\/brst(.+)/", $httpagent, $matches))
    return "Burst/$matches[1]";
elseif (preg_match("/^RAZA (.+)$/", $httpagent, $matches))
    return "Shareaza/$matches[1]";
elseif (preg_match("/Rufus\/([0-9]+\.[0-9]+\.[0-9]+)/", $httpagent, $matches))
    return "Rufus/$matches[1]";
elseif (preg_match("/^BitTorrent\\/([0-9]+\\.[0-9]+(\\.[0-9]+)*)/", $httpagent, $matches))
{
if(substr($peer_id, 0, 6) == "exbc\08")
    return "BitComet/0.56";
elseif(substr($peer_id, 0, 6) == "exbc\09")
    return "BitComet/0.57";
elseif(substr($peer_id, 0, 6) == "exbc\0:")
    return "BitComet/0.58";
elseif(substr($peer_id, 0, 8) == "-BC0059-")
    return "BitComet/0.59";
elseif(substr($peer_id, 0, 8) == "-BC0060-")
    return "BitComet/0.60";
elseif(substr($peer_id, 0, 8) == "-BC0061-")
    return "BitComet/0.61";
elseif ((strpos($httpagent, 'BitTorrent/4.1.2')!== false) && (substr($peer_id, 2, 2) == "BS"))
    return "BitSpirit/v3";
elseif(substr($peer_id, 0, 7) == "exbc\0L")
    return "BitLord/1.0";
elseif(substr($peer_id, 0, 7) == "exbcL")
    return "BitLord/1.1";
else
    return "BitTorrent/$matches[1]";
}
elseif (preg_match("/^Python-urllib\\/([0-9]+\\.[0-9]+(\\.[0-9]+)*)/", $httpagent, $matches))
    return "G3 Torrent";
elseif (preg_match("/MLdonkey( |\/)([0-9]+\\.[0-9]+).*/", $httpagent, $matches))
    return "MLdonkey$matches[1]";
elseif (preg_match("/ed2k_plugin v([0-9]+\\.[0-9]+).*/", $httpagent, $matches))
    return "eDonkey/$matches[1]";
elseif (preg_match('~^uTorrent~', $httpagent))
{
if(substr($peer_id, 0, 8) == "-UT1130-")
    return "uTorrent 1.1.3";
if(substr($peer_id, 0, 8) == "-UT1140-")
    return "uTorrent 1.1.4";
if(substr($peer_id, 0, 8) == "-UT1150-")
    return "uTorrent 1.1.5";
if(substr($peer_id, 0, 8) == "-UT1161-")
    return "uTorrent 1.1.6.1";
if(substr($peer_id, 0, 8) == "-UT1171-")
    return "uTorrent 1.1.7.1";
if(substr($peer_id, 0, 8) == "-UT1172-")
    return "uTorrent 1.1.7.2";
if(substr($peer_id, 0, 8) == "-UT1200-")
    return "uTorrent/1.2";
if(substr($peer_id, 0, 8) == "-UT1220-")
    return "uTorrent/1.2.2";
if(substr($peer_id, 0, 8) == "-UT123B-")
    return "uTorrent/1.2.3b";
if(substr($peer_id, 0, 8) == "-UT1300-")
    return "uTorrent/1.3.0";
if(substr($peer_id, 0, 8) == "-UT1400-")
    return "uTorrent/1.4.0";
else
    return "uTorrent";
}
else
    return ($httpagent != "" ? $httpagent : "---");
}

// PEERS TABLE FUNCTION
function dltable($name, $arr, $torrent)
{
    global $CURUSER, $txt;

    $s = "<b>" . count($arr) . " $name</b>\n";
    if (!count($arr)) {
        return $s;
    }
    $s .= "\n";
    $s .= "<table class=table_table cellspacing=0 cellpadding=3 width=95%>\n";
    $s .= "<tr><td class=table_head>" . $txt['USERNAME'] . "/IP</td>" .
          "<td class=table_head>" . $txt['PORT'] . "</td>".
          "<td class=table_head>" . $txt['UPLOADED'] . "</td>".
          "<td class=table_head>" . $txt['DOWNLOADED'] . "</td>" .
          "<td class=table_head>" . $txt['RATIO'] . "</td>" .
          "<td class=table_head>" . $txt['COMPLETE'] . "</td>" .
          "<td class=table_head>" . $txt['CONNECTED'] . "</td>" .
          "<td class=table_head><b>" . $txt['IDLE'] . "</b></td>".
          "<td class=table_head><b>Client</b></td></tr>\n";
    $now = time();

    // DEFINE MODERATOR
    $moderator = (isset($CURUSER) && get_user_class() >= UC_JMODERATOR);
    $mod = get_user_class() >= UC_JMODERATOR;
    foreach ($arr as $e) {
        $s .= "<tr>\n";

        // todo: subquery
        $una = DB::fetchAssoc('
            SELECT id, username, privacy
            FROM users
            WHERE ip = ?
            ORDER BY last_access DESC
            LIMIT 1',
            [$e['ip']]
        );

        if ($una["privacy"] == "strong" && get_user_class() < UC_JMODERATOR && $CURUSER["id"] != $una["owner"]) {
            $s .= "<td class=table_col1><a href=#><b>Anonymous</b></a></td>\n";
        } elseif ($una["username"]) {
            $s .= "<td class=table_col1><a href=account-details.php?id=$una[id]><b>$una[username]</b></a></td>\n";
        } else {
            $s .= "<td class=table_col1>" . ($mod ? $e["ip"] : preg_replace('/\.\d+$/', ".xxx", $e["ip"])) . "</td>\n";
        }
        $s .= "<td class=table_col2>" . ($e['connectable'] == "yes" ? $e["port"] : "---") . "</td>\n";
        $s .= "<td class=table_col1>" . mksize($e["uploaded"]) . "</td>\n";
        $s .= "<td class=table_col2>" . mksize($e["downloaded"]) . "</td>\n";

        if ($e["downloaded"]) {
          $ratio = $e["uploaded"] / $e["downloaded"];
          if ($ratio < 0.1)
            $s .= "<td class=table_col2><font color=#ff0000>" . number_format($ratio, 2) . "</font></td>\n";
          else if ($ratio < 0.2)
            $s .= "<td class=table_col2><font color=#ee0000>" . number_format($ratio, 2) . "</font></td>\n";
          else if ($ratio < 0.3)
            $s .= "<td class=table_col2><font color=#dd0000>" . number_format($ratio, 2) . "</font></td>\n";
          else if ($ratio < 0.4)
            $s .= "<td class=table_col2><font color=#cc0000>" . number_format($ratio, 2) . "</font></td>\n";
          else if ($ratio < 0.5)
            $s .= "<td class=table_col2><font color=#bb0000>" . number_format($ratio, 2) . "</font></td>\n";
          else if ($ratio < 0.6)
            $s .= "<td class=table_col2><font color=#aa0000>" . number_format($ratio, 2) . "</font></td>\n";
          else if ($ratio < 0.7)
            $s .= "<td class=table_col2><font color=#990000>" . number_format($ratio, 2) . "</font></td>\n";
          else if ($ratio < 0.8)
            $s .= "<td class=table_col2><font color=#880000>" . number_format($ratio, 2) . "</font></td>\n";
          else if ($ratio < 0.9)
            $s .= "<td class=table_col2><font color=#770000>" . number_format($ratio, 2) . "</font></td>\n";
          else if ($ratio < 1)
            $s .= "<td class=table_col2><font color=#660000>" . number_format($ratio, 2) . "</font></td>\n";
          else
            $s .= "<td class=table_col2>" . number_format($ratio, 2) . "</td>\n";
        }
        elseif ($e["uploaded"])
            $s .= "<td class=table_col2>Inf.</td>\n";
        else
            $s .= "<td class=table_col2>---</td>\n";

        $s .= "<td class=table_col1>" . sprintf("%.2f%%", 100 * (1 - ($e["to_go"] / $torrent["size"]))) . "</td>\n";
        $s .= "<td class=table_col2>" . mkprettytime($now - $e["st"]) . "</td>\n";
        $s .= "<td class=table_col1>" . mkprettytime($now - $e["la"]) . "</td>\n";
        $s .= "<td class=table_col2 align=right>" . h(getagent($e["client"],$e["peer_id"])) . "</td>\n";
        $s .= "</tr>\n";
    }
    $s .= "</table>\n";
    return $s;
}
// END PEERS TABLE FUNCTION


function tt_peers_list(int $id)
{
    loadLanguage();

    $data = Cache::rise('tt-peerslist-'.$id, function() use ($id) {

        global $txt;

        ob_start();
        $downloaders = [];
        $seeders = [];
        $subres = DB::query('
            SELECT
                peer_id, client, seeder, ip, port, uploaded, downloaded, to_go, UNIX_TIMESTAMP(started) AS st, connectable,
                UNIX_TIMESTAMP(last_action) AS la
            FROM peers
            WHERE torrent = '.$id);
        while ($subrow = $subres->fetch()) {
            if ($subrow['seeder'] == 'yes')
                $seeders[] = $subrow;
            else
                $downloaders[] = $subrow;
        }

        function leech_sort($a, $b)
        {
            if ( isset( $_GET['usort'] ) )
                return seed_sort($a,$b);
            $x = $a['to_go'];
            $y = $b['to_go'];
            if ($x == $y)
                return 0;
            if ($x < $y)
                return -1;
            return 1;
        }

        function seed_sort($a,$b)
        {
            $x = $a['uploaded'];
            $y = $b['uploaded'];
            if ($x == $y)
                return 0;
            if ($x < $y)
                return 1;
            return -1;
        }

        usort($seeders, 'seed_sort');
        usort($downloaders, 'leech_sort');

        $row = DB::fetchAssoc('SELECT size FROM torrents WHERE id = '.$id);

        echo '<tr><td valign=top align=left><b>', $txt['SEEDS'], ' </b>', dltable(' '. $txt['SEEDS'].
            '(s) <a href="torrents-details.php?id='.$id.'" class="sublink">['.$txt['HIDE'].']</a>',
            $seeders, $row), ' </td></tr>';

        echo '<tr><td valign=top align=left><b>', $txt['LEECH'], ' </b>', dltable(' ' . $txt['LEECH'] .
            '(s) <a href="torrents-details.php?id='.$id.'" class="sublink">[' . $txt['HIDE'] . ']</a>',
            $downloaders, $row) . ' </td></tr>';

        return [ob_get_clean()];
    }, 300); // 5 minutes
    // dump($data);

    // sleep(5);

    return json_encode($data, JSON_UNESCAPED_UNICODE);
}
