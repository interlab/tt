<?php

require_once __DIR__ . '/../../backend/functions.php';

dbconn();

loggedinorreturn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach(['id', 'name', 'descr', 'type'] as $v) {
        if (!isset($_POST[$v])) {
            genbark("missing form data");
        }
    }

    $id = (int) $_POST['id'];
    if (!$id) {
        die('bad id');
    }

    $name = $_POST['name'];
    $descr = $_POST['descr'];
    $type = (int) $_POST['type'];

    $row = DB::fetchAssoc('SELECT owner, filename, save_as FROM torrents WHERE id = ' . $id);
    if (! $row) {
        die('torrent not found');
    }

    if ($CURUSER["id"] != $row["owner"] && get_user_class() < UC_JMODERATOR) {
        genbark("You're not the owner! How did that happen?\n");
    }

    $updateset = [];
    $params = [];

    $fname = $row["filename"];
    preg_match('/^(.+)\.torrent$/si', $fname, $matches);
    $shortfname = $matches[1];
    $dname = $row["save_as"];

    $updateset[] = 'name = ?';
    $params[] = $name;

    $updateset[] = 'search_text = ?';
    $params[] = searchfield($shortfname . ' ' . $dname);

    $updateset[] = 'descr = ?';
    $params[] = $descr;

    $updateset[] = 'ori_descr = ?';
    $params[] = $descr;

    $updateset[] = 'category = ?';
    $params[] = $type;

    if (!empty($_POST["banned"])) {
        $updateset[] = 'banned = ?';
        $params[] = 'yes';
        $_POST["visible"] = 0;
    } else {
        $updateset[] = 'banned = ?';
        $params[] = 'no';
    }

    $updateset[] = 'visible = ?';
    $params[] = !empty($_POST["visible"]) ? "yes" : "no";

    DB::executeUpdate('UPDATE torrents SET ' . join(',', $updateset) . ' WHERE id = ' . $id, $params);

    write_log("Torrent $id ($name) was edited by $CURUSER[username]");

    $returl = "torrents-details.php?id=$id&edited=1";
    if (isset($_POST["returnto"])) {
        $returl .= "&returnto=" . urlencode($_POST["returnto"]);
    }
    header("Refresh: 0; url=$returl");

    die('');
}


$id = (int) ($_GET["id"] ?? 0);
if (!$id) {
	genbark("Oops", "Where is the ID Number?");
}

$row = DB::fetchAssoc('SELECT * FROM torrents WHERE id = ' . $id . ' LIMIT 1');

if (!$row) {
	die('Torrent not found.');
}

stdhead('Edit Torrent "' . $row["name"] . '"');

begin_frame($txt['EDIT_TORRENT'].' - <a href="torrents-details.php?id='.$id.'">'.$row['name'].'</a>', 'center');

if (!isset($CURUSER) || ($CURUSER["id"] != $row["owner"] && get_user_class() < UC_JMODERATOR)) {
	genbark("Can't edit this torrent",
        "<p>You're not the rightful owner, or you're not <a href=\"account-login.php?returnto="
        . urlencode($_SERVER["REQUEST_URI"]) . "&amp;nowarn=1\">logged in</a> properly.</p>\n");
} else {
    echo '<table align=center cellpadding=3 cellspacing=5 border=0>
        <form method="post" action="torrents-edit.php" enctype="multipart/form-data">
        <input type="hidden" name="id" value="'.$id.'">';
	if (isset($_GET["returnto"])) {
		print("<input type=\"hidden\" name=\"returnto\" value=\"" . h($_GET["returnto"]) . "\" />\n");
    }
	tr("<B>" . $txt['TNAME'] . ":</B> ", "<input type=\"text\" name=\"name\" value=\"" . h($row["name"]) . "\" size=\"80\" />", 1);

	tr("<B>" . $txt['TDESC'] . ":</B> ", "<textarea name=\"descr\" rows=\"10\" cols=\"70\">"
        . h($row["ori_descr"]) . "</textarea><br />" . $txt['HTML_NOT_ALLOWED'] . "", 1);

	$s = "<select name=\"type\">\n";

	$cats = genrelist();
	foreach ($cats as $subrow) {
		$s .= "<option value=\"" . $subrow["id"] . "\"";
		if ($subrow["id"] == $row["category"])
			$s .= " selected=\"selected\"";
		$s .= ">" . h($subrow["name"]) . "</option>\n";
	}

	$s .= "</select>\n";
	tr("<B>" . $txt['TTYPE'] . ":</B> ", $s, 1);
	tr("<B>" . $txt['VISIBLE'] . ":</B> ", "<input type=\"checkbox\" name=\"visible\""
        . (($row["visible"] == "yes") ? " checked=\"checked\"" : "" )
        . " value=\"1\" /> Visible on main page<br /><table border=0 cellspacing=0 cellpadding=0 width=420><tr><td>"
        . $txt['VISIBLEONMAIN'] . "</td></tr></table>", 1);

    if (get_user_class() >= UC_JMODERATOR) { 
		tr("<B>" . $txt['BANNED'] . ":</B> ", "<input type=\"checkbox\" name=\"banned\""
            . (($row["banned"] == "yes") ? " checked=\"checked\"" : "" )
            . " value=\"1\" /> " . $txt['BANNED_TORRENT'] . "", 1);}

	echo '<tr><td colspan="2" align="center"><input type="submit" value="'
        . $txt['SUBMIT_EDIT'] . '" style="height: 25px; width: 100px"></td></tr>
        </form></table>';
	end_frame();

	echo "<br /><br />";

	begin_frame($txt['DELETE_TORRENT'], 'center');
	print("<form method=\"post\" action=\"torrents-delete.php\">\n");
	print("<input type=\"hidden\" name=\"id\" value=\"$id\">\n");
	if (isset($_GET["returnto"])) {
		print("<input type=\"hidden\" name=\"returnto\" value=\"" . h($_GET["returnto"]) . "\" />\n");
    }
  	print("<B>" . $txt['REASON_FOR_DELETE'] . " </B> <input type=text size=40 name=reason> <input type=submit value='"
        . $txt['DELETE_IT'] . "' style='height: 25px'>\n");
	print("</form>\n");
	print("</p>\n");
}

end_frame();
stdfoot();
