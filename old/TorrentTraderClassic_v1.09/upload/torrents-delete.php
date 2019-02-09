<?php 

require_once 'backend/functions.php';

$id = (int) ($_POST['id'] ?? 0);
if (! $id) {
    die('bad id');
}

dbconn();
loggedinorreturn();

$row = DB::fetchAssoc('SELECT name, owner, seeders FROM torrents WHERE id = ' . $id);
if (! $row) {
    die('torrent not found');
}

if ($CURUSER["id"] != $row["owner"] && get_user_class() < UC_MODERATOR) {
    bark("Error", $txt['CANT_EDIT_TORRENT']);
    die;
}

$reason = trim($_POST["reason"]);
if (! $reason) {
    bark("Error", $txt['REASON_FOR_DELETE']);
    die;
}

deletetorrent($id);

write_log('Torrent ' . $id . ' (' . $row['name'] . ') was deleted by ' . $CURUSER['username'] . ' (' . $reason . ')');

stdhead("Torrent deleted!");
begin_frame();

if (isset($_POST["returnto"]))
    $ret = "<BR><BR>
        <a href=\"" . h($_POST["returnto"]) . "\">Back</a><BR>";
else
	$ret = "<BR><BR><a href=\"./\">Back to index</a><BR>";
?>

<?= $txt['TORRENT_DELETED']; ?>
<BR><p><?= $ret ?></p><br>

<?php 
end_frame();

stdfoot();
