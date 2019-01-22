<?
require_once("backend/functions.php");
dbconn();
loggedinorreturn();
adminonly();

if(isset($_POST["delmp"])) {
	$_POST["delmp"] = array_map("intval", $_POST["delmp"]);
	$do="DELETE FROM messages WHERE id IN (" . implode(", ", $_POST[delmp]) . ")";
	$res=mysql_query($do);
	$numDone = mysql_affected_rows();
}
stdhead();
begin_frame("Done");
echo "<br><B><center>Deleted $numDone messages.<BR><BR><a href=admin.php>Back To Staff CP</a></center></b>";
end_frame();
stdfoot();
?>