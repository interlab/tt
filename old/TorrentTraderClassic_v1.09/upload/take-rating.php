<?
require_once("backend/functions.php");
dbconn();

if (!isset($CURUSER))
	bark("Rating Error", "Must be logged in to vote");

if (!mkglobal("rating:id"))
	bark("Rating Error", "Missing form data");

$id = 0 + $id;
if (!$id)
	bark("Rating Error", "Invalid id");

$rating = 0 + $rating;
if ($rating <= 0 || $rating > 5)
	bark("Rating Error", "Invalid rating");

$res = mysql_query("SELECT owner FROM torrents WHERE id = $id");
$row = mysql_fetch_array($res);
if (!$row)
	bark("Rating Error", "No such torrent");

//if ($row["owner"] == $CURUSER["id"])
//	bark("Rating Error", "You can't vote on your own torrents.");

$res = mysql_query("INSERT INTO ratings (torrent, user, rating, added) VALUES ($id, " . $CURUSER["id"] . ", $rating, NOW())");
if (!$res) {
	if (mysql_errno() == 1062)
		bark("Rating Error", "You have already rated this torrent.");
	else
		bark("Rating Error", mysql_error());
}

mysql_query("UPDATE torrents SET numratings = numratings + 1, ratingsum = ratingsum + $rating WHERE id = $id");
header("Refresh: 0; url=torrents-details.php?id=$id&rated=1");
?>
