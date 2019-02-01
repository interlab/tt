<?php

ob_start();
require_once("backend/functions.php");

dbconn();

$id = (int) ($_GET["id"] ?? 0);

IF ($LOGGEDINONLY){
	loggedinorreturn();
}

if (! $id) {
	bark("ID not found", "You can't download, if you don't tell me what you want!");
}

$name = DB::fetchColumn('SELECT filename FROM torrents WHERE id = ' . $id);

$fn = "$torrent_dir/$id.torrent";

if (!$name)
	bark("File not found", "No file has been found with that ID!");
if (!is_file($fn))
	bark("File not found", "The ID has been found on the Database, but the torrent has gone!<BR><BR>Check Server Paths and CHMODs Are Correct!");
if (!is_readable($fn))
	bark("File not found", "The ID and torrent were found, but the torrent is NOT readable!");

DB::query("UPDATE torrents SET hits = hits + 1 WHERE id = $id");

header("Content-Type: application/x-bittorrent");
header("Content-Disposition: attachment; filename=\"$name\"");

readfile($fn);
