<?
require_once("backend/functions.php");

dbconn();

$body = trim($_POST["body"]);
if (!$body) {
  bark("Oops...", "You must enter something!");
  exit;
}

if (!isset($CURUSER))
	die();

if (!mkglobal("body:id"))
	die();

$id = 0 + $id;
if (!$id)
	die();

$res = mysql_query("SELECT 1 FROM torrents WHERE id = $id");
$row = mysql_fetch_array($res);
if (!$row)
	die();

mysql_query("INSERT INTO comments (user, torrent, added, text, ori_text) VALUES (" .
		$CURUSER["id"] . ",$id, '" . get_date_time() . "', " . sqlesc($body) .
     "," . sqlesc($body) . ")");

$newid = mysql_insert_id();

mysql_query("UPDATE torrents SET comments = comments + 1 WHERE id = $id");

//PM NOTIF
$res = mysql_query("SELECT name, owner FROM torrents WHERE id = $id") or sqlerr;
$arr = mysql_fetch_array($res);

$ras = mysql_query("SELECT commentpm FROM users WHERE id = $arr[owner]") or sqlerr;
                 $arg = mysql_fetch_array($ras);

				 if($arg["commentpm"] == 'yes' && $CURUSER['id'] != $arr["owner"])
                    {
$msg = "You have received a comment on your torrent [url=". $SITEURL."/torrents-details.php?id=$id]here[/url]";
mysql_query("INSERT INTO messages (poster, sender, receiver, msg, added) VALUES('0','0', " . $arr['owner'] . ", " . sqlesc($msg) . ", '" . get_date_time() . "')") or bark("", mysql_error());
                     }  
//PM NOTIF

header("Refresh: 0; url=torrents-details.php?id=$id&viewcomm=$newid#comm$newid");

?>
