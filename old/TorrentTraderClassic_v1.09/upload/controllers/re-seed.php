<?php

dbconn(false);
loggedinorreturn();

$reseedid = (int) ($_GET['id'] ?? 0);

if (!is_valid_id($reseedid)) {
    stderr('Error', 'Torrent doesn\'t exist.');
}

stdhead('Reseed Request', true);
begin_frame('Reseed Request:');

// check cookie for spam prevention
if (isset($_COOKIE['TTrsreq' . $reseedid])) {
    echo '<div>You have recently made a request for this reseed. Please wait longer for another request.</div>';
    // end cookie check
} else {
    $owner = DB::fetchColumn('SELECT owner FROM torrents WHERE id = ' . $reseedid);
    if (empty($owner)) {
        stderr('Error', 'Torrent doesn\'t exist');
    }

    echo '<br><br><div align=left>Your request for a re-seed has been sent to the following members that have completed this torrent:<br><br>';
    // GET THE TORRENT AND USER ID FROM THIS TORRENTS COMPLETED LIST,
    // YOU CAN AMMEND THIS TO LOOK AT SNATCHED TABLE IF NEEDED

    $pn_msg = $CURUSER['username'] .
        ' has requested a re-seed on the torrent below because there are currently no or few seeds: '
        . $SITEURL . '/torrents-details.php?id=' . $reseedid . " \nThank You!";

    # @todo: set limit in settings in admin panel
    $res = DB::query('
        SELECT u.id, u.username
        FROM downloaded AS d
            INNER JOIN users AS u ON (u.id = d.user)
        WHERE d.torrent = ' . $reseedid . '
            AND d.user != ' . $CURUSER['id'] . '
        LIMIT 500');
    while ($row = $res->fetch()) {
        // DO MSG
        echo '<a href=account-details.php?id='.$row['id'].'>'.$row['username'].'</a> ';

        $subject = '"Reseed Request"';
        $rec = $row['id'];
        $send = $CURUSER['id'];

        // SEND MSG
        DB::insert('messages', ['sender' => $send, 'receiver' => $rec, 'added' => now(), 'msg' => $pn_msg]);

        // request spamming prevention
        @setcookie('TTrsreq' . $reseedid, $reseedid);

    }
    echo '</div>';

    if ($CURUSER['id'] != $owner) {
        DB::insert('messages', ['sender' => $CURUSER['id'], 'receiver' => $owner, 'added' => now(), 'msg' => $pn_msg]);
    }
}

echo '<br><br>';
end_frame();
stdfoot();

