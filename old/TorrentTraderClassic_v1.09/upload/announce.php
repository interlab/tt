<?
//
// TORRENTTRADER ANNOUNCE V2 (WWW.TORRENTTRADER.ORG)
// CHANGES: MODIFIED TO INCLUDE FUNCTIONS CALLED FROM FUNCTIONS.PHP AND BENC.PHP
// INCLUDES: WAIT TIMES, LIVE STATS UPDATE, PORT BLOCKING, NEW GZIP OUTPUT, ANTI-BROWSER, FULL SNATCHED DETAILS
//
// LINE 303/304 UNCONNECTABLE POSSIBLE FIX, SIMPLY CHANGE THE COMMENTED SQL
//
// SUPPORT FOR OLD "WHO COMPLETED" MOD ON LINE 409-413 
//
//
require_once("backend/config.php");

//START FUNCTIONS

function unesc($x) {
    if (get_magic_quotes_gpc())
        return stripslashes($x);
    return $x;
}

function is_valid_id($id)
{
  return is_numeric($id) && ($id > 0) && (floor($id) == $id);
}

function validip($ip)
{
	if (!empty($ip) && ip2long($ip)!=-1)
	{
		$reserved_ips = array (
				array('0.0.0.0','2.255.255.255'),
				array('10.0.0.0','10.255.255.255'),
				array('127.0.0.0','127.255.255.255'),
				array('169.254.0.0','169.254.255.255'),
				array('172.16.0.0','172.31.255.255'),
				array('192.0.2.0','192.0.2.255'),
				array('192.168.0.0','192.168.255.255'),
				array('255.255.255.0','255.255.255.255')
		);

		foreach ($reserved_ips as $r)
		{
				$min = ip2long($r[0]);
				$max = ip2long($r[1]);
				if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
		}
		return true;
	}
	else return false;
}

function getip() {
   if (isset($_SERVER)) {
     if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && validip($_SERVER['HTTP_X_FORWARDED_FOR'])) {
       $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
     } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && validip($_SERVER['HTTP_CLIENT_IP'])) {
       $ip = $_SERVER['HTTP_CLIENT_IP'];
     } else {
       $ip = $_SERVER['REMOTE_ADDR'];
     }
   } else {
     if (getenv('HTTP_X_FORWARDED_FOR') && validip(getenv('HTTP_X_FORWARDED_FOR'))) {
       $ip = getenv('HTTP_X_FORWARDED_FOR');
     } elseif (getenv('HTTP_CLIENT_IP') && validip(getenv('HTTP_CLIENT_IP'))) {
       $ip = getenv('HTTP_CLIENT_IP');
     } else {
       $ip = getenv('REMOTE_ADDR');
     }
   }

   return $ip;
}

function dbconn($autoclean = false) {
    global $mysql_host, $mysql_user, $mysql_pass, $mysql_db;
    if (!@mysql_connect($mysql_host, $mysql_user, $mysql_pass))
    {
      die('dbconn: mysql_connect: ' . mysql_error());
    }
    mysql_select_db($mysql_db)
        or die('dbconn: mysql_select_db: ' + mysql_error());
}

function hash_pad($hash) {
    return str_pad($hash, 20);
}

function hash_where($name, $hash) {
    $shhash = preg_replace('/ *$/s', "", $hash);
    return "($name = " . sqlesc($hash) . " OR $name = " . sqlesc($shhash) . ")";
}

function sqlesc($x) {
    return "'".mysql_real_escape_string($x)."'";
}

function err($msg)
{
	benc_resp(array("failure reason" => array(type => "string", value => $msg)));
	exit();
}

function benc($obj) {
	if (!is_array($obj) || !isset($obj["type"]) || !isset($obj["value"]))
		return;
	$c = $obj["value"];
	switch ($obj["type"]) {
		case "string":
			return benc_str($c);
		case "integer":
			return benc_int($c);
		case "list":
			return benc_list($c);
		case "dictionary":
			return benc_dict($c);
		default:
			return;
	}
}

function benc_str($s) {
	return strlen($s) . ":$s";
}

function benc_int($i) {
	return "i" . $i . "e";
}

function benc_list($a) {
	$s = "l";
	foreach ($a as $e) {
		$s .= benc($e);
	}
	$s .= "e";
	return $s;
}

function benc_dict($d) {
	$s = "d";
	$keys = array_keys($d);
	sort($keys);
	foreach ($keys as $k) {
		$v = $d[$k];
		$s .= benc_str($k);
		$s .= benc($v);
	}
	$s .= "e";
	return $s;
}

function benc_resp($d)
{
	benc_resp_raw(benc(array(type => "dictionary", value => $d)));
}

function benc_resp_raw($x) {

header("Content-Type: text/plain");

header("Pragma: no-cache");

if ($_SERVER["HTTP_ACCEPT_ENCODING"] == "gzip") {

 header("Content-Encoding: gzip");

 echo gzencode($x, 9, FORCE_GZIP);

} else

 print($x);

}

function gmtime()
{
    return strtotime(get_date_time());
}

function get_date_time($timestamp = 0)
{
  if ($timestamp)
    return date("Y-m-d H:i:s", $timestamp);
  else
    return gmdate("Y-m-d H:i:s");
}

function portblacklisted($port)
{
	// direct connect
	if ($port >= 411 && $port <= 413) return true;

	// bittorrent (AZUREUS)
	// if ($port >= 6881 && $port <= 6889) return true;

	// kazaa
	if ($port == 1214) return true;

	// gnutella
	if ($port >= 6346 && $port <= 6347) return true;

	// emule
	if ($port == 4662) return true;

	// winmx
	if ($port == 6699) return true;

	return false;
}

//////////////////////// NOW WE DO THE ANNOUNCE CODE ////////////////////////

// BLOCK ACCESS WITH WEB BROWSERS
$agent = $_SERVER["HTTP_USER_AGENT"];
if (ereg("^Mozilla\\/", $agent) || ereg("^Opera\\/", $agent) || ereg("^Links ", $agent) || ereg("^Lynx\\/", $agent))
	err("torrent not registered with this tracker");



//GET DETAILS OF PEERS ANNOUNCE
$req = "info_hash:peer_id:!ip:port:uploaded:downloaded:left:!event";
foreach (explode(":", $req) as $x)
{
	if ($x[0] == "!")
	{
		$x = substr($x, 1);
		$opt = 1;
	}
	else
		$opt = 0;
	if (!isset($_GET[$x]))
	{
		if (!$opt)
			err("missing key");
		continue;
	}
	$GLOBALS[$x] = unesc($_GET[$x]);
}

foreach (array("info_hash","peer_id") as $x)
{
	if (strlen($GLOBALS[$x]) != 20)
		err("invalid $x (" . strlen($GLOBALS[$x]) . " - " . urlencode($GLOBALS[$x]) . ")");
}

if (empty($ip) || !validip($ip))
	$ip = getip();

$port = 0 + $port;
$downloaded = 0 + $downloaded;
$uploaded = 0 + $uploaded;
$left = 0 + $left;

$rsize = 50;
foreach(array("num want", "numwant", "num_want") as $k)
{
	if (isset($_GET[$k]))
	{
		$rsize = 0 + $_GET[$k];
		break;
	}
}

//PORT CHECK
if (!$port || $port > 0xffff)
	err("invalid port");

//TRACKER EVENT CHECK
if (!isset($event))
	$event = "";

$seeder = ($left == 0) ? "yes" : "no";

dbconn(false);

// GET HASH AND SELECT FROM DB
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
	$sql = mysql_query("SELECT id, name, category, banned, seeders + leechers AS numpeers, UNIX_TIMESTAMP(added) AS ts FROM torrents WHERE info_hash='$info_hash'") or err("$info_hash - Database error. Cannot complete request.");
else
	$sql = mysql_query("SELECT id, name, category, banned, seeders + leechers AS numpeers, UNIX_TIMESTAMP(added) AS ts FROM torrents ORDER BY info_hash") or err("Database error. Cannot complete request.");


//DOES THE TORRENT EXIST?
$torrent = mysql_fetch_array($sql);
if (!$torrent)
	err("torrent not found on this tracker - hash = " . $info_hash);

//IS THE IP REGISTERED, IF SO CALL USER ID
$userid = 0;
if ($MEMBERSONLY){
	$rz = mysql_query("SELECT id, uploaded, downloaded, class FROM users WHERE ip='$ip' AND enabled='yes' LIMIT 1") or err('Tracker error (1)');
	if (mysql_num_rows($rz) < 1)
		err("Unrecognized host ($ip). Please go to $SITEURL to sign-up or login.");

		$azz = mysql_fetch_assoc($rz);
		$userid = $azz["id"];
}

//SELECT DATA FROM PEERS TABLE
$torrentid = $torrent["id"];
$torrentname = $torrent["name"];
$torrentcategory = $torrent["category"];
$fields = "seeder, UNIX_TIMESTAMP(last_action) AS ez, peer_id, ip, port, uploaded, downloaded, userid";
$numpeers = $torrent["numpeers"];
$limit = "";
if ($numpeers > $rsize)
	$limit = "ORDER BY RAND() LIMIT $rsize";

// ABC and CONNECTABLE issues FIX, swap commented line over
$res = mysql_query("SELECT $fields FROM peers WHERE torrent = $torrentid AND connectable = 'yes' $limit");
//$res = mysql_query("SELECT $fields FROM peers WHERE torrent = $torrentid  $limit");

//DO SOME BENC STUFF TO THE PEERS CONNECTION
$resp = "d" . benc_str("interval") . "i" . $announce_interval . "e" . benc_str("peers") . "l";
unset($self);
while ($row = mysql_fetch_assoc($res))
{
	$row["peer_id"] = hash_pad($row["peer_id"]);

	if ($row["peer_id"] === $peer_id)
	{
		$userid = $row["userid"];
		$self = $row;
		continue;
	}

	$resp .= "d" .
		benc_str("ip") . benc_str($row["ip"]) .
		benc_str("peer id") . benc_str($row["peer_id"]) .
		benc_str("port") . "i" . $row["port"] . "e" .
		"e";
}
$resp .= "ee";

$selfwhere = "torrent = $torrentid AND " . hash_where("peer_id", $peer_id);


// FILL $SELF WITH DETAILS FROM PEERS TABLE (CONNECTING PEERS DETAILS)
if (!isset($self))
{
	$res = mysql_query("SELECT $fields FROM peers WHERE $selfwhere");
	$row = mysql_fetch_assoc($res);
	if ($row){
		$userid = $row["userid"];
		$self = $row;
	}
}
// END $SELF FILL

// SNATCHED MOD - GET DATE TIME/OFFSET
$dt = gmtime() - 180;//OFFSET
$dt = sqlesc(get_date_time($dt));

if (!isset($self))//IF PEER IS NOT IN PEERS TABLE DO THE WAIT TIME CHECK
{

	if ($MEMBERSONLY_WAIT && $MEMBERSONLY){
		if ($left > 0 && $azz["class"] == 0 )
			{
				$gigs = $azz["uploaded"] / (1024*1024*1024);
				$elapsed = floor((gmtime() - $torrent["ts"]) / 3600);
				$ratio = (($azz["downloaded"] > 0) ? ($azz["uploaded"] / $azz["downloaded"]) : 1); 
				if ($ratio == 0 && $gigs == 0) $wait = 24;
				elseif ($ratio < $RATIOA || $gigs < $GIGSA) $wait = $WAITA;
				elseif ($ratio < $RATIOB || $gigs < $GIGSB) $wait = $WAITB;
				elseif ($ratio < $RATIOC || $gigs < $GIGSC) $wait = $WAITC;
				elseif ($ratio < $RATIOD || $gigs < $GIGSD) $wait = $WAITD;
				else $wait = 0;
			if ($wait)
			if ($elapsed < $wait)
				err("Not authorized (" . ($wait - $elapsed) . "h) - READ THE FAQ! $SITEURL");
			}
	}

}else{// IF WE DO HAVE PEERS DETAILS ($self) THEN WE UPDATE THE UP/DOWN STATS HERE

		//ANTI FLOOD
		$start = $self["ez"];  //last_action
		$end = time();  //now time
		if ($end - $start < 60 && $event != "completed") // Flood time in secs
			err("Sorry, minimum announce interval = 60 sec.");
		//END ANTI FLOOD

		$upthis = max(0, $uploaded - $self["uploaded"]);
		$downthis = max(0, $downloaded - $self["downloaded"]);

		if (($upthis > 0 || $downthis > 0) && is_valid_id($userid)) // SEE IF THERE IS ANYTHING THATS GONE UP (LIVE STATS!)
			{
			mysql_query("UPDATE users SET uploaded = uploaded + $upthis, downloaded = downloaded + $downthis WHERE id=$userid") or err("Tracker error 3");
			}
}//END WAIT AND STATS UPDATE

$updateset = [];

////////////////// NOW WE DO THE TRACKER EVENT UPDATES ///////////////////

if ($event == "stopped")// UPDATE "STOPPED" EVENT
{
	if (isset($self))// DELETE PEER AND REMOVE SEEDER OR LEECHER
	{
		//UPDATE SNATCHED
		mysql_query("UPDATE snatched SET seeder = 'no', connectable='no' WHERE torrent = $torrentid AND userid = $userid");

		mysql_query("DELETE FROM peers WHERE $selfwhere");
		if (mysql_affected_rows())
		{
			if ($self["seeder"] == "yes")
				$updateset[] = "seeders = seeders - 1";
			else
				$updateset[] = "leechers = leechers - 1";
		}
	}
}else{
	if ($event == "completed")// UPDATE "COMPLETED" EVENT
		{
		//UPDATE SNATCHED
		mysql_query("UPDATE snatched SET  finished  = 'yes', completedat = $dt WHERE torrent = $torrentid AND userid = $userid");

		$updateset[] = "times_completed = times_completed + 1";
			// UPDATE THE "WHO COMPLETED TABLE"
				mysql_query("INSERT INTO downloaded (torrent, user) VALUES ('$torrentid', '$userid')") or err(mysql_error());
		}//END COMPLETED

	if (isset($self))// NO EVENT? THEN WE MUST BE A NEW PEER OR ARE NOW SEEDING A COMPLETED TORRENT
	{// NOW WE ARE SEEDING AFTER COMPLETED

//SNATCH UPDATE
	$res=mysql_query("SELECT uploaded, downloaded FROM snatched WHERE torrent = $torrentid AND userid = $userid");
		$row = mysql_fetch_array($res);
		$sockres = @fsockopen($ip, $port, $errno, $errstr, 5);
	  if (!$sockres)
		$connectable = "no";
	  else
	 {
	   $connectable = "yes";
	   @fclose($sockres);
	}
	   $downloaded2=$downloaded - $self["downloaded"];
	   $uploaded2=$uploaded - $self["uploaded"];
		mysql_query("UPDATE snatched SET uploaded = uploaded+$uploaded2, downloaded = downloaded+$downloaded2, port = $port, connectable = '$connectable', agent= " . sqlesc($agent) . ", to_go = $left, last_action = $dt, seeder = '$seeder' WHERE torrent = $torrentid AND userid = $userid");
//END SNATCH UPDATE
		mysql_query("UPDATE peers SET ip = " . sqlesc($ip) . ", port = $port, uploaded = $uploaded, downloaded = $downloaded, to_go = $left, last_action = NOW(), client = " . sqlesc($agent) . ", seeder = '$seeder' WHERE $selfwhere");

		if (mysql_affected_rows() && $self["seeder"] != $seeder)
		{
			if ($seeder == "yes"){
				$updateset[] = "seeders = seeders + 1";
				$updateset[] = "leechers = leechers - 1";
			} else {
				$updateset[] = "seeders = seeders - 1";
				$updateset[] = "leechers = leechers + 1";
			}
		}
	} else {
		if (portblacklisted($port))
			err("Port $port is blacklisted.");	
		else
		{// WE ARE NOT A "COMPLETED" SEED, WE ARE A "NEW" SEEDER
			$sockres = @fsockopen($ip, $port, $errno, $errstr, 5);
			if (!$sockres)
				$connectable = "no";
			else
			{
				$connectable = "yes";
				@fclose($sockres);
			}
		}
			//SNATCHED MOD
			$res = mysql_query("SELECT torrent, userid FROM snatched WHERE torrent = $torrentid AND userid = $userid");
			  $check = mysql_fetch_assoc($res);
		   if (!$check)

			  mysql_query("INSERT INTO snatched (torrent, torrentid, userid, port, startdat, last_action, agent, torrent_name, torrent_category) VALUES ($torrentid, $torrentid, $userid, $port, $dt, $dt, " . sqlesc($agent) . ", " . sqlesc($torrentname) . ", $torrentcategory)");
			  //END SNATCHED

			$ret = mysql_query("INSERT INTO peers (connectable, torrent, peer_id, ip, port, uploaded, downloaded, to_go, started, last_action, seeder, userid, client) VALUES ('$connectable', $torrentid, " . sqlesc($peer_id) . ", " . sqlesc($ip) . ", $port, $uploaded, $downloaded, $left, NOW(), NOW(), '$seeder', '$userid', " . sqlesc($agent) . ")");
			if ($ret)
			{
				if ($seeder == "yes")
					$updateset[] = "seeders = seeders + 1";
				else
					$updateset[] = "leechers = leechers + 1";
			}
		}
}
//////////////////	END TRACKER EVENT UPDATES ///////////////////

// SEEDED, LETS MAKE IT VISIBLE THEN
if ($seeder == "yes") {
	if ($torrent["banned"] != "yes") // DONT MAKE BANNED ONES VISIBLE
		$updateset[] = "visible = 'yes'";
	$updateset[] = "last_action = NOW()";
}

// NOW WE UPDATE THE TORRENT AS PER ABOVE
if (count($updateset))
	mysql_query("UPDATE torrents SET " . join(",", $updateset) . " WHERE id = $torrentid");

// NOW BENC THE DATA AND SEND TO CLIENT???
benc_resp_raw($resp);

exit();

?>
