<?php

require_once("functions.php");


function docleanup() {
	global $torrent_dir, $signup_timeout, $max_dead_torrent_time, $autoclean_interval;

	//set_time_limit(0);  //commented out incase of infinate loop error

	ignore_user_abort(1);

	do {
        // Ratio Warn
GLOBAL $RATIO_WARNINGON,$RATIOWARN_TIME,$RATIOWARN_AMMOUNT,$RATIOWARN_BAN;
if ($RATIO_WARNINGON) {

$query = mysql_query("SELECT id,downloaded,uploaded FROM users WHERE class=0 AND donated=0 AND downloaded>0") or die(mysql_error());
while ($row=mysql_fetch_array($query)) {
    $userid = $row['id'];

        $userratio = number_format($row['uploaded'] / $row['downloaded'], 2);
    
    $warningmsg ="This is a warning. Your ratio is too low and has been low for $RATIOWARN_TIME days. You have $RATIOWARN_BAN days to get your ratio above $RATIOWARN_AMMOUNT or you will be banned!";
    
  if ($userratio <= $RATIOWARN_AMMOUNT){
  
    //CHECK TO SEE IF USER IS ALREADY IN RATIOWARN TABLE
    $check = mysql_query("SELECT * FROM ratiowarn WHERE userid='$userid'");

    //USER IS IN TABLE
    if ((mysql_num_rows($check) != "0")){
        $status = mysql_fetch_assoc($check);
        
        //CHECK WHEN WARNED USER WAS WARNED
        if($status['warned'] == "yes"){
              
            $sql = mysql_query("SELECT warntime,TO_DAYS(NOW()) - TO_DAYS(warntime) as difference FROM ratiowarn WHERE userid='$userid'");
            $warndate = mysql_fetch_assoc($sql);
            
            if ($warndate['difference'] > $RATIOWARN_BAN){ //BAN IF USER HAS BEEN WARNED MORE THAN WARNDAYS
                mysql_query("UPDATE ratiowarn SET banned='yes' WHERE userid='$userid'");
                mysql_query("UPDATE users SET enabled='no' WHERE id='$userid'");
            }

        }else{ //ELSE USER MUST NOW BE WARNED

            $sql = mysql_query("SELECT ratiodate,TO_DAYS(NOW()) - TO_DAYS(ratiodate) as difference FROM ratiowarn WHERE userid='$userid'");
            
            $notificationdate = mysql_fetch_assoc($sql);
            
            if ($notificationdate['difference'] > $RATIOWARN_TIME){//if notification date is > $RATIOWARN_TIME
                        
                mysql_query("UPDATE ratiowarn SET warntime=NOW(),warned='yes' WHERE userid='$userid'");    //add to warned list
                mysql_query("UPDATE users SET warned='yes' WHERE id='$userid'");
                mysql_query("INSERT INTO messages (poster, sender, receiver, added, msg) VALUES ('0', '0', $userid, NOW(), " . sqlesc($warningmsg) . ")");   //send pm

            }
        }

   }else{ //ELSE USER IS IN "NOT" IN TABLE, SO LETS WATCH THEM
        mysql_query("INSERT INTO ratiowarn (userid, ratiodate) VALUES ($userid, NOW())");
   }
  }


    
    //MAKE SURE USER IS NOT IN RATIOWARN TABLE, GOOD RATIO, REMOVE FROM RATIOWARN
    if ($userratio > $RATIOWARN_AMMOUNT && $userratio){
        
        $check = mysql_query("SELECT * FROM ratiowarn WHERE userid='$userid'");
        
        if(mysql_num_rows($check) != "0"){

            mysql_query("DELETE FROM ratiowarn WHERE userid='$userid'");
            mysql_query("UPDATE users SET warned='no',enabled='yes' WHERE id='$userid'");
        }

    }
}
}
// End Ratio Warn
		$res = mysql_query("SELECT id FROM torrents");
		$ar = array();
		while ($row = mysql_fetch_array($res)) {
			$id = $row[0];
			$ar[$id] = 1;
		}

		if (!count($ar))
			break;

		$dp = @opendir($torrent_dir);
		if (!$dp)
			break;

		$ar2 = array();
		while (($file = readdir($dp)) !== false) {
			if (!preg_match('/^(\d+)\.torrent$/', $file, $m))
				continue;
			$id = $m[1];
			$ar2[$id] = 1;
			if (isset($ar[$id]) && $ar[$id])
				continue;
			$ff = $torrent_dir . "/$file";
			unlink($ff);
		}
		closedir($dp);

		if (!count($ar2))
			break;
//DELETE OLD TORRENTS AND CLEAN PEERS TABLE
		$delids = array();
		foreach (array_keys($ar) as $k) {
			if (isset($ar2[$k]) && $ar2[$k])
				continue;
			$delids[] = $k;
			unset($ar[$k]);
		}
		if (count($delids)){
			mysql_query("DELETE FROM torrents WHERE id IN (" . join(",", $delids) . ")");
			mysql_query("DELETE FROM snatched WHERE torrentid IN (" . join(",", $delids) . ")");
		}

		$res = mysql_query("SELECT torrent FROM peers GROUP BY torrent");
		$delids = array();
		while ($row = mysql_fetch_array($res)) {
			$id = $row[0];
			if (isset($ar[$id]) && $ar[$id])
				continue;
			$delids[] = $id;
		}
		if (count($delids))
			mysql_query("DELETE FROM peers WHERE torrent IN (" . join(",", $delids) . ")");

		$res = mysql_query("SELECT torrent FROM files GROUP BY torrent");
		$delids = array();
		while ($row = mysql_fetch_array($res)) {
			$id = $row[0];
			if ($ar[$id])
				continue;
			$delids[] = $id;
		}
		if (count($delids))
			mysql_query("DELETE FROM files WHERE torrent IN (" . join(",", $delids) . ")");
	} while (0);

//CLEANUP TORRENTS THAT ARE OVER THE DEADTIME (new version)
	//$deadtime = deadtime();
	//mysql_query("DELETE FROM peers WHERE last_action < FROM_UNIXTIME($deadtime)");
        $res = mysql_query("DELETE FROM peers WHERE NOW()-last_action > 21600");

//UPDATE SNATCHED TABLE
$deadtime = deadtime();
mysql_query("UPDATE snatched SET seeder='no' WHERE seeder='yes' AND last_action < FROM_UNIXTIME($deadtime)");

//MAKE OLD TORRENTS INVISIBLE
	$deadtime -= $max_dead_torrent_time;
	mysql_query("UPDATE torrents SET visible='no' WHERE visible='yes' AND last_action < FROM_UNIXTIME($deadtime)");

//DELETE PENDING USER ACCOUNTS
	$deadtime = time() - $signup_timeout;
	mysql_query("DELETE FROM users WHERE status = 'pending' AND added < FROM_UNIXTIME($deadtime) AND last_login < FROM_UNIXTIME($deadtime) AND last_access < FROM_UNIXTIME($deadtime) AND last_access != '0000-00-00 00:00:00'");

//OPTIMISE TABLES
	mysql_query("OPTIMIZE TABLE `guests` , `peers` , `torrents` , `files` , `log` , `messages` , `forum_posts` ,`users`,`snatched`  ; ");



//INVITES PART
 	$deadtime = time() - $signup_timeout;
	$user = mysql_query("SELECT invited_by FROM users WHERE status = 'pending' AND added < FROM_UNIXTIME($deadtime) AND last_access = '0000-00-00 00:00:00'");
	$arr = mysql_fetch_assoc($user);
         if (mysql_num_rows($user) > 0)
 	{
         $invites = mysql_query("SELECT invites FROM users WHERE id = $arr[invited_by]");
	$arr2 = mysql_fetch_assoc($invites);
         if ($arr2[invites] < 10)
	{
         $invites = $arr2[invites] +1;
	mysql_query("UPDATE users SET invites='$invites' WHERE id = $arr[invited_by]");
	}
         mysql_query("DELETE FROM users WHERE status = 'pending' AND added < FROM_UNIXTIME($deadtime) AND last_access = '0000-00-00 00:00:00'");
    }

/////////////////////////////////////// cleanup requests //////////////////////////////////////////
   $hours = 720; // Hours to keep no filled requests 720=30days, 30*24=720
   $dt = sqlesc(get_date_time(gmtime() - ($hours * 3600)));
   $res = mysql_query("SELECT id, userid, request, descr, added, hits, cat, filled, filledby FROM requests WHERE added < $dt");
   while ($arr = mysql_fetch_assoc($res))
   {
mysql_query("DELETE FROM requests WHERE id=$arr[id]") or sqlerr(__FILE__, __LINE__);
mysql_query("DELETE FROM addedrequests WHERE id=$arr[id]") or sqlerr(__FILE__, __LINE__);
}
/////////////////////////////////////// cleanup requests //////////////////////////////////
$abc = mysql_query("SELECT id, userid, request, descr, added, hits, cat, filled, filledby FROM requests WHERE filledby > 0");
   while ($arr = mysql_fetch_assoc($abc))
{
$re_msg = "Your request \"$torrent\" was filled by " . $CURUSER["username"] . ".You can download it <a href=".$SITEURL."/torrents-details.php?id=$id&hit=1>HERE</a>";
mysql_query("INSERT INTO messages (poster, sender, receiver, added, msg) VALUES(0, 0, $arr[userid], '" . get_date_time() . "', " . sqlesc($re_msg) . ")") or sqlerr(__FILE__, __LINE__);
mysql_query("DELETE FROM requests WHERE id=$arr[id]") or sqlerr(__FILE__, __LINE__);
mysql_query("DELETE FROM addedrequests WHERE requestid=$arr[id]") or sqlerr(__FILE__, __LINE__);

}
/////////////////////////////////////////////////////////////////////////////////////////////////


//DELETE OLD MESSAGES OVER X DAYS OLD
$secs = 28*86400;// SET THE NUMBER OF DAYS (DEFAULT 28)
$dt = sqlesc(get_date_time(gmtime() - $secs));
mysql_query("DELETE FROM messages WHERE added < $dt");

//START INVITES UPDATE
function autoinvites($length, $minlimit, $maxlimit, $minratio, $invites)
       {
	$time = sqlesc(get_date_time(gmtime() - (($length)*86400)));
 	$minlimit = $minlimit*1024*1024*1024;
	$maxlimit = $maxlimit*1024*1024*1024;
	$res = mysql_query("SELECT id, invites FROM users WHERE class > 0 AND enabled = 'yes' AND downloaded >= $minlimit AND downloaded < $maxlimit AND uploaded / downloaded >= $minratio AND warned = 'no' AND invites < 10 AND invitedate < $time") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0)
    {
        while ($arr = mysql_fetch_assoc($res))
        {
if ($arr[invites] == 9)
$invites = 1;
elseif ($arr[invites] == 8 && $invites == 3)
$invites = 2;
  mysql_query("UPDATE users SET invites = invites+$invites, invitedate = NOW() WHERE id=$arr[id]") or sqlerr(__FILE__, __LINE__);
 }
}
}
//SET INVITE AMOUNTS ACCORDING TO RATIO/GIGS ETC
autoinvites(10,1,4,.90,1);
autoinvites(10,4,7,.95,2);
autoinvites(10,7,10,1.00,3);
autoinvites(10,10,100000,1.05,4);
//END INVITES

//UPDATE TORRENT STATS
	$torrents = array();
	$res = mysql_query("SELECT torrent, seeder, COUNT(*) AS c FROM peers GROUP BY torrent, seeder");
	while ($row = mysql_fetch_assoc($res)) {
		if ($row["seeder"] == "yes")
			$key = "seeders";
		else
			$key = "leechers";
		$torrents[$row["torrent"]][$key] = $row["c"];
	}

	$res = mysql_query("SELECT torrent, COUNT(*) AS c FROM comments GROUP BY torrent");
	while ($row = mysql_fetch_assoc($res)) {
		$torrents[$row["torrent"]]["comments"] = $row["c"];
	}

	$fields = explode(":", "comments:leechers:seeders");
	$res = mysql_query("SELECT id, seeders, leechers, comments FROM torrents");
	while ($row = mysql_fetch_assoc($res)) {
		$id = $row["id"];
		$torr = $torrents[$id];
		foreach ($fields as $field) {
			if (!isset($torr[$field]))
				$torr[$field] = 0;
		}
		$update = array();
		foreach ($fields as $field) {
			if ($torr[$field] != $row[$field])
				$update[] = "$field = " . $torr[$field];
		}
		if (count($update))
			mysql_query("UPDATE torrents SET " . implode(",", $update) . " WHERE id = $id");
	}
	

}

?>
