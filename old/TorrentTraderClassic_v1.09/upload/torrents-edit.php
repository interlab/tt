<?
//
// - Theme And Language Updated 27.Nov.05
//
require_once("backend/functions.php");

$id = 0 + $HTTP_GET_VARS["id"];
if (!$id)
	genbark("Oops", "Where is the ID Number?");

dbconn();
loggedinorreturn();

$res = mysql_query("SELECT * FROM torrents WHERE id = $id");
$row = mysql_fetch_array($res);
if (!$row)
	die();

stdhead("Edit Torrent \"" . $row["name"] . "\"");

begin_frame("" . EDIT_TORRENT . "", 'center');

if (!isset($CURUSER) || ($CURUSER["id"] != $row["owner"] && get_user_class() < UC_JMODERATOR)) {
	genbark("Can't edit this torrent", "<p>You're not the rightful owner, or you're not <a href=\"account-login.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&amp;nowarn=1\">logged in</a> properly.</p>\n");
}
else {
  print("<table align=center cellpadding=3 cellspacing=5 border=0 >");
	print("<form method=post action=take-edit.php enctype=multipart/form-data>\n");
	print("<input type=\"hidden\" name=\"id\" value=\"$id\">\n");
	if (isset($_GET["returnto"]))
		print("<input type=\"hidden\" name=\"returnto\" value=\"" . htmlspecialchars($_GET["returnto"]) . "\" />\n");
	tr("<B>" . TNAME . ":</B> ", "<input type=\"text\" name=\"name\" value=\"" . htmlspecialchars($row["name"]) . "\" size=\"80\" />", 1);
	tr("<B>" . NFO . ":</B> ", "<input type=radio name=nfoaction value='keep' CHECKED>" . KEEPCURRENT . ":<br>".
	"<input type=radio name=nfoaction value='update'>". UPDATENFO . ":<br /><input type=file name=nfo size=80>", 1);
if ((strpos($row["ori_descr"], "<") === false) || (strpos($row["ori_descr"], "&lt;") !== false))
  $c = "";
else
  $c = " checked";
	tr("<B>" . TDESC . ":</B> ", "<textarea name=\"descr\" rows=\"10\" cols=\"70\">" . htmlspecialchars($row["ori_descr"]) . "</textarea><br />" . HTML_NOT_ALLOWED . "", 1);

	$s = "<select name=\"type\">\n";

	$cats = genrelist();
	foreach ($cats as $subrow) {
		$s .= "<option value=\"" . $subrow["id"] . "\"";
		if ($subrow["id"] == $row["category"])
			$s .= " selected=\"selected\"";
		$s .= ">" . htmlspecialchars($subrow["name"]) . "</option>\n";
	}

	$s .= "</select>\n";
	tr("<B>" . TTYPE . ":</B> ", $s, 1);
	tr("<B>" . VISIBLE . ":</B> ", "<input type=\"checkbox\" name=\"visible\"" . (($row["visible"] == "yes") ? " checked=\"checked\"" : "" ) . " value=\"1\" /> Visible on main page<br /><table border=0 cellspacing=0 cellpadding=0 width=420><tr><td>" . VISIBLEONMAIN . "</td></tr></table>", 1);

if (get_user_class() >= UC_JMODERATOR) { 
		tr("<B>" . BANNED . ":</B> ", "<input type=\"checkbox\" name=\"banned\"" . (($row["banned"] == "yes") ? " checked=\"checked\"" : "" ) . " value=\"1\" /> " . BANNED_TORRENT . "", 1);}

	print("<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"" . SUBMIT_EDIT . "\" style='height: 25px; width: 100px'> <input type=reset value=\"" . RESETCHANGES . "\" style='height: 25px; width: 100px'></td></tr>\n");
	print("</form></table>\n");
	end_frame();
	echo "<br /><br />";
	begin_frame("" . DELETE_TORRENT . "", 'center');
	print("<form method=\"post\" action=\"torrents-delete.php\">\n");
	print("<input type=\"hidden\" name=\"id\" value=\"$id\">\n");
	if (isset($_GET["returnto"]))
		print("<input type=\"hidden\" name=\"returnto\" value=\"" . htmlspecialchars($_GET["returnto"]) . "\" />\n");
  	print("<B>" . REASON_FOR_DELETE . " </B> <input type=text size=40 name=reason> <input type=submit value='" . DELETE_IT . "' style='height: 25px'>\n");
	print("</form>\n");
	print("</p>\n");
}

end_frame();
stdfoot();

?>
