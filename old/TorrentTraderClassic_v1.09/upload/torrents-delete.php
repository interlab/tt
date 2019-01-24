<?
//
// CSS and language updated 30.11.05
//
require_once("backend/functions.php");

if (!mkglobal("id"))
	genbark("missing form data");

$id = 0 + $id;
if (!$id)
	die();

dbconn();
loggedinorreturn();

$res = mysql_query("SELECT name,owner,seeders FROM torrents WHERE id = $id");
$row = mysql_fetch_array($res);
if (!$row)
	die();

if ($CURUSER["id"] != $row["owner"] && get_user_class() < UC_MODERATOR){
bark("Error", "" . CANT_EDIT_TORRENT . "");
die;
}

$reason = trim($_POST["reason"]);
if (!$reason){
bark("Error", "" . REASON_FOR_DELETE . "");
die;
}

deletetorrent($id);

write_log("Torrent $id ($row[name]) was deleted by $CURUSER[username] ($reason)\n");

stdhead("Torrent deleted!");
begin_frame();

if (isset($_POST["returnto"]))
	$ret = "<BR><BR><a href=\"" . h($_POST["returnto"]) . "\">Back</a><BR>";
else
	$ret = "<BR><BR><a href=\"./\">Back to index</a><BR>";

?>

<? echo "" . TORRENT_DELETED . "";?>
<BR><p><?= $ret ?></p></br>

<?
end_frame();

stdfoot();
?>