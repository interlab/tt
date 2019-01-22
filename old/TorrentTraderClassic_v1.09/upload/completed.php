<?
//
// CSS And language updated 4.12.05
//
require "backend/functions.php";
dbconn(false);
loggedinorreturn();
stdhead();

begin_frame("Recorded Downloads", center);

$id = $_GET["id"];
$id = 0 + $id;

$r1 = mysql_query("SELECT name FROM torrents WHERE id='$id'");
$a1 = mysql_fetch_assoc($r1);
$torrentname = $a1["name"];

echo "<h3>" . $torrentname . "</h3>\n";

$r1 = mysql_query("SELECT user FROM downloaded where torrent='$id'");
if (mysql_num_rows($r1) == 0)
{
	echo "<br><b>No downloads recorded.</b><br>If this torrent is EXTERNAL we are unable to tell who has completed it.<br>\n";	
}
else
{

if (get_user_class() >= UC_JMODERATOR)	
	echo "<font color=green>[Mod Viewable]</font> - This user has selected a STRONG privacy level, their details are hidden from public viewing<br><br>";

	echo "<table cellspacing=0 cellpadding=3 class=table_table><tr><td align=center class=table_head>" . USERNAME . "</font></td>";
	echo "<td align=center class=table_head>" . CURRENTLY_SEEDING . "</td>";
	echo "<td align=center class=table_head>" . RATIO . "</td></tr>";
	while ($a1 = mysql_fetch_assoc($r1))
	{
		$userid = $a1["user"];
		$r2 = mysql_query("SELECT username, ip, downloaded, uploaded, privacy FROM users where id='$userid'");
		$a2 = mysql_fetch_assoc($r2);
		$username = $a2["username"];
		$privacy = $a2["privacy"];
		$ip = $a2["ip"];
		if($a2["downloaded"] > 0) $sr = $a2["uploaded"] / $a2["downloaded"];
		else $sr = 0;
		$r3 = mysql_query("SELECT seeder FROM peers where ip='$ip' AND torrent='$id'");
		if (empty($username)) $username = "Unknown";
      if ($privacy == "strong") {
		  if (get_user_class() >= UC_JMODERATOR){
			  echo "<tr><td class=table_col1><a href=account-details.php?id=" . $userid . ">" . $username . "</a> <font color=green>[Mod Viewable]</font></td>\n";
		  }else{
			  echo "<tr><td class=table_col1>Private</td>\n";
		  }
		echo "<td align=center class=table_col2>\n";
		if (mysql_num_rows($r3) > 0) echo "<font color=green>" . YES . "</font>";
		else echo "<font color=red>" . NO . "</font>";
		echo "</td>\n";
		echo "<td align=center class=table_col1>" . number_format($sr, 2) . "</td>\n";
		echo "\n</tr>\n";
	  }else{
		echo "<tr><td><a href=account-details.php?id=" . $userid . ">" . $username . "</a></td>\n";
		echo "<td align=center class=table_col2>\n";
		if (mysql_num_rows($r3) > 0) echo "<font color=green>" . YES . "</font>";
		else echo "<font color=red>" . NO . "</font>";
		echo "</td>\n";
		echo "<td align=center class=table_col1>" . number_format($sr, 2) . "</td>\n";
		echo "\n</tr>\n";
	  }

	}
	echo "</table>";
}

echo "<p align=center><a href=torrents-details.php?id=" . $id . ">Back</a></p>";

end_frame();
stdfoot();
?>
