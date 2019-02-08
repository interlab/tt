<?php

require_once('backend/functions.php');
dbconn();

if (! isset($CURUSER)) {
	bark('Rating Error', 'Must be logged in to vote');
}

$id = (int) ($_REQUEST['id'] ?? 0);
if (! $id) {
	bark('Rating Error', 'Invalid id');
}

$rating = (int) ($_REQUEST['rating'] ?? 0);
if ($rating <= 0 || $rating > 5) {
	bark('Rating Error', 'Invalid rating');
}

$row = DB::fetchAssoc('SELECT owner FROM torrents WHERE id = ' . $id);
if (! $row) {
	bark('Rating Error', 'No such torrent');
}

if (intval($row['owner']) === $CURUSER['id']) {
    bark('Rating Error', 'You can\'t vote on your own torrents.');
}

try {
    $res = DB::executeUpdate('
        INSERT INTO ratings (torrent, user, rating, added) VALUES (?, ?, ?, ?)',
        [$id, $CURUSER['id'], $rating, date('Y-m-d H:i:s')]
    );
} catch (\Exception $e) {
    if ($e->getErrorCode() === 1062) {
        bark('Rating Error', 'You have already rated this torrent.');
    } else {
        // todo: write ro log
        bark('Rating Error', 'db error');
    }
}

DB::executeUpdate('
    UPDATE torrents
        SET numratings = numratings + 1, ratingsum = ratingsum + '.$rating.'
    WHERE id = ' . $id
);

header('Refresh: 0; url=torrents-details.php?id='.$id.'&rated=1');

