<?php

require_once("backend/functions.php");

dbconn();

$body = trim($_POST["body"]);
if (!$body) {
   bark("Oops...", "You must enter something!");
   exit;
}

if (!isset($CURUSER))
    die('not for quest');

$id = (int) ($_POST['id'] ?? 0);
if (!$id)
    die('bad id');

$col = DB::fetchColumn("SELECT 1 FROM news WHERE id = $id LIMIT 1");
if (!$col)
    die('news not found');

DB::executeUpdate('INSERT INTO comments (user, news, added, text, ori_text) VALUES (?, ?, ?, ?, ?)',
    [ $CURUSER["id"], $id, get_date_time(), $body, $body ]
);

$newid = DB::lastInsertId();

DB::query("UPDATE news SET comments = comments + 1 WHERE id = $id");

header("Refresh: 0; url=show-archived.php?id=$id&viewcomm=$newid#comm$newid");

