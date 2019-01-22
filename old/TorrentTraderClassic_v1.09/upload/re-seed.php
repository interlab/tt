<?
ob_start();
require_once("backend/functions.php");
dbconn(false);
loggedinorreturn();

$reseedid = 0 + $_GET["id"];

if (!is_valid_id($id)) stderr("Error", "Torrent doesn't exist.");

stdhead("Reseed Request",true);

begin_frame("Reseed Request:");

if (isset($_COOKIE["TTrsreq$reseedid"])){ //check cookie for spam prevention
echo "<div align=left>You have recently made a request for this reseed. Please wait longer for another request.</div>";
//end cookie check
}else{
$res = mysql_query("SELECT owner FROM torrents WHERE id=$reseedid");
if (mysql_num_rows($res) < 1) stderr("Error", "Torrent doesn't exist");
$owner = mysql_result($res, 0);
echo "<BR><br><div align=left>Your request for a re-seed has been sent to the following members that have completed this torrent:<br><Br>";
//GET THE TORRENT AND USER ID FROM THIS TORRENTS COMPLETED LIST, YOU CAN AMMEND THIS TO LOOK AT SNATCHED TABLE IF NEEDED

$sres = mysql_query("SELECT * FROM downloaded WHERE torrent = " .$reseedid. "");
while ($srow = mysql_fetch_array($sres))
{
//SELECT THE COMPLETED USERS DETAILS
$res =mysql_query("SELECT id, username FROM users WHERE id = ".$srow["user"]." ") or die(mysql_error());

$result=mysql_fetch_array($res);

//DO MSG
print("<a href=account-details.php?id=$result[id]>".$result["username"]."</a> ");

$pn_msg = "" . $CURUSER["username"] . " has requested a re-seed on the torrent below because there are currently no or few seeds:

$SITEURL/torrents-details.php?id=".$_GET["id"]." \nThank You!";
$subject= '"Reseed Request"';
$rec=$result["id"];
$send=$CURUSER["id"];

//SEND MSG
mysql_query("INSERT INTO messages (sender, receiver, added, msg) VALUES ($send,$rec,NOW(), " . sqlesc($pn_msg) . ")") or die(mysql_error());

//request spamming prevention
@setcookie("TTrsreq".$reseedid, $reseedid);

}
mysql_query("INSERT INTO messages (sender, receiver, added, msg) VALUES (".$CURUSER['id'].",$owner,NOW(), " . sqlesc($pn_msg) . ")") or die(mysql_error());
}
echo "<BR><br>";
end_frame();

stdfoot();
ob_end_flush();
?>