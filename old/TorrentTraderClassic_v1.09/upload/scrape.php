<?php
//
// Scrape v1.1 FLASH (4.Feb.2006)
// Optimised for speed!, GZIP added, Added stats for "completed times"
//
//

ob_start("ob_gzhandler");
require_once("backend/config.php");

function dbconn($autoclean = false) {
    global $mysql_host, $mysql_user, $mysql_pass, $mysql_db;
    if (!@mysql_connect($mysql_host, $mysql_user, $mysql_pass))
    {
      die('dbconn: mysql_connect: ' . mysql_error());
    }
    mysql_select_db($mysql_db)
        or die('dbconn: mysql_select_db: ' + mysql_error());
}

function bark($heading = "Error", $text, $sort = "Error") {
  echo $text;
  die;
}

function sqlesc($s) {
	return "'".mysql_real_escape_string($s)."'";
}

function hex2bin($hexdata) {
  $bindata = "";
  for ($i=0;$i<strlen($hexdata);$i+=2) {
    $bindata.=chr(hexdec(substr($hexdata,$i,2)));
  }
 
  return $bindata;
}


dbconn(false);

// Windows compatibility section -- required for Windows servers
$usehash = false;
if (isset($_GET["info_hash"]))
{
	if (get_magic_quotes_gpc())
		$info_hash = stripslashes($_GET["info_hash"]);
	else
		$info_hash = $_GET["info_hash"];
	if (strlen($info_hash) == 20)
		$info_hash = bin2hex($info_hash);
	else if (strlen($info_hash) != 40)
		err("Invalid info hash value.");
	$info_hash = strtolower($info_hash);
	$usehash = true;
}
if ($usehash)
	$query = mysql_query("SELECT info_hash, seeders, leechers, times_completed, filename FROM torrents WHERE info_hash=".sqlesc($info_hash)) or bark("", "$info_hash - Database error. Cannot complete request.");
else
	$query = mysql_query("SELECT info_hash, seeders, leechers, times_completed, filename FROM torrents ORDER BY info_hash") or bark("", "Database error. Cannot complete request.");


echo "d5:filesd";

while ($row = mysql_fetch_row($query))
{
	$hash = hex2bin($row[0]);
	echo "20:".$hash."d";
	echo "8:completei".$row[1]."e";
	echo "10:downloadedi".$row[3]."e";
	echo "10:incompletei".$row[2]."e";
	if (isset($row[4]))
		echo "4:name".strlen($row[4]).":".$row[4];
	echo "e";
}

echo "ee";
header("Content-Type: text/plain");

die();
?>
