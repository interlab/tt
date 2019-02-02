<?php

require_once __DIR__ . '/../../backend/functions.php';
dbconn(false);
stdhead();

begin_frame("Todays Torrents");

$date_time = get_date_time(time() - (3600 * 24)); // the 24 is the hours you want listed
// $limit = 30;

if ($LOGGEDINONLY && !$CURUSER) {
    echo "<BR><BR><b><CENTER>You Are Not Logged In<br>Only Members Can View Torrents Please Signup.</CENTER><BR><BR>";
} else {
    $catresult = DB::executeQuery('
        SELECT c.id, c.name, COUNT(*) as num
        FROM categories AS c
            INNER JOIN torrents AS t ON t.category = c.id
        WHERE t.added >= ?
            AND t.banned = ?
            AND t.visible = ?
        GROUP BY c.sort_index', [$date_time, 'no', 'yes']);

    while ($cat = $catresult->fetch()) {
        // list($pagertop, $pagerbottom, $limit) = pager(30, 1000 - (1000 % $count), 'index.php?');
        $query = '
            SELECT t.id, t.category, t.leechers, t.seeders, t.name, t.times_completed, t.size,
                t.comments, t.nfo,t.owner, t.banned, t.numfiles, t.added, t.hits, t.filename,
                c.name AS cat_name, c.image AS cat_pic, u.privacy, u.username
            FROM torrents AS t
                LEFT JOIN categories AS c ON t.category = c.id
                LEFT JOIN users AS u ON t.owner = u.id
            WHERE t.banned = ?
                AND t.category = ' . $cat['id'] . '
                AND t.added >= ?
                AND t.visible = ?
            ORDER BY t.id DESC
            LIMIT 30';

        $res = DB::executeQuery($query, ['no', $date_time, 'yes']);

        echo '<B><a href=browse.php?cat='.$cat['id'].'>'.$cat['name'].'</a></B>';
        torrenttable($res);
        echo '<div align=left>» <a href=browse.php?cat='.$cat['id'].'>Show All</a></div>';

        echo '<BR>';
    }
}

end_frame();
stdfoot();

