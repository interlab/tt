<?php

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

function db_run($db, $sql, array $params=[])
{
    try {
        $q = $db->prepare($sql);
        $q->execute($params);
    } catch (PDOException $e) {
        err('DB Error! #' . __LINE__, $e);
    }

    return $q;
}

function is_valid_id($id)
{
  return is_numeric($id) && ($id > 0) && (floor($id) == $id);
}

function validip($ip)
{
	if (!empty($ip) && ip2long($ip)!=-1) {
		$reserved_ips = [
            array('0.0.0.0','2.255.255.255'),
            array('10.0.0.0','10.255.255.255'),
            array('127.0.0.0','127.255.255.255'),
            array('169.254.0.0','169.254.255.255'),
            array('172.16.0.0','172.31.255.255'),
            array('192.0.2.0','192.0.2.255'),
            array('192.168.0.0','192.168.255.255'),
            array('255.255.255.0','255.255.255.255')
		];

		foreach ($reserved_ips as $r) {
            $min = ip2long($r[0]);
            $max = ip2long($r[1]);
            if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
		}
		return true;
	}
	else {
        return false;
    }
}

function getip()
{
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

function dbconn()
{
    global $mysql_host, $mysql_user, $mysql_pass, $mysql_db;

    $db_type = 'mysql';
    $db_server = $mysql_host;
    $db_name = $mysql_db;
    $db_user = $mysql_user;
    $db_passwd = $mysql_pass;

    try {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            PDO::ATTR_EMULATE_PREPARES => 0,
        ];
        $db = new PDO($db_type . ':host=' . $db_server . ';dbname=' . $db_name,
            $db_user, $db_passwd, $options
        );
        unset($options);
    } catch (PDOException $e) {
        err('Houston, we have a problem. #' . __LINE__, $e);
        # die('<br>Error!: ' . $e->getMessage() . '<br>');
    }

    return $db;
}

function hash_pad($hash)
{
    return str_pad($hash, 20);
}

function err($msg)
{
	benc_resp(array("failure reason" => array('type' => "string", 'value' => $msg)));
	exit();
}

function benc($obj)
{
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
	benc_resp_raw(benc(['type' => "dictionary", 'value' => $d]));
}

function benc_resp_raw($x)
{
    header('Content-Type: text/plain');
    header('Pragma: no-cache');

    if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])
        && stristr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')
        && ini_get("zlib.output_compression") == 0
        && ini_get('output_handler') != 'ob_gzhandler'
    ) {
        header("Content-Encoding: gzip");
        echo gzencode( $x, 9, FORCE_GZIP );
    } else {
        echo $x;
    }
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
	return ($port >= 411 && $port <= 413)

	// bittorrent (AZUREUS)
	|| ($port >= 6881 && $port <= 6889)

	// kazaa
	|| ($port == 1214)

	// gnutella
	|| ($port >= 6346 && $port <= 6347)

	// emule
	|| ($port == 4662)

	// winmx
	|| ($port == 6699);
}

//////////////////////// NOW WE DO THE ANNOUNCE CODE ////////////////////////

// BLOCK ACCESS WITH WEB BROWSERS
$agent = $_SERVER["HTTP_USER_AGENT"];

if (isset($_SERVER['HTTP_COOKIE'], $_SERVER['HTTP_ACCEPT_LANGUAGE'], $_SERVER['HTTP_ACCEPT_CHARSET'])
    || preg_match('~firefox|msie|opera|chrome|safari|mozilla|seamonkey|konqueror|netscape|gecko|navigator|mosaic|' .
               'links|lynx|amaya|omniweb|avant|camino|flock|aol~i', $agent)) {
    header("HTTP/1.0 500 Bad Request");
    die('Browser Not Allowed');
}

// GET DETAILS OF PEERS ANNOUNCE

# Invalid information received from BitTorrent client
foreach (['info_hash', 'peer_id', 'port', 'downloaded', 'uploaded', 'left'] as $x)  {
    if (!isset($_GET[$x]))
        err('Missing key: ' . $x);
}

foreach (['info_hash', 'peer_id'] as $x)
    if (strlen($_GET[$x]) != 20)
        err('Invalid ' . $x . ' (' . strlen($_GET[$x]) . ' - ' . urlencode($_GET[$x]) . ')');

foreach (['info_hash', 'peer_id', 'port', 'event', 'ip', 'localip'] as $x)
    if (isset($_GET[$x]))
        $GLOBALS[$x] = $_GET[$x];

if (empty($ip) || !validip($ip))
	$ip = getip();

$port = (int) ($_GET['port'] ?? 0);
$downloaded = (int) ($_GET['downloaded'] ?? 0);
$uploaded = (int) ($_GET['uploaded'] ?? 0);
$left = (int) ($_GET['left'] ?? 0);

$event = $_GET['event'] ?? '';
$is_ban = 'yes';
$yes = 'yes';
$no = 'no';
$connectable_check = false;
$now = date('Y-m-d H:i:s');

$rsize = 50;
foreach(['num want', 'numwant', 'num_want'] as $k) {
	if (isset($_GET[$k])) {
		$rsize = (int) $_GET[$k];
		break;
	}
}

// PORT CHECK
if (!$port || $port > 0xffff)
	err("invalid port");

$seeder = ($left == 0) ? "yes" : "no";

$db = dbconn();

// GET HASH AND SELECT FROM DB
$usehash = false;
if (isset($_GET["info_hash"])) {
	$info_hash = $_GET["info_hash"];
	if (strlen($info_hash) == 20)
		$info_hash = bin2hex($info_hash);
	elseif (strlen($info_hash) != 40)
		err("Invalid info hash value.");
	$info_hash = strtolower($info_hash);
	$usehash = true;
}

// todo: if bad check and free - skip passkey check

// DOES THE TORRENT EXIST?
$q = db_run($db, '
    SELECT
        id, name, category, banned, seeders, leechers,
        seeders + leechers AS numpeers, UNIX_TIMESTAMP(added) AS ts, times_completed
    FROM torrents
    WHERE info_hash = ?
    LIMIT 1',
    [$info_hash]
);
$torrent = $q->fetch();
$q->closeCursor();
if (! $torrent) {
    err("torrent not found on this tracker - hash = " . $info_hash);
}
$torrent = array_map('intval', $torrent);
if ($torrent['banned'] == $is_ban) {
    err('Torrent is banned!');
}


// IS THE IP REGISTERED, IF SO CALL USER ID
$userid = 0;
if ($MEMBERSONLY) {
    $_GET['passkey'] = $_GET['passkey'] ?? '';
    $passkey = $_GET['passkey'] ? '' : pack('H*', $_GET['passkey']);
    if (! $passkey) {
        err("Bad passkey. Please go to $SITEURL to sign-up or login.");
    }

    $q = db_run($db, '
        SELECT id, uploaded, downloaded, class, enabled
        FROM users
        WHERE passkey = ?
        LIMIT 1',
        [$passkey]
    );
    $azz = $q->fetch();
    $q->closeCursor();

    if (! $azz) {
        err('Permission denied, user not found by passkey');
    }

    if ($azz['enabled'] == $no) {
        err('Permission denied, you\'re not enabled');
    }

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

# Порт открыт?
if ('started' == $event && $connectable_check) {
    if (portblacklisted($port)) {
		err("Port $port is blacklisted.");
    }
    $sockres = @fsockopen($ip, $port, $errno, $errstr, 5);
    if (!$sockres) {
        $connectable = $no;
    } else {
        $connectable = $yes;
        @fclose($sockres);
    }
} else {
    $connectable = $yes;
}


$q = db_run($db, '
    SELECT ' . $fields . '
    FROM peers
    WHERE torrent = ' . $torrentid . ($connectable == $yes ? '' : '
        AND connectable = \'yes\''). '
    '. $limit);

// DO SOME BENC STUFF TO THE PEERS CONNECTION
$resp = "d" . benc_str("interval") . "i" . $announce_interval . "e" . benc_str("peers") . "l";

while ($row = $q->fetch()) {
	$row["peer_id"] = hash_pad($row["peer_id"]);

	if ($row["peer_id"] === $peer_id) {
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
$q->closeCursor();

// FILL $SELF WITH DETAILS FROM PEERS TABLE (CONNECTING PEERS DETAILS)
if (! isset($self)) {
    $q = db_run($db, 'SELECT '.$fields .' FROM peers WHERE torrent = ? AND peer_id = ?',
        [$torrentid, $peer_id]
    );
    $row = $q->fetch();
    $q->closeCursor();
	if ($row) {
		$userid = $row["userid"];
		$self = $row;
	}
}
// END $SELF FILL

// SNATCHED MOD - GET DATE TIME/OFFSET
$dt = gmtime() - 180; // OFFSET
$dt = get_date_time($dt);

// IF PEER IS NOT IN PEERS TABLE DO THE WAIT TIME CHECK
if (! isset($self)) {
	if ($MEMBERSONLY_WAIT && $MEMBERSONLY) {
		if ($left > 0 && $azz["class"] == 0 ) {
            $gigs = $azz["uploaded"] / (1024*1024*1024);
            $elapsed = floor((gmtime() - $torrent["ts"]) / 3600);
            $ratio = (($azz["downloaded"] > 0) ? ($azz["uploaded"] / $azz["downloaded"]) : 1); 
            if ($ratio == 0 && $gigs == 0) $wait = 24;
            elseif ($ratio < $RATIOA || $gigs < $GIGSA) $wait = $WAITA;
            elseif ($ratio < $RATIOB || $gigs < $GIGSB) $wait = $WAITB;
            elseif ($ratio < $RATIOC || $gigs < $GIGSC) $wait = $WAITC;
            elseif ($ratio < $RATIOD || $gigs < $GIGSD) $wait = $WAITD;
            else $wait = 0;
            if ($wait) {
                if ($elapsed < $wait) {
                    err("Not authorized (" . ($wait - $elapsed) . "h) - READ THE FAQ! $SITEURL");
                }
            }
        }
    }
} else {
    // IF WE DO HAVE PEERS DETAILS ($self) THEN WE UPDATE THE UP/DOWN STATS HERE
    // ANTI FLOOD
    $start = $self["ez"];  //last_action
    $end = time();  //now time
    if ($end - $start < 60 && $event != "completed") {
        // Flood time in secs
        err("Sorry, minimum announce interval = 60 sec.");
    }
    // END ANTI FLOOD

    $upthis = max(0, $uploaded - $self["uploaded"]);
    $downthis = max(0, $downloaded - $self["downloaded"]);

    // SEE IF THERE IS ANYTHING THATS GONE UP (LIVE STATS!)
    if (($upthis > 0 || $downthis > 0) && is_valid_id($userid)) {
        $q = db_run($db, 'UPDATE users SET uploaded = uploaded + ?, downloaded = downloaded + ? WHERE id = ?',
            [$upthis, $downthis, $userid]
        );
    }
}
// END WAIT AND STATS UPDATE

$updateset = [];

////////////////// NOW WE DO THE TRACKER EVENT UPDATES ///////////////////

// UPDATE "STOPPED" EVENT
if ($event == "stopped") {
    // DELETE PEER AND REMOVE SEEDER OR LEECHER
	if (isset($self)) {
		// UPDATE SNATCHED
        $q = db_run(
            $db,
            'UPDATE snatched SET seeder = ?, connectable = ? WHERE torrent = ? AND userid = ?',
            ['no', 'no', $torrentid, $userid]
        );

        $q = db_run($db, 'DELETE FROM peers WHERE torrent = ? AND peer_id = ?',
            [$torrentid, $peer_id]
        );

		if ($q->rowCount()) {
			if ($self["seeder"] == "yes")
				$updateset[] = "seeders = seeders - 1";
			else
				$updateset[] = "leechers = leechers - 1";
		}
	}
} else {
    // UPDATE "COMPLETED" EVENT
	if ($event == "completed") {
		// UPDATE SNATCHED
		db_run($db, 'UPDATE snatched SET finished = ?, completedat = ? WHERE torrent = ? AND userid = ?',
            ['yes', $dt, $torrentid, $userid]
        );

		$updateset[] = "times_completed = times_completed + 1";
		// UPDATE THE "WHO COMPLETED TABLE"
		db_run($db, 'INSERT INTO downloaded (torrent, user, added) VALUES (?, ?, ?)',
            [$torrentid, $userid, $now]
        );
	}
    // END COMPLETED EVENT

    // NO EVENT? THEN WE MUST BE A NEW PEER OR ARE NOW SEEDING A COMPLETED TORRENT
	if (isset($self)) {
        // NOW WE ARE SEEDING AFTER COMPLETED

        // SNATCH UPDATE
	    $downloaded2 = $downloaded - $self["downloaded"];
	    $uploaded2 = $uploaded - $self["uploaded"];
        db_run($db, '
            UPDATE snatched
            SET uploaded = uploaded + ?, downloaded = downloaded + ?, port = ?,
                connectable = ?, agent = ?, to_go = ?,
                last_action = ?, seeder = ?
            WHERE torrent = ? AND userid = ?',
            [$uploaded2, $downloaded2, $port, $connectable, $agent, $left, $dt, $seeder, $torrentid, $userid]
        );
        // END SNATCH UPDATE
		$q = db_run($db,
            'UPDATE peers SET ip = ?, port = ?, uploaded = ?, downloaded = ?,
                to_go = ?, last_action = ?, client = ?, seeder = ? WHERE torrent = ? AND peer_id = ?',
            [$ip, $port, $uploaded, $downloaded, $left, date('Y-m-d H:i:s'), $agent, $seeder, $torrentid, $peer_id]
        );

		if ($q->rowCount() && $self["seeder"] != $seeder) {
			if ($seeder == "yes") {
				$updateset[] = "seeders = seeders + 1";
				$updateset[] = "leechers = leechers - 1";
			} else {
				$updateset[] = "seeders = seeders - 1";
				$updateset[] = "leechers = leechers + 1";
			}
		}
	} else {
		// SNATCHED MOD
		$res = db_run($db, 'SELECT torrent, userid FROM snatched WHERE torrent = ? AND userid = ?',
            [$torrentid, $userid]
        );
		$check = $res->fetch();
		if (! $check) {
			db_run($db,
                'INSERT INTO snatched (torrent, torrentid, userid, port, startdat,
                                        last_action, agent, torrent_name, torrent_category)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [$torrentid, $torrentid, $userid, $port, $dt, $dt, $agent, $torrentname, $torrentcategory]
            );
        }
		// END SNATCHED

		$ret = db_run($db,
            'INSERT INTO peers (connectable, torrent, peer_id, ip, port, uploaded, downloaded,
                                to_go, started, last_action, seeder, userid, client)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [$connectable, $torrentid, $peer_id, $ip, $port, $uploaded, $downloaded,
                $left, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $seeder, $userid, $agent]
        );
		if ($ret->lastInsertId()) {
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
	db_run($db, "UPDATE torrents SET " . join(",", $updateset) . " WHERE id = $torrentid");

// NOW BENC THE DATA AND SEND TO CLIENT???
benc_resp_raw($resp);

exit();


