<?
require "backend/functions.php";
dbconn();
loggedinorreturn();
jmodonly();

if ($_SERVER["REQUEST_METHOD"] == "POST")
	$ip = $_POST["ip"];
else
	$ip = $_GET["ip"];
if ($ip)
{
	$nip = ip2long($ip);
	if ($nip == -1)
	  bark("Error", "Bad IP.");
	$res = mysql_query("SELECT * FROM bans WHERE $nip >= first AND $nip <= last") or sqlerr(__FILE__, __LINE__);
	if (mysql_num_rows($res) == 0)
	  bark("Result", "The IP address <b>$ip</b> is not banned.");
	else
	{
	  $banstable = "<table class=main border=0 cellspacing=0 cellpadding=5>\n" .
	    "<tr><td class=colhead>First</td><td class=colhead>Last</td><td class=colhead>Comment</td></tr>\n";
	  while ($arr = mysql_fetch_assoc($res))
	  {
	    $first = long2ip($arr["first"]);
	    $last = long2ip($arr["last"]);
	    $comment = h($arr["comment"]);
	    $banstable .= "<tr><td>$first</td><td>$last</td><td>$comment</td></tr>\n";
	  }
	  $banstable .= "</table>\n";
	  bark("Result", "<table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded style='padding-right: 5px'></td><td class=embedded>The IP address <b>$ip</b> is banned:</td></tr></table><p>$banstable</p>");
	}
}
stdhead();
require_once("backend/admin-functions.php");
adminmenu();
begin_frame("Test IP");
?><CENTER>
<form method=post action=iptest.php>
<table border=0 style="border-collapse: collapse" bordercolor="#646262" cellspacing=0 cellpadding=3>
<tr><td class=rowhead>IP Address</td><td><input type=text name=ip>&nbsp;<input type=submit class=btn value='OK'></td></tr>
</form>
</table></CENTER>

<?
end_frame();
stdfoot();
?>