<?php

require_once __DIR__ . '/../../backend/functions.php';
dbconn();
loggedinorreturn();

stdhead('Torrents Needing Seeds');

begin_frame($txt['TORRENT_NEED_SEED'], 'center');

$count = DB::fetchColumn('
    SELECT COUNT(*)
    FROM torrents
    WHERE banned = \'no\'
        AND leechers >= 5
        AND seeders <= 1
    LIMIT 1'
);

if (!$count) {
    echo '
    <div align="center" style="border-collapse: collapse; border: 1px solid #D6D9DB;">
    <span style="color: #000000;"><b>' . $txt['NO_TORRENT_NEED_SEED'] . '</b></span>
    </div>
    <br><br>';
} else {
    [$pagertop, $pagerbottom, $limit] = pager(15, $count, 'torrents-needseed.php', ['lastpagedefault' => 1]);

    $need_seeds = DB::fetchAll('
        SELECT torrents.*, users.username
        FROM torrents
            LEFT JOIN users ON torrents.owner = users.id
        WHERE banned = \'no\'
            AND leechers >= 5
            AND seeders <= 1
        ORDER BY seeders
        ' . $limit);

    if ($need_seeds) {
        echo '<font color="#FF0000">' . $txt['IF_YOU_HAVE'] . '</font>';

        echo $pagertop;

        echo '
        <br>
        <div align="center" style="border: 1px solid #646262; padding: 1px;">
            <table align="center" style="border-collapse: collapse; border: 1px solid #D6D9DB; width: 100%;">
                <td class=table_head align=center><font size=1 face=Verdana color=black>' . $txt['TNAME'] . '</td>
                <td class=table_head align=center><font size=1 face=Verdana color=black>' . $txt['UPLOADER'] . '</td>
                <td class=table_head align=center><font size=1 face=Verdana color=black>' . $txt['SIZE'] . '</td>
                <td class=table_head align=center><font size=1 face=Verdana color=black>' . $txt['SEEDS'] . '</td>
                <td class=table_head align=center><font size=1 face=Verdana color=black>' . $txt['LEECH'] . '</td>
                <td class=table_head align=center><font size=1 face=Verdana color=black>' . $txt['COMPLETE'] . '</td>
                <td class=table_head align=center><font size=1 face=Verdana color=black>' . $txt['ADDED'] . '</td>';

        foreach ($need_seeds as $row) {
            $torrname = h($row['name']);
            if (strlen($torrname) > 40) {
                $torrname = substr($torrname, 0, 40) . '...';
            }

            echo '
            <tr>
            <td class=table_col2 align=left><a href="torrents-details.php?id='.$row['id'].'">'.$torrname.'</a></td>
            <td class=table_col1 align=center><a href="account-details.php?id='.$row['owner'].'">'.$row['username'].'</a></td>
            <td class=table_col2 align=right><font size=1 face=Verdana>' . mksize($row['size']) . '</td>
            <td class=table_col1 align=center><font color=green>'.$row['seeders'].'</td>
            <td class=table_col2 align=center><font color=red>'.$row['leechers'].'</td>
            <td class=table_col1 align=center><font color=black>'.$row['times_completed'].'</td>
            <td class=table_col2 align=center><font color=purple>'.$row['added'].'</td>
            </tr>';
        }

        echo '</table></div>';
        echo $pagerbottom;
    } else {
    echo '
        <div align="center" style="border-collapse: collapse; border: 1px solid #D6D9DB;">
        <span style="color: #000000;"><b>' . $txt['NO_TORRENT_NEED_SEED'] . '</b></span>
        </div>
        <br><br>';
    }
    echo '<br><br>';
}

end_frame();
stdfoot();

