<?php

require_once("backend/functions.php");

dbconn();

$body = trim($_POST["body"] ?? '');
if (!$body) {
    bark("Oops...", "You must enter something!");
    exit;
}

if (!isset($CURUSER)) {
	die('curuser not found.');
}

loggedinorreturn();

$id = (int) ($_POST['id'] ?? 0);
if (!$id) {
	die('id not found.');
}

$arr = DB::fetchAssoc("SELECT name, owner FROM torrents WHERE id = $id LIMIT 1");
if (! $arr) {
	die('Torrent not found!');
}

DB::executeUpdate('
    INSERT INTO comments (user, torrent, added, text, ori_text)
        VALUES (?, ?, ?, ?, ?)',
    [ $CURUSER["id"], $id, get_date_time(), $body, $body]
);

$newid = DB::lastInsertId();

DB::executeUpdate('UPDATE torrents SET comments = comments + 1 WHERE id = ' . $id);

// PM NOTIF
$user = DB::fetchAssoc('SELECT commentpm FROM users WHERE id = ' . $arr['owner']);

if ($user["commentpm"] === 'yes' && $CURUSER['id'] != $arr["owner"]) {
    $msg = 'You have received a comment on your torrent [url='. $SITEURL. '/torrents-details.php?id=' . $id . ']here[/url]';
    DB::executeUpdate('
        INSERT INTO messages (poster, sender, receiver, msg, added) VALUES(?, ?, ?, ?, ?)',
        [0, 0, $arr['owner'], $msg, get_date_time()]
    );
}
// end PM NOTIF

header('Refresh: 0; url=torrents-details.php?id=' . $id . '&viewcomm=' . $newid . '#comm' . $newid);

