<?php

dbconn();
loggedinorreturn();

stdhead('Confirm');
begin_frame();


$takeuser = (int) ($_POST['user'] ?? 0);
$taketorrent = (int) ($_POST['torrent'] ?? 0);
$takeforumid = (int) ($_POST['forumid'] ?? 0);
$takeforumpost = (int) ($_POST['forumpost'] ?? 0);
$takereason = ($_POST['reason'] ?? '');


$user = (int) ($_GET['user'] ?? 0);
$torrent = (int) ($_GET['torrent'] ?? 0);
$forumid = (int) ($_GET['forumid'] ?? 0);
$forumpost = (int) ($_GET['forumpost'] ?? 0);


if (! empty($_POST['delreport'])) {
    jmodonly();

    $_POST['delreport'] = array_map('intval', $_POST['delreport']);
    $res = DB::query('SELECT id FROM reports WHERE dealtwith = 0 AND id IN (' . implode(', ', $_POST['delreport']) . ')');
    while ($arr = $res->fetch()) {
        DB::executeUpdate('UPDATE reports SET dealtwith = 1, dealtby = ' . $CURUSER['id'] . ' WHERE id = ' . $arr['id']);
    }

    ob_end_clean();
    header('Location: ' . $GLOBALS['SITEURL'] . '/admin.php');
    die('');
}

if (!empty($takeuser)) {
    if (empty($takereason)){
        bark('Error', 'You must enter a reason.');
        die;
    }

    $username = DB::fetchColumn('SELECT username FROM users WHERE id = ' . $takeuser);
    if (empty($username)) {
        print('User not found');
        end_frame();
        stdfoot();
        die();
    }
    $userlink = '<a href="account-details.php?id=' . $takeuser . '">' . $username . '</a>';
    $res = DB::fetchColumn('
        SELECT id
        FROM reports
        WHERE addedby = ' . $CURUSER['id'] . '
            AND votedfor = ' . $takeuser . '
            AND type = \'user\''
    );
    if (! $res) {
        DB::executeUpdate('INSERT into reports (addedby, votedfor, type, reason) VALUES (?, ?, ?, ?)',
            [$CURUSER['id'], $takeuser, 'user', $takereason]
        );
        print("User: $userlink, Reason: $takereason<p></p>Successfully Reported");
        end_frame();
        stdfoot();
        die();
    } else {
        print("You have already reported user $userlink");
        end_frame();
        stdfoot();
        die();
    }
}

if (($taketorrent != '') && ($takereason != '')) {
    if (! $takereason) {
        bark('Error', 'You must enter a reason.');
        die;
    }
    $res = DB::fetchColumn('SELECT id FROM reports WHERE addedby = ' . $CURUSER['id']
        . ' AND votedfor = ' . $taketorrent . ' AND type = \'torrent\'');
    if (! $res) {
        DB::insert('reports', [
            'addedby' => $CURUSER['id'], 'votedfor' => $taketorrent,
            'type' => 'torrent', 'reason' => $takereason
        ]);
        print("Torrent: $taketorrent, Reason: $takereason<p></p>Successfully Reported");
        end_frame();
        stdfoot();
        die();
    } else {
        print("You have already reported torrent $taketorrent");
        end_frame();
        stdfoot();
        die();
    }
}

if ($user != '') {
    $arr = DB::fetchAssoc('SELECT username, class FROM users WHERE id = ' . $user);
    if (! $arr) {
        print('Invalid UserID');
        end_frame();
        stdfoot();
        die();
    }

    if ($arr['class'] >= UC_JMODERATOR) {
        print('Can\'t report this user, sorry.');
        end_frame();
        stdfoot();
        die();
    } else {
        echo '<h2>Are you sure you would like to report user <a href="userdetails.php?id='.$user.'"><b>'.$arr['username'].'</b></a>?</h2>
            <p>Please note, this is <b>not</b> to be used to report leechers, we have scripts in place to deal with them</p>
            <b>Reason</b> (required): <form method=post action=report.php>
            <input type="hidden" name="user" value="' . $user . '">
            <input type=text size=100 name=reason>
            <input type=submit class=btn value=Confirm></form>';
    }
}


if ($torrent != '') {
    $arr = DB::fetchAssoc('SELECT name FROM torrents WHERE id = ' . $torrent);

    if (! $arr) {
        print("Invalid TorrentID");
        end_frame();
        stdfoot();
        die();
    }

    echo '
        <h2>Are you sure you would like to report torrent <a href="details.php?id='.$torrent.'"><b>'.$arr['name'].'</b></a>?</h2>
        <b>Reason</b> (required): <form method="post" action="report.php">
        <input type="hidden" name="torrent" value="'.$torrent.'">
        <input type="text" size="100" name="reason">
        <input type="submit" class="btn" value="Confirm"></form>';
}


if (($forumid != '') && ($forumpost != '')) {
    $arr = DB::fetchAssoc('SELECT subject FROM forum_topics WHERE id = '.$forumid);

    if (! $arr) {
        print('Invalid Forum ID');
        end_frame();
        stdfoot();
        die();
    }

    print("<h2>Are you sure you would like to report the following forum post 
        <a href=forums.php?action=viewtopic&topicid=$forumid&page=p#$forumpost><b>$arr[subject]</b></a>?</h2>
        <p></p>
        <b>Reason</b> (required): <form method=post action=report.php>
        <input type=hidden name=forumid value=$forumid>
        <input type=hidden name=forumpost value=$forumpost>
        <input type=text size=100 name=reason><p></p>
        <input type=submit class=btn value=Confirm></form>");
}


if (($takeforumid != '') && ($takereason != '')) {
    $res = DB::fetchColumn("
        SELECT id
        FROM reports
        WHERE addedby = $CURUSER[id]
            AND votedfor= $takeforumid
            AND votedfor_xtra= $takeforumpost
            AND type = 'forum'");
    if (! $res) {
        if (!$takereason) {
            bark("Error", "You must enter a reason.");
            die;
        }

        DB::executeUpdate('
            INSERT into reports (addedby, votedfor, votedfor_xtra, type, reason)
            VALUES (?, ?, ?, ?, ?)',
            [$CURUSER['id'], $takeforumid, $takeforumpost, 'forum', $takereason]
        );
        print("User: $takeuser, Reason: $takereason<p></p>Successfully Reported");
        end_frame();
        stdfoot();
        die();
    } else {
        print('');
        end_frame();
        stdfoot();
        die();
    }
}

if (($user != '') && ($torrent != '')) {
    print("<h1>Missing Info</h1>");
}

end_frame();
stdfoot();
