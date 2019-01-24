<?

require_once("backend/functions.php");

$action = $_GET["action"];
$commentid = 0 + $_GET["cid"];
 if (!is_valid_id($commentid))
		bark("Error", "Invalid ID $commentid.");

dbconn(false);
loggedinorreturn();
jmodonly();
 	
if ($action =="")
{
stdhead("Edit comment:");
begin_frame("Edit Comment");

   $res = mysql_query("SELECT * FROM comments WHERE id=$commentid");
  $arr = mysql_fetch_array($res);

	print("<center><b>Edit comment </b><p>\n");
	print("<form method=\"post\" action=\"edit-comments.php?action=doedit&amp;cid=$commentid\">\n");
	print("<input type=\"hidden\" name=\"returnto\" value=\"" . $_SERVER["HTTP_REFERER"] . "\" />\n");
	print("<input type=\"hidden\" name=\"cid\" value=\"$commentid\" />\n");
	print("<textarea name=\"text\" rows=\"10\" cols=\"60\">" . h($arr["text"]) . "</textarea></p>\n");
	print("<p><input type=\"submit\" class=btn value=\"Submit Changes\" /></p></form></center>\n");

end_frame();

stdfoot();
die;
}

if ($action == "doedit")
{
$text = $_POST['text'];
$commentid = $_POST['cid'];
$returnto = $_POST['returnto'];

$query="UPDATE comments SET text='$text' WHERE id=$commentid";
$result=mysql_query($query) or die("error");

		if ($returnto)
	  	header("Location: $returnto");
		else
		  header("Location: $SITEURL/");      // change later ----------------------
		die;
}

if ($action =="delete")
{
stdhead("Delete comment:");
begin_frame("Delete Comment");
$query=" DELETE FROM comments WHERE id = $commentid";
$result=mysql_query($query) or die("error");
echo "<br><br>Comment Deleted OK<br><br>";
end_frame();

stdfoot();
die;
}

?>