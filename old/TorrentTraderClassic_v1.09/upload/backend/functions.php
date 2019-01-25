<?php

// error_reporting(E_ALL ^ E_NOTICE);
error_reporting(-1);
// error_reporting(E_ALL ^ E_NOTICE);

define('ST_START_TIME', microtime(true));

mb_internal_encoding('UTF-8');
setlocale(LC_ALL, 'ru_RU.UTF-8');
header("Content-Type: text/html; charset=UTF-8");
set_time_limit(30);
ignore_user_abort(true);
date_default_timezone_set('Europe/Kiev');

if (PHP_MAJOR_VERSION < 7) {
    die ('Your version not support. You will should be used php >= 7');
}

if (version_compare(PHP_VERSION, '7.3.0', '>')) {
    die('PHP 7.3 not support.');
}

$GLOBALS['ttversion'] = '1.09';

function local_user()
{
  global $HTTP_SERVER_VARS;

  return $HTTP_SERVER_VARS["SERVER_ADDR"] == $HTTP_SERVER_VARS["REMOTE_ADDR"];
}

require_once("config.php");
require_once("cleanup.php");
require_once("extras.php");

define('ST_ROOT_DIR', dirname(__DIR__));
require_once __DIR__ . '/constants.php';
require_once ST_ROOT_DIR . '/helpers/DB.php';
require_once ST_ROOT_DIR . '/helpers/Yaml.php';
require_once ST_ROOT_DIR . '/helpers/Helper.php';
require_once ST_ROOT_DIR . '/libs/vendor/autoload.php';


//temp place for invites variables
$invite_timeout = 86400 * 3;
$invites = 3000;

// PHP5 with register_long_arrays off?
if (!isset($HTTP_POST_VARS) && isset($_POST))
{
    $HTTP_POST_VARS = $_POST;
    $HTTP_GET_VARS = $_GET;
    $HTTP_SERVER_VARS = $_SERVER;
    $HTTP_COOKIE_VARS = $_COOKIE;
    $HTTP_ENV_VARS = $_ENV;
    $HTTP_POST_FILES = $_FILES;
}

function h($str)
{
    return htmlspecialchars($str, ENT_COMPAT, 'utf-8', false);
}

function getmicrotime()
{
    [$usec, $sec] = explode(' ', microtime());
    return ((float)$usec + (float)$sec);
}

// IP Validation
function validip($ip)
{
	if (!empty($ip) && $ip == long2ip(ip2long($ip))) {
		// reserved IANA IPv4 addresses
		// http://www.iana.org/assignments/ipv4-address-space
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
	else return false;
}

// Patched function to detect REAL IP address if it's valid
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

function dbconn($autoclean = false)
{
    global $mysql_host, $mysql_user, $mysql_pass, $mysql_db;

    my_pdo_connect($mysql_db, $mysql_user, $mysql_pass, $mysql_host);

    userlogin();

    // todo
    // if ($autoclean) {
        // register_shutdown_function("autoclean");
    // }
}

function userlogin()
{
    global $GLOBALBAN, $HTTP_SERVER_VARS, $SITE_ONLINE;

    unset($GLOBALS['CURUSER']);

    $ip = getip();
    $nip = ip2long($ip);
    $row = DB::fetchAssoc('
        SELECT *
        FROM bans
        WHERE ' . $nip . ' >= first
            AND ' . $nip . ' <= last
    ');
    if ($row) {
        header("HTTP/1.0 403 Forbidden");
        echo '
    <html>
    <head>
    <title>Forbidden</title>
    </head>
    <body>
    <h1>Forbidden</h1>Unauthorized IP address.
    <br />
    Reason for banning: ', $row['comment'], '
    </body>
    </html>';
        die;
    }

    if (empty($_COOKIE["uid"]) || empty($_COOKIE["pass"])) {
        return;
    }
    $id = (int) $_COOKIE["uid"];
    if (!$id || strlen($_COOKIE["pass"]) != 32) {
        return;
    }
    $row = DB::fetchAssoc("
    SELECT *
    FROM users
    WHERE id = $id
        AND enabled = 'yes'
        AND status = 'confirmed'");
    if (!$row) {
        return;
    }
    $sec = hash_pad($row["secret"]);
    if ($_COOKIE["pass"] != md5($sec.$row["password"].$sec)) {
        return;
    }

    DB::update('users', [
            'last_access' => get_date_time(),
            'ip' => $ip,
            // 'showext' => $showext,
            // 'url' => getenv("REQUEST_URI"),
            // 'useragent' => $_SERVER["HTTP_USER_AGENT"],
        ],
        [ 'id' => $row["id"] ]
    );

    $row['ip'] = $ip;
    $GLOBALS["CURUSER"] = $row;
}

function autoclean() {
    global $autoclean_interval;

    $now = time();
    $docleanup = 0;

    $res = mysql_query("SELECT value_u FROM avps WHERE arg = 'lastcleantime'");
    $row = mysql_fetch_array($res);
    if (!$row) {
        mysql_query("INSERT INTO avps (arg, value_u) VALUES ('lastcleantime',$now)");
        return;
    }
    $ts = $row[0];
    if ($ts + $autoclean_interval > $now)
        return;
    mysql_query("UPDATE avps SET value_u=$now WHERE arg='lastcleantime' AND value_u = $ts");
    if (!mysql_affected_rows())
        return;

    docleanup();
}

function unesc($x) {
    if (get_magic_quotes_gpc())
        return stripslashes($x);
    return $x;
}

function mksize($bytes) {
  if ($bytes < 1000 * 1024)
    return number_format($bytes / 1024, 2) . " KB";
  if ($bytes < 1000 * 1048576)
    return number_format($bytes / 1048576, 2) . " MB";
  if ($bytes < 1000 * 1073741824)
	return number_format($bytes / 1073741824, 2) . " GB";
  return number_format($bytes / 1099511627776, 2) . " TB";
}

function mksizekb($bytes)
{
  return number_format($bytes / 1024) . " KiB";
}

function mksizemb($bytes)
{
  return number_format($bytes / 1048576,2) . " MiB";
}

function mksizegb($bytes)
{
  return number_format($bytes / 1073741824,2) . " GiB";
}

function deadtime() {
    global $announce_interval;
    return time() - floor($announce_interval * 1.3);
}

function mkprettytime($s) {
    if ($s < 0)
        $s = 0;
    $t = [];
    foreach (array("60:sec","60:min","24:hour","0:day") as $x) {
        $y = explode(":", $x);
        if ($y[0] > 1) {
            $v = $s % $y[0];
            $s = floor($s / $y[0]);
        }
        else
            $v = $s;
        $t[$y[1]] = $v;
    }

    if ($t["day"])
        return $t["day"] . "d " . sprintf("%02d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
    if ($t["hour"])
        return sprintf("%d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
//    if ($t["min"])
        return sprintf("%d:%02d", $t["min"], $t["sec"]);
//    return $t["sec"] . " secs";
}

function mkglobal($vars) {
    if (!is_array($vars))
        $vars = explode(":", $vars);
    foreach ($vars as $v) {
        if (isset($_GET[$v]))
            $GLOBALS[$v] = unesc($_GET[$v]);
        elseif (isset($_POST[$v]))
            $GLOBALS[$v] = unesc($_POST[$v]);
        else
            return 0;
    }
    return 1;
}

function tr($x,$y,$noesc=0) {
    if ($noesc)
        $a = $y;
    else {
        $a = h($y);
        $a = str_replace("\n", "<br />\n", $a);
    }
    print("<tr><td class=\"heading\" valign=\"top\" align=\"right\">$x</td><td valign=\"top\" align=left>$a</td></tr>\n");
}

function validfilename($name) {
    return preg_match('/^[^\0-\x1f:\\\\\/?*\xff#<>|]+$/si', $name);
}

function validemail($email) {
    return preg_match('/^[\w.-]+@([\w.-]+\.)+[a-z]{2,6}$/is', $email);
}

//secure vars
function sqlesc($x) {
	$x = str_replace("'", "'", $x);
    $x = str_replace("--", "--", $x);
    $x = str_replace("UPDATE", "", $x);
    $x = str_replace("DELETE", "", $x);
    $x = str_replace("DROP", "", $x);
    $x = str_replace("INSERT", "", $x);
    $x = str_replace("$mysql_", "", $x);
    $x = str_replace("java script:", "", $x);

   if (get_magic_quotes_gpc()) {
       $x = stripslashes($x);
   }
   if (!is_numeric($x)) {
       $x = "'".mysql_real_escape_string($x)."'";
   }
   return $x;
}

function sqlwildcardesc($x) {
    return str_replace(array("%","_"), array("\\%","\\_"), mysql_real_escape_string($x));
}

function urlparse($m) {
    $t = $m[0];
    if (preg_match(',^\w+://,', $t))
        return "<a href=\"$t\">$t</a>";
    return "<a href=\"http://$t\">$t</a>";
}

function parsedescr($d, $html) {
    if (!$html)
    {
      $d = h($d);
      $d = str_replace("\n", "\n<br />", $d);
    }
    return $d;
}

function genbark($x,$y) {
    stdhead($y);
    begin_frame("<font color=red>Error - ". h($y) ."</font>", 'center');
    print("<p>" . h($x) . "</p>\n");
    end_frame();
    stdfoot();
    exit();
}

function mksecret($len = 20) {
    $ret = "";
    for ($i = 0; $i < $len; $i++)
        $ret .= chr(mt_rand(0, 255));
    return $ret;
}

function httperr($code = 404) {
    header("HTTP/1.0 404 Not found");
    print("<h1>Not Found</h1>\n");
    print("<p>Sorry pal :(</p>\n");
    exit();
}

function gmtime()
{
    return strtotime(get_date_time());
}

// @todo: md5 not recommended
function logincookie($id, $password, $secret, $updatedb = 1, $expires = 0x7fffffff)
{
    $md5 = md5($secret.$password.$secret);
    setcookie('uid', $id, $expires, '/');
    setcookie('pass', $md5, $expires, '/');

    if ($updatedb) {
        DB::query('
    UPDATE users
        SET last_login = NOW()
    WHERE id = ' . $id);
    }
}

function logoutcookie()
{
    setcookie('uid', false, time(), '/');
    setcookie('pass', false, time(), '/');
}

function loggedinorreturn()
{
    global $CURUSER;

    if (!$CURUSER) {
        header("Refresh: 0; url=account-login.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]));
        exit();
    }
}

function isNotGuest()
{
    global $CURUSER, $SITEURL;

    // print_r($CURUSER);
    // die('LOL');

    if (!$CURUSER) {
        header('Location: '.$SITEURL.'/account-login.php?returnto='.urlencode($_SERVER['REQUEST_URI']));
        die();
    }
}

function adminonly() {
    global $CURUSER;
    if (get_user_class() < UC_ADMINISTRATOR) {
        stderr("ACCESS DENIED", "Sorry this page is only for Administrators.");
        exit();
    }
}

function modonly() {
    global $CURUSER;
    if (get_user_class() < UC_MODERATOR) {
        stderr("ACCESS DENIED", "Sorry this page is only for Super Moderators.");
        exit();
    }
}

function jmodonly()
{
    global $CURUSER;

    if (get_user_class() < UC_JMODERATOR) {
        stderr("ACCESS DENIED", "Sorry this page is only for Moderators.");
        exit();
    }
}

function deletetorrent($id) {
    global $torrent_dir;
    mysql_query("DELETE FROM torrents WHERE id = $id");
	mysql_query("DELETE FROM snatched WHERE torrentid = $id");
    foreach(explode(".","peers.files.comments.ratings") as $x)
        mysql_query("DELETE FROM $x WHERE torrent = $id");
    unlink("$torrent_dir/$id.torrent");
	@unlink("$nfo_dir/$id.nfo");
}

function pager($rpp, $count, $href, $opts = []) {
    $pages = ceil($count / $rpp);

    if (!$opts["lastpagedefault"])
        $pagedefault = 0;
    else {
        $pagedefault = floor(($count - 1) / $rpp);
        if ($pagedefault < 0)
            $pagedefault = 0;
    }

    if (isset($_GET["page"])) {
        $page = 0 + $_GET["page"];
        if ($page < 0)
            $page = $pagedefault;
    }
    else
        $page = $pagedefault;

    $pager = "";

    $mp = $pages - 1;
    $as = "<b>&lt;&lt;&nbsp;Prev</b>";
    if ($page >= 1) {
        $pager .= "<a href=\"{$href}page=" . ($page - 1) . "\">";
        $pager .= $as;
        $pager .= "</a>";
    }
    else
        $pager .= $as;
    $pager .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    $as = "<b>Next&nbsp;&gt;&gt;</b>";
    if ($page < $mp && $mp >= 0) {
        $pager .= "<a href=\"{$href}page=" . ($page + 1) . "\">";
        $pager .= $as;
        $pager .= "</a>";
    }
    else
        $pager .= $as;

    if ($count) {
        $pagerarr = [];
        $dotted = 0;
        $dotspace = 3;
        $dotend = $pages - $dotspace;
        $curdotend = $page - $dotspace;
        $curdotstart = $page + $dotspace;
        for ($i = 0; $i < $pages; $i++) {
            if (($i >= $dotspace && $i <= $curdotend) || ($i >= $curdotstart && $i < $dotend)) {
                if (!$dotted)
                    $pagerarr[] = "...";
                $dotted = 1;
                continue;
            }
            $dotted = 0;
            $start = $i * $rpp + 1;
            $end = $start + $rpp - 1;
            if ($end > $count)
                $end = $count;
            $text = "$start&nbsp;-&nbsp;$end";
            if ($i != $page)
                $pagerarr[] = "<a href=\"{$href}page=$i\"><b>$text</b></a>";
            else
                $pagerarr[] = "<b>$text</b>";
        }
        $pagerstr = join(" | ", $pagerarr);
        $pagertop = "<p align=\"center\">$pager<br />$pagerstr</p>\n";
        $pagerbottom = "<p align=\"center\">$pagerstr<br />$pager</p>\n";
    }
    else {
        $pagertop = "<p align=\"center\">$pager</p>\n";
        $pagerbottom = $pagertop;
    }

    $start = $page * $rpp;

    return array($pagertop, $pagerbottom, "LIMIT $start,$rpp");
}

function downloaderdata($res) {
    $rows = [];
    $ids = [];
    $peerdata = [];
    while ($row = mysql_fetch_assoc($res)) {
        $rows[] = $row;
        $id = $row["id"];
        $ids[] = $id;
        $peerdata[$id] = array(downloaders => 0, seeders => 0, comments => 0);
    }

    if (count($ids)) {
        $allids = implode(",", $ids);
        $res = mysql_query("SELECT COUNT(*) AS c, torrent, seeder FROM peers WHERE torrent IN ($allids) GROUP BY torrent, seeder");
        while ($row = mysql_fetch_assoc($res)) {
            if ($row["seeder"] == "yes")
                $key = "seeders";
            else
                $key = "downloaders";
            $peerdata[$row["torrent"]][$key] = $row["c"];
        }
        $res = mysql_query("SELECT COUNT(*) AS c, torrent FROM comments WHERE torrent IN ($allids) GROUP BY torrent");
        while ($row = mysql_fetch_assoc($res)) {
            $peerdata[$row["torrent"]]["comments"] = $row["c"];
        }
    }

    return array($rows, $peerdata);
}

function commenttable($rows) {
    begin_frame();
    $count = 0;
    foreach ($rows as $row)
    {
print("<br>\n");
	$postername = h($row["username"]);
 if ($postername == "") {
        $postername = "Deluser";
		$title = "Deleted Account";
		$privacylevel = "strong";
        $avatar = "";
		$usersignature = "";
		$userdownloaded = "";
	   $useruploaded = "";
      }else {
	$res4 = mysql_query("SELECT COUNT(*) FROM forum_posts WHERE userid=" . $row["user"] . "") or sqlerr();
	$arr33 = mysql_fetch_row($res4);
	$forumposts = $arr33[0];

	$res44 = mysql_query("SELECT COUNT(*) FROM comments WHERE user=" . $row["user"] . "") or sqlerr();
	$arr333 = mysql_fetch_row($res44);
	$commentposts = $arr333[0];
	$commentid = $row["id"];



      $avatar = h($row["avatar"]);
	  $userdownloaded = mksize($row["downloaded"]);
	  $useruploaded = mksize($row["uploaded"]);
	  $title =  h($row["title"]);
	  $privacylevel = $row["privacy"];
	  $usersignature = stripslashes(format_comment($row["signature"]));
}
if ($row["downloaded"] > 0)
    {
      $userratio = number_format($row["uploaded"] / $row["downloaded"], 2);
    }
else
      if ($row["uploaded"] > 0)
        $userratio = "Inf.";
      else
        $userratio = "---";

      if (!$avatar)
        $avatar = "images/default_avatar.gif";
      begin_table(true);
        print("<tr valign=top>\n");

			if (get_user_class() >= UC_JMODERATOR){
				print("<td></td><TD>Posted: " . $row["added"] . " - <a href=edit-comments.php?cid=" . $row["id"] . ">[Edit]</a>- <a href=edit-comments.php?action=delete&cid=" . $row["id"] . ">[Delete]</a></td></tr><tr valign=top>\n");
			}else{
				print("<td></td><TD>Posted: " . $row["added"] . "</td></tr><tr valign=top>\n");
			}

	if ($privacylevel == "strong"){
			if (get_user_class() >= UC_JMODERATOR){
				print("<td valign=top width=150 align=left><center><b>$postername</b><br><i>$title</i></center><br><font color=green>Uploaded: $useruploaded<br>Downloaded: $userdownloaded</font><br>Forum Posts: $forumposts<br>Comments Posted: $commentposts<br><font color=green>Ratio: $userratio</font><br><br><center><img width=80 height=80 src=$avatar></center><br></td>\n");
			}else{
				print("<td valign=top width=150 align=left><center><b>$postername</b><br><i>$title</i></center><br>Forum Posts: $forumposts<br>Comments Posted: $commentposts<br><br><center><img width=80 height=80 src=$avatar></center><br></td>\n");
			}
		}else{
		 print("<td valign=top width=150 align=left><center><b>$postername</b><br><i>$title</i></center><br>Uploaded: $useruploaded<br>Downloaded: $userdownloaded<br>Forum Posts: $forumposts<br>Comments Posted: $commentposts<br>Ratio: $userratio<br><br><center><img width=80 height=80 src=$avatar></center><br></td>\n");
	}

        print("<td class=text>" . format_comment($row["text"]) . "<br><br>");
      if (!$usersignature){
		print("<br></td>\n");
	  }else{
		print("<br>--------------------<br>$usersignature</td>\n");
	  }
        print("</tr>\n");
      end_table();
    }
    end_frame();
}

function searchfield($s) {
    return preg_replace(array('/[^a-z0-9]/si', '/^\s*/s', '/\s*$/s', '/\s+/s'), array(" ", "", "", " "), $s);
}

function genrelist()
{
    return DB::fetchAll('SELECT id, name FROM categories ORDER BY sort_index, id');
}

function linkcolor($num) {
    if (!$num)
        return "red";
    if ($num == 1)
        return "yellow";
    return "green";
}

function ratingpic($num) {
    $r = round($num * 2) / 2;
    if ($r < 1 || $r > 5)
        return;
    return "<img src=\"images/$r.gif\" border=\"0\" alt=\"rating: $num / 5\" />";
}

function stdhead($title = "", $msgalert = true)
{
    global $CURUSER, $HTTP_SERVER_VARS, $PHP_SELF, $SITE_ONLINE, $FORUMS, $IRCCHAT;
    global $FUNDS, $OFFLINEMSG, $SITENAME, $GLOBALBANS, $POLLSON, $theme, $REMOVALSON;
    global $NEWSON, $USENET, $DONATEON, $DISCLAIMERON, $absolute_path, $sizebytes, $sizelimit;
    global $limitedext, $extlimit, $INVITEONLY, $SITEURL;

    header("Content-Type: text/html; charset=UTF-8");

    loadLanguage();
    global $txt;
    // dump($GLOBALS);

    if (!$SITE_ONLINE) {
        if (get_user_class() != UC_ADMINISTRATOR) {
            echo '<BR><BR><BR><CENTER>'.stripslashes($OFFLINEMSG).'</CENTER><BR><BR>';
            die;
        } else {
            echo '<BR><BR><BR><CENTER><B>
            <FONT COLOR=RED>SITE OFFLINE, ADMIN ONLY VIEWING! DO NOT LOGOUT</FONT></B>
            <BR>If you logout please edit backend/config.php and set $SITE_ONLINE to true </CENTER><BR><BR>';
        }
    }

    if (!$CURUSER) {
        guestadd();
    }

    if ($title == '') {
        $title = $SITENAME;
    } else {
        $title = $SITENAME . ' :: ' . h($title);
    }

    $ss_uri = getThemeUri();

    $GLOBALS['ss_uri'] = $ss_uri;
    $GLOBALS['SITEURL'] = $SITEURL;

    require_once ST_ROOT_DIR.'/themes/'.$ss_uri.'/block.php'; //add theme blocks modification
    require_once ST_ROOT_DIR.'/themes/'.$ss_uri.'/header.php';
}

function getThemeUri()
{
    global $CURUSER, $st;

    if (isset($st['cache']['styleUri'])) {
        return $st['cache']['styleUri'];
    }

    $res = DB::fetchAssoc('
        SELECT uri
        FROM stylesheets
        WHERE id = :id
        LIMIT 1',
        [ 'id' => $CURUSER ? $CURUSER['stylesheet'] : 1 ]
    );

    $uri = $res['uri'] ?? 'default';

    $st['cache']['styleUri'] = $uri;

    return $uri;
}

function loadLanguage()
{
    global $CURUSER, $txt;
    static $st_load_lang = null;

    if (! is_null($st_load_lang)) {
        return;
    }

    if ($CURUSER) {
        $lang_uri = DB::fetchColumn("
    SELECT uri
    FROM languages
    WHERE id = ".$CURUSER["language"]);
    }

    if (! isset($lang_uri)) {
        $lang_uri = DB::fetchColumn('
    SELECT uri
    FROM languages
    WHERE id = 1');
    }

    // dump($lang_uri, ST_ROOT_DIR . '/languages/' . $lang_uri);
    
    $GLOBALS['txt'] = st_parse_yaml(ST_ROOT_DIR . '/languages/' . $lang_uri);
    // $GLOBALS['txt'] = st_parse_yaml(ST_ROOT_DIR . '/languages/russian.yml');

    // dump($GLOBALS['txt']);
    // die;
    
    $st_load_lang = true;
}

function updateUserLastBrowse()
{
    global $CURUSER;

    if ($CURUSER) {
        DB::update('users', ['last_browse' => gmtime()], ['id' => $CURUSER['id']]);
    }
}

function stdfoot()
{
  require_once ST_THEMES_DIR . '/' . $GLOBALS['ss_uri'] . '/footer.php';
}

function get_percent_completed_image($p) {
 $maxpx = "30"; // Maximum amount of pixels for the progress bar

 if ($p == 0) $progress = "<img src=\"" . $GLOBALS['SITEURL'] . "/images/progbar-rest.gif\" height=9 width=" . ($maxpx) . " />";
 if ($p >= 100) $progress = "<img src=\"" . $GLOBALS['SITEURL'] . "/images/progbar-green.gif\" height=9 width=" . ($maxpx) . " />";
 if ($p >= 1 && $p <= 30) $progress = "<img src=\"" . $GLOBALS['SITEURL'] . "/images/progbar-red.gif\" height=9 width=" . ($p*($maxpx/100)) . " /><img src=\"" . $GLOBALS['SITEURL'] . "/images/progbar-rest.gif\" height=9 width=" . ((100-$p)*($maxpx/100)) . " />";
 if ($p >= 31 && $p <= 65) $progress = "<img src=\"" . $GLOBALS['SITEURL'] . "/images/progbar-yellow.gif\" height=9 width=" . ($p*($maxpx/100)) . " /><img src=\"" . $GLOBALS['SITEURL'] . "/images/progbar-rest.gif\" height=9 width=" . ((100-$p)*($maxpx/100)) . " />";
 if ($p >= 66 && $p <= 99) $progress = "<img src=\"" . $GLOBALS['SITEURL'] . "/images/progbar-green.gif\" height=9 width=" . ($p*($maxpx/100)) . " /><img src=\"" . $GLOBALS['SITEURL'] . "/images/progbar-rest.gif\" height=9 width=" . ((100-$p)*($maxpx/100)) . " />";
 return "<img src=\"" . $GLOBALS['SITEURL'] . "/images/bar_left.gif\" />" . $progress ."<img src=\"" . $GLOBALS['SITEURL'] . "/images/bar_right.gif\" />";
}

function torrenttable($res, $variant = "index") {
//
// The parts commented out in this section can be used to display different columns in your torrent tables
// Please only modify the section below if you understand PHP/MYSQL
//
	global $CURUSER, $MEMBERSONLY_WAIT, $MAXDISPLAYLENGTH, $WAITA, $WAITB, $WAITC, $WAITD, $GIGSA, $GIGSB, $GIGSC, $GIGSD, $RATIOA, $RATIOB, $RATIOC, $RATIOD;	
	//ratio wait code
		if ($CURUSER["class"] < UC_VIP && $CURUSER['donated'] == 0 )
			 {
		  $gigs = $CURUSER["uploaded"] / (1024*1024*1024);
		$ratio = (($CURUSER["downloaded"] > 0) ? ($CURUSER["uploaded"] / $CURUSER["downloaded"]) : 0);
		  if ($ratio < 0 || $gigs < 0) $wait = $WAITA;
		  elseif ($ratio < $RATIOA || $gigs < $GIGSA) $wait = $WAITA;
		  elseif ($ratio < $RATIOB || $gigs < $GIGSB) $wait = $WAITB;
		  elseif ($ratio < $RATIOC || $gigs < $GIGSC) $wait = $WAITC;
		  elseif ($ratio < $RATIOD || $gigs < $GIGSD) $wait = $WAITD;
		  else $wait = 0;
			}
	//end ratio wait code
?>

<table align=center cellpadding="0" cellspacing="0" class="ttable_headouter" width=100%>
<td>
<table align=center cellpadding="0" cellspacing="0" class="ttable_headinner" width=100%>

<!---------------------START SORTING MOD------------------------->
<?php
$count_get = 0;
foreach ($_GET as $get_name => $get_value) {
if ($get_name != "sort" && $get_name != "type") {
 if ($count_get > 0) {
  $oldlink = $oldlink . "&" . $get_name . "=" . $get_value;
 } else {
  $oldlink = $oldlink . $get_name . "=" . $get_value;
 }
 $count_get++;
}
}

if ($count_get > 0) {
$oldlink = $oldlink . "&";
}

if ($_GET['sort'] == "1") {
if ($_GET['type'] == "desc") {
 $link1 = "asc";
} else {
 $link1 = "desc";
}
}

if ($_GET['sort'] == "2") {
if ($_GET['type'] == "desc") {
 $link2 = "asc";
} else {
 $link2 = "desc";
}
}

if ($_GET['sort'] == "3") {
if ($_GET['type'] == "desc") {
 $link3 = "asc";
} else {
 $link3 = "desc";
}
}

if ($_GET['sort'] == "4") {
if ($_GET['type'] == "desc") {
 $link4 = "asc";
} else {
 $link4 = "desc";
}
}

if ($_GET['sort'] == "5") {
if ($_GET['type'] == "desc") {
 $link5 = "asc";
} else {
 $link5 = "desc";
}
}

if ($_GET['sort'] == "6") {
if ($_GET['type'] == "desc") {
 $link6 = "asc";
} else {
 $link6 = "desc";
}
}

if ($_GET['sort'] == "7") {
if ($_GET['type'] == "desc") {
 $link7 = "asc";
} else {
 $link7 = "desc";
}
}

if ($_GET['sort'] == "8") {
if ($_GET['type'] == "desc") {
 $link8 = "asc";
} else {
 $link8 = "desc";
}
}

if ($link1 == "") { $link1 = "asc"; } // for torrent name
if ($link2 == "") { $link2 = "desc"; } // for torrent nfo
if ($link3 == "") { $link3 = "desc"; } // for Comments
if ($link4 == "") { $link4 = "desc"; } // for Size
if ($link5 == "") { $link5 = "desc"; } // for Times Completed
if ($link6 == "") { $link6 = "desc"; } // for Seeders
if ($link7 == "") { $link7 = "desc"; } // for Leechers
if ($link8 == "") { $link8 = "desc"; } //for Categories
?>

<!--------------------END SORTING MOD--------------------->

<td class=ttable_head><a href="?<?= $oldlink; ?>sort=8&type=<?= $link8; ?>"><?= TYPE ?></a></td>
<td class=ttable_head><a href="?<?= $oldlink; ?>sort=1&type=<?= $link1; ?>"><?= NAME ?></a></td>
<?php if ($variant == "index"){
echo "<td class=ttable_head>DL</td>";
?>
<td class=ttable_head><a href="?<?= $oldlink; ?>sort=2&type=<?= $link2; ?>">NFO</a></td>
<?php
}
elseif ($variant == "mytorrents"){
echo "<td class=ttable_head>" . EDIT . "</td>";
echo "<td class=ttable_head>" . VISIBLE . "</td>";
echo "<td class=ttable_head>" . BANNED . "</td>";
}

if ($MEMBERSONLY_WAIT){
	if ($wait)
		{
			print("<td class=ttable_head>" . WAIT . "</td>\n");
		}
}
?>
<td class=ttable_head><a href="?<?= $oldlink; ?>sort=3&type=<?= $link3; ?>"><?= COMMENTS ?></a></td>
<!-- <td class=ttable_head><?= RATINGS ?></td> -->
<td class=ttable_head><a href="?<?= $oldlink;?>sort=4&type=<?= $link4; ?>"><?= SIZE ?></a></td>
<!-- <td class=ttable_head><?= FILES ?></td> -->
<td class=ttable_head><a href="?<?= $oldlink;?>sort=5&type=<?= $link5; ?>"><?= COMPLETED ?></a></td>
<td class=ttable_head><a href="?<?= $oldlink;?>sort=6&type=<?= $link6; ?>"><?= SEEDS ?></a></td>
<td class=ttable_head><a href="?<?= $oldlink;?>sort=7&type=<?= $link7; ?>"><?= LEECH ?></a></td>
<td class=ttable_head><?= HEALTH ?></td>

<?php
	print("</tr>\n");

	while ($row = mysql_fetch_assoc($res)) {
		$id = $row["id"];
		print("<tr>\n");

		print("<td class=ttable_col1 align=center>");
		if (isset($row["cat_name"])) {
			print("<a href=\"browse.php?cat=" . $row["category"] . "\">");
			if (isset($row["cat_pic"]) && $row["cat_pic"] != "")
				print("<img border=\"0\"src=\"" . $GLOBALS['SITEURL'] . "/images/categories/" . $row["cat_pic"] . "\" alt=\"" . $row["cat_name"] . "\" />");
			else
				print($row["cat_name"]);
			print("</a>");
		}
		else
			print("-");
		print("</td>\n");

		// MODIFICATION TO DISPLAY ONLY x FIRST CHARACTERS IN TORRENT NAME !

$smallname =substr(h($row["name"]) , 0, $MAXDISPLAYLENGTH);
if ($smallname != h($row["name"])) { $smallname .= '...' ; }

$last_browse = $CURUSER["last_browse"];
$time_now = gmtime();
if ($last_browse > $time_now || !is_numeric($last_browse)) {
  $last_browse=$time_now;
}
if (sql_timestamp_to_unix_timestamp($row["added"]) >= $last_browse){
	$dispname = "<b>" . $smallname . "</b> <b><font color=red>(NEW)</font></b>";
}else{
	$dispname = "<b>" . $smallname . "</b>";
}

		print("<td class=ttable_col2> <img border=0 src=" . $GLOBALS['SITEURL'] . "/images/cross.gif id=expandoGif$id onclick=\"expand($id)\" alt=\"show/hide\"> <a  title=\"".$row["name"]."\" href=\"torrents-details.php?");

        if ($variant == "mytorrents")
			print("returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&amp;");
		print("id=$id");
		if ($variant == "index")
			print("&amp;hit=1");
		print("\">$dispname</a></td>\n");

		if ($variant == "index"){
			print("<td class=ttable_col1 align=center><a href=\"download.php?id=$id&name=" . rawurlencode($row["filename"]) . "\"><img src=" . $GLOBALS['SITEURL'] . "/images/icon_download.gif border=0 alt=\"Download .torrent\"></a></td>");

			$nfo = h($row["nfo"]);
			if (!$nfo) {
				print("<td class=ttable_col1 align=center>-</td>");
			}else{
				print("<td class=ttable_col1 align=center><a href=torrents-viewnfo.php?id=$row[id]><img  src=" . $GLOBALS['SITEURL'] . "/images/icon_nfo.gif border=0 alt='View NFO'></a></td>");
			}
		}
		elseif ($variant == "mytorrents")
			print("<td class=ttable_colx align=center><a href=\"torrents-edit.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&amp;id=" . $row["id"] . "\"><font size=1 face=Verdana>EDIT</a></td>\n");

		if ($variant == "mytorrents") {
			print("<td class=ttable_colx align=center>");
			if ($row["visible"] == "no")
				print("NO");
			else
				print("YES");
			print("</td>\n");
		}

		if ($variant == "mytorrents") {
			print("<td class=ttable_colx align=center>");
			if ($row["banned"] == "no")
				print("NO");
			else
				print("YES");
			print("</td>\n");
		}

//START RATIO WAIT HACK
if ($MEMBERSONLY_WAIT){
		if ($wait)	{
			$elapsed = floor((gmtime() - strtotime($row["added"])) / 3600);
	        if ($elapsed < $wait)
	        {
	          $color = dechex(floor(127*($wait - $elapsed)/48 + 128)*65536);
	          print("<td align=center class=ttable_colx><nobr><a href=\"faq.php\"><font color=\"$color\">" . number_format($wait - $elapsed) . " h</font></a></nobr></td>\n");
			}
	        else
	          print("<td align=center class=ttable_colx><nobr>-</nobr></td>\n");
        }
}
//END RATIO WAIT HACK

		print("<td class=ttable_col1 align=center><font size=1 face=Verdana><a href=torrents-comment.php?id=$id>" . $row["comments"] . "</a></td>\n");

	/*	print("<td class=ttable_col2 align=center>");
		if (!isset($row["rating"]))
			print("---");
		else {
			$rating = round($row["rating"] * 2) / 2;
			$rating = ratingpic($row["rating"]);
			if (!isset($rating))
				print("-");
			else
				print($rating);
		}
		print("</td>\n");*/

		print("<td class=ttable_col2 align=center><font size=1 face=Verdana>" . mksize($row["size"]) . "</td>\n");

			/*	if ($row["type"] == "single")
			print("<td class=alt2 align=center><font size=1 face=Verdana>" . $row["numfiles"] . "</td>\n");
		else {
			if ($variant == "index")
				print("<td class=ttable_col1 align=center><b><font size=1 face=Verdana><a href=\"torrents-details.php?id=$id&amp;hit=1&amp;filelist=1\">" . $row["numfiles"] . "</a></b></td>\n");
			else
				print("<td class=ttable_col1 align=center><b><font size=1 face=Verdana><a href=\"torrents-details.php?id=$id&amp;filelist=1#filelist\">" . $row["numfiles"] . "</a></b></td>\n"); 
		}*/

		print("<td class=ttable_col1 align=center>" . $row["times_completed"] . "</td>\n");

		if ($row["seeders"]) {
			if ($variant == "index")
				print("<td class=ttable_col2 align=center><b><font color=green><B>" . $row["seeders"] . "</b></td>\n");
			else
				print("<td class=ttable_col2 align=center><b><font color=green><B>" . $row["seeders"] . "</b></td>\n");
		}
		else
			print("<td class=ttable_col2 align=center><font color=green><B>" . $row["seeders"] . "</b></td>\n");

		if ($row["leechers"]) {
			if ($variant == "index")
				print("<td class=ttable_col1 align=center><font color=red><b>" . $row["leechers"] . "</b></td>\n");
			else
				print("<td class=ttable_col1 align=center><font color=red><b>" . $row["leechers"] . "</b></td>\n");
		}
		else
			print("<td class=ttable_col1 align=center><font color=red><B>" . $row["leechers"] . "</b></td>\n");

// Progressbar Mod
$seedersProgressbar = [];
$leechersProgressbar = [];
$resProgressbar = mysql_query("SELECT p.seeder, p.to_go, t.size FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE  p.torrent = '$id'") or sqlerr();
$progressPerTorrent = 0;
$iProgressbar = 0;
while ($rowProgressbar = mysql_fetch_array($resProgressbar)) {
    $progressPerTorrent += sprintf("%.2f", 100 * (1 - ($rowProgressbar["to_go"] / $rowProgressbar["size"])));    
    $iProgressbar++;
}
if ($iProgressbar == 0)
    $iProgressbar = 1;
$progressTotal = sprintf("%.2f", $progressPerTorrent / $iProgressbar);
$picProgress = get_percent_completed_image(floor($progressTotal))." (".round($progressTotal)."%)";
print("<td class=ttable_col2 align=left>$picProgress</td>\n");

/*
End of modification
*/


		print("</tr>\n");
		print("<tr><td class=alt1 colspan=11><div id=\"descr$id\" style=\"margin-left: 70px; display: none\">\n");
		print("<table width=97% border=0 cellspacing=0 cellpadding=0>\n");
		print("<tr><td><b>Date Added:</b></td>\n");
		print("<td>" . str_replace(" ", "&nbsp;at&nbsp;", $row["added"]) . "</td>\n");
			if($row["privacy"] == "strong" && get_user_class() < UC_JMODERATOR AND $CURUSER["id"] != $row["owner"]){
			print("</tr><tr><td><b>Added By:</b></td><td>Anonymous</td></tr><tr><td><b>Comments</b></td>\n");
			}else{
			print("</tr><tr><td><b>Added By:</b></td><td><a href=account-details.php?id=" . $row["owner"] . ">" . (isset($row["username"]) ? h($row["username"]) : "<i>(unknown)</i>") . "</a></td></tr><tr><td><b>Comments</b></td>\n");
			}
		print("<td>There are <b><a href=\"torrents-details.php?id=$id#startcomments\">" . $row["comments"] . "</a></b> comments for this file.\n");
		print("</td>\n");
		print("</tr><tr><td><b>Status:</b></td>\n");
		print("<td>\n");

		if ($row['seeders'] == 0 && $row['leechers'] == 0) {
			// no seeders/leechers = innactive
			echo '<font color=#808080><b>INACTIVE</b></font>- This release is most probably dead (<b>' . $row['seeders'] . '</b> seeds and <b>' . $row['leechers'] . '</b> leechers).';
		} elseif($row['seeders'] == 0 && $row['leechers']) {
			// some leechers but no seed = very bad
			echo '<font color=#CC0000><b>CAUTION</b></font>- The release is active (<b>' . $row['leechers'] . '</b>)but there are no complete versions for the file availble.';
		} elseif($row['seeders'] < 2) {
			// few seeds = poor
			echo '<font color=#808000><b>POOR</b></font>- This release is active but there are only <b>' . $row['seeders'] . '</b> seeds. This release may be slow to download.';
		} else {
			// working fine
			echo '<font color=#008000><b>GOOD</b></font>- This release is active (<b>' . $row['seeders'] . '</b> seeds and <b>' . $row['leechers'] . '</b> leechers) and should download within a few hours.';
		}
		//speed mod
		$resSpeed = mysql_query("SELECT seeders,leechers FROM torrents WHERE $where visible='yes' and id = $id ORDER BY added DESC LIMIT 15") or sqlerr(__FILE__, __LINE__); 
		if ($rowTmp = mysql_fetch_row($resSpeed))
			list($seedersTmp,$leechersTmp) = $rowTmp;  
		if ($seedersTmp >= 1 && $leechersTmp >= 1){ 
		   $speedQ = mysql_query("SELECT (t.size * t.times_completed + SUM(p.downloaded)) / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS totalspeed FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' AND p.torrent = '$id' GROUP BY t.id ORDER BY added ASC LIMIT 15") or sqlerr(__FILE__, __LINE__); 
		   $a = mysql_fetch_assoc($speedQ); 
		   $totalspeed = mksize($a["totalspeed"]) . "/s"; 
		} 
		else 
		$totalspeed = "Torrent inactive";  
			print("<tr><td><b>Total Speed:</b></td>\n");
			print("<td><b><font color=green>");
			echo $totalspeed;
			print("</font></b></td></tr>");//speed end

		print("</td></tr></table>\n");
		print("</div>\n");
	}

	print("</table></td></table>\n");

    return $rows;
}

function hit_start() {
    return;
    global $RUNTIME_START, $RUNTIME_TIMES;
    $RUNTIME_TIMES = posix_times();
    $RUNTIME_START = gettimeofday();
}

function hit_count() {
    return;
    global $RUNTIME_CLAUSE;
    if (preg_match(',([^/]+)$,', $_SERVER["SCRIPT_NAME"], $matches))
        $path = $matches[1];
    else
        $path= "(unknown)";
    $period = date("Y-m-d H") . ":00:00";
    $RUNTIME_CLAUSE = "page = " . sqlesc($path) . " AND period = '$period'";
    $update = "UPDATE hits SET count = count + 1 WHERE $RUNTIME_CLAUSE";
    mysql_query($update);
    if (mysql_affected_rows())
        return;
    $ret = mysql_query("INSERT INTO hits (page, period, count) VALUES (" . sqlesc($path) . ", '$period', 1)");
    if (!$ret)
        mysql_query($update);
}

function hit_end() {
    return;
    global $RUNTIME_START, $RUNTIME_CLAUSE, $RUNTIME_TIMES;
    if (empty($RUNTIME_CLAUSE))
        return;
    $now = gettimeofday();
    $runtime = ($now["sec"] - $RUNTIME_START["sec"]) + ($now["usec"] - $RUNTIME_START["usec"]) / 1000000;
    $ts = posix_times();
    $sys = ($ts["stime"] - $RUNTIME_TIMES["stime"]) / 100;
    $user = ($ts["utime"] - $RUNTIME_TIMES["utime"]) / 100;
    mysql_query("UPDATE hits SET runs = runs + 1, runtime = runtime + $runtime, user_cpu = user_cpu + $user, sys_cpu = sys_cpu + $sys WHERE $RUNTIME_CLAUSE");
}

function hash_pad($hash) {
    return str_pad($hash, 20);
}

function hash_where($name, $hash) {
    $shhash = preg_replace('/ *$/s', "", $hash);
    return "($name = " . sqlesc($hash) . " OR $name = " . sqlesc($shhash) . ")";
}


// Set this to the line break character sequence of your system
$linebreak = "\r\n";

function get_row_count($table, $suffix = '')
{
    $suffix = !empty($suffix) ? ' '.$suffix : '';
    $num = DB::fetchColumn('
    SELECT COUNT(*)
    FROM '.$table.$suffix, [], 0);

    return $num;
}

function show_error_msg($title, $message, $wrapper = "1") {
    if ($wrapper)
		stdhead($title);
		begin_frame("<font color=red>". h($title) ."</font>");
		echo "<p><CENTER><B>$message</B></CENTER></p>";
		end_frame();

    if ($wrapper) {
		stdfoot();
		exit;
	}
}


function stderr($heading = "", $text, $sort = "") {
  stdhead("$sort: $heading"); 
  begin_frame("<font color=red>$sort: $heading</font>", 'center');
  echo $text;
  end_frame();
  stdfoot();
  die;
}

function bark($heading = "Error", $text, $sort = "Error") {
  stdhead("$sort: $heading");
  begin_frame("<font color=red>$sort: $heading</font>", 'center');
  echo $text;
  end_frame();
  stdfoot();
  die;
}

function bark2($heading = "Error", $text, $sort = "Error") {
	print("<div align=\"center\"><br /><table border=\"0\" width=\"500\" cellspacing=\"0\" cellpadding=\"0\"><tr>\n");
	print("<td bgcolor=\"#FFFFFF\" align=\"center\" style=\"border-style: dotted; border-width: 1px\" bordercolor=\"#CC0000\">\n");
	print("<font face=\"Verdana\" size=\"1\"><font color=\"#CC0000\"><b>$heading</b></font><br />$text</font></td>\n");
	print("</tr></table></div><br />\n");
}
function sqlerr($query = "") {
	stdhead();
	begin_frame("MYSQL Error");
	print("<div align=center><br><table border=0 width=500 cellspacing=0 cellpadding=0><tr>\n");
	print("<td bgcolor=#FFFFFF align=center style=border-style: dotted; border-width: 1px bordercolor=#CC0000>\n");
	print("<font face=Verdana size=1><b>MYSQL Error has occurred!</b><br><BR>There is a problem with the database, possibly a corrupt table, missing field/column or bad syntax.</font></td>\n");
	print("</tr></table></div><br>\n");
	//print("<BR><b>MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	end_frame();
	stdfoot();
	die;
}

// @todo: check: ??? не используется?
function get_user_timezone($id)
{
    global $CURUSER;

    $id = (int) $id;
    if ($CURUSER) {
        $timezone = DB::fetchColumn('
    SELECT tzoffset
    FROM users
    WHERE id = '.$id.'
    LIMIT 1');

        if ($timezone) {
            return $timezone;
        } else {
            return 3;
        } // Default timezone
    }
}

function numUserMsg()
{
    global $CURUSER, $st;

    if (isset($st['cache']['user_num_msg'])) {
        return $st['cache']['user_num_msg'];
    }

    $id = (int) $CURUSER['id'];
    $num = DB::fetchColumn('
        SELECT COUNT(*)
        FROM messages
        WHERE receiver = ?
        LIMIT 1',
        [$id]
    );
    $st['cache']['user_num_msg'] = $num;

    return $num;
}

function numUnreadUserMsg()
{
    global $CURUSER, $st;

    if (isset($st['cache']['numUnreadMsg']))
        return $st['cache']['numUnreadMsg'];

    $id = (int) $CURUSER['id'];
    $num = DB::fetchColumn('
    SELECT COUNT(*)
    FROM messages
    WHERE receiver = '.$id.'
        AND unread = ?', ['yes'], 0);
    $st['cache']['numUnreadMsg'] = $num;

    return $num;
}

// Returns the current time in GMT in MySQL compatible format.
function get_date_time($timestamp = 0)
{
if ($timestamp)
return date("Y-m-d H:i:s", $timestamp);
else
  $idcookie = $_COOKIE['uid'];
  return gmdate("Y-m-d H:i:s", time() + (60 * get_user_timezone($idcookie)));
}

function encodehtml($s, $linebreaks = true)
{
  $s = str_replace("<", "&lt;", str_replace("&", "&amp;", $s));
  if ($linebreaks)
    $s = nl2br($s);
  return $s;
}

function get_dt_num()
{
  return gmdate("YmdHis");
}

function format_urls($s)
{
return preg_replace(
    "/(\A|[^=\]'\"a-zA-Z0-9])((http|ftp|https|ftps|irc):\/\/[^<>\s]+)/i",
    "\\1<a href=redirect.php?url=\\2>\\2</a>", $s);
//  return preg_replace( "/(?<!<a href=\")((http|ftp)+(s)?:\/\/[^<>\s]+)/i", "<a href=\"\\0\">\\0</a>", $txt );
}

function format_comment($text, $strip_html = true, $strip_slash = true)
{
	global $smilies, $privatesmilies;

	$s = $text;

	if ($strip_html)
		$s = h($s);

	if ($strip_slash)
		$s = stripslashes($s);

	// [*]
	$s = preg_replace("/\[\*\]/", "<li>", $s);

	// [b]Bold[/b]
	$s = preg_replace("/\[b\]((\s|.)+?)\[\/b\]/", "<b>\\1</b>", $s);

	// [i]Italic[/i]
	$s = preg_replace("/\[i\]((\s|.)+?)\[\/i\]/", "<i>\\1</i>", $s);

	// [u]Underline[/u]
	$s = preg_replace("/\[u\]((\s|.)+?)\[\/u\]/", "<u>\\1</u>", $s);

	// [u]Underline[/u]
	$s = preg_replace("/\[u\]((\s|.)+?)\[\/u\]/i", "<u>\\1</u>", $s);

	// [img]http://www/image.gif[/img]
	$s = preg_replace("/\[img\](http:\/\/[^\s'\"<>]+(\.gif|\.jpg|\.png|\.bmp|\.jpeg))\[\/img\]/i", "<img border=0 src=\"\\1\">", $s);

	// [img=http://www/image.gif]
	$s = preg_replace("/\[img=(http:\/\/[^\s'\"<>]+(\.gif|\.jpg|\.png|\.bmp|\.jpeg))\]/i", "<img border=0 src=\"\\1\">", $s);

	// [color=blue]Text[/color]
	$s = preg_replace(
		"/\[color=([a-zA-Z]+)\]((\s|.)+?)\[\/color\]/i",
		"<font color=\\1>\\2</font>", $s);

	// [color=#ffcc99]Text[/color]
	$s = preg_replace(
		"/\[color=(#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])\]((\s|.)+?)\[\/color\]/i",
		"<font color=\\1>\\2</font>", $s);

	// [url=http://www.example.com]Text[/url]
	$s = preg_replace(
		"/\[url=((http|ftp|https|ftps|irc):\/\/[^<>\s]+?)\]((\s|.)+?)\[\/url\]/i",
		"<a href=redirect.php?url=\\1>\\3</a>", $s);

	// [url]http://www.example.com[/url]
	$s = preg_replace(
		"/\[url\]((http|ftp|https|ftps|irc):\/\/[^<>\s]+?)\[\/url\]/i",
		"<a href=redirect.php?url=\\1>\\1</a>", $s);

	// [size=4]Text[/size]
	$s = preg_replace(
		"/\[size=([1-7])\]((\s|.)+?)\[\/size\]/i",
		"<font size=\\1>\\2</font>", $s);

	// [font=Arial]Text[/font]
	$s = preg_replace(
		"/\[font=([a-zA-Z ,]+)\]((\s|.)+?)\[\/font\]/i",
		"<font face=\"\\1\">\\2</font>", $s);

	//[quote]Text[/quote]
	$s = preg_replace(
		"/\[quote\]\s*((\s|.)+?)\s*\[\/quote\]\s*/i",
		"<p class=sub><b>Quote:</b></p><table class=main border=1 cellspacing=0 cellpadding=10><tr><td style='border: 1px black dotted'>\\1</td></tr></table><br />", $s);

	//[quote=Author]Text[/quote]
	$s = preg_replace(
		"/\[quote=(.+?)\]\s*((\s|.)+?)\s*\[\/quote\]\s*/i",
		"<p class=sub><b>\\1 wrote:</b></p><table class=main border=1 cellspacing=0 cellpadding=10><tr><td style='border: 1px black dotted'>\\2</td></tr></table><br />", $s);
                
        //[hr]
        $s = preg_replace("/\[hr\]/i", "<hr>", $s);

        //[hr=#ffffff] [hr=red]
        $s = preg_replace("/\[hr=((#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])|([a-zA-z]+))\]/i", "<hr color=\"\\1\"/>", $s);

        //[swf]http://somesite.com/test.swf[/swf]
        $s = preg_replace("/\[swf\]((www.|http:\/\/|https:\/\/)[^\s]+(\.swf))\[\/swf\]/i",
        "<param name=movie value=\\1/><embed width=470 height=310 src=\\1></embed>", $s);

        //[swf=http://somesite.com/test.swf]
        $s = preg_replace("/\[swf=((www.|http:\/\/|https:\/\/)[^\s]+(\.swf))\]/i",
        "<param name=movie value=\\1/><embed width=470 height=310 src=\\1></embed>", $s);

	// URLs
	$s = format_urls($s);

	// Linebreaks
	$s = nl2br($s);

	// Maintain spacing
	$s = str_replace("  ", " &nbsp;", $s);

    foreach ($smilies as $code => $url) {
		$s = str_replace($code, "<img border=0 src=" . $GLOBALS['SITEURL'] . "/images/smilies/$url>", $s);
    }

    foreach ($smilies as $code => $url) {
		$s = str_replace($code, "<img border=0 src=" . $GLOBALS['SITEURL'] . "/images/smilies/$url>", $s);
    }

	return $s;
}

const UC_USER = 0;
const UC_UPLOADER = 1;
const UC_VIP = 2;
const UC_JMODERATOR = 3;
const UC_MODERATOR = 4;
const UC_ADMINISTRATOR = 5;

function get_user_class()
{
  global $CURUSER;
  return $CURUSER["class"];
}

function get_user_class_name($class)
{
  switch ($class)
  {
    case UC_USER: return "User";

	case UC_UPLOADER: return "Uploader";

    case UC_VIP: return "VIP";

	case UC_JMODERATOR: return "Moderator";

    case UC_MODERATOR: return "Super Moderator";

    case UC_ADMINISTRATOR: return "Administrator";

    }
  return "";
}

function is_valid_user_class($class)
{
  return is_numeric($class) && floor($class) == $class && $class >= UC_USER && $class <= UC_ADMINISTRATOR;
}

function is_valid_id($id)
{
  return is_numeric($id) && ($id > 0) && (floor($id) == $id);
}

function begin_table()
  {

    print("<table align=center cellpadding=\"0\" cellspacing=\"0\" class=\"ttable_headouter\" width=100%><tr><td>"
         ."<table align=center cellpadding=\"0\" cellspacing=\"0\" class=\"ttable_headinner\" width=100%>\n"); 
  }

  function end_table()
  {
    print("</table></td></tr></table>\n");
  }

  //-------- Inserts a smilies frame
  //         (move to globals)

  function insert_smilies_frame()
  {
    global $smilies;

    begin_frame("Smilies", true);

    begin_table(false, 5);

    print("<tr><td class=colhead>Type...</td><td class=colhead>To make a...</td></tr>\n");

    while (list($code, $url) = each($smilies))
      print("<tr><td>$code</td><td><img src=" . $GLOBALS['SITEURL'] . "/images/smilies/$url></td>\n");


    end_table();

    end_frame();
  }


function sql_timestamp_to_unix_timestamp($s)
{
  return mktime(substr($s, 11, 2), substr($s, 14, 2), substr($s, 17, 2), substr($s, 5, 2), substr($s, 8, 2), substr($s, 0, 4));
}

  function get_ratio_color($ratio)
  {
    if ($ratio < 0.1) return "#ff0000";
    if ($ratio < 0.2) return "#ee0000";
    if ($ratio < 0.3) return "#dd0000";
    if ($ratio < 0.4) return "#cc0000";
    if ($ratio < 0.5) return "#bb0000";
    if ($ratio < 0.6) return "#aa0000";
    if ($ratio < 0.7) return "#990000";
    if ($ratio < 0.8) return "#880000";
    if ($ratio < 0.9) return "#770000";
    if ($ratio < 1) return "#660000";
    return "#000000";
  }

  function get_slr_color($ratio)
  {
    if ($ratio < 0.025) return "#ff0000";
    if ($ratio < 0.05) return "#ee0000";
    if ($ratio < 0.075) return "#dd0000";
    if ($ratio < 0.1) return "#cc0000";
    if ($ratio < 0.125) return "#bb0000";
    if ($ratio < 0.15) return "#aa0000";
    if ($ratio < 0.175) return "#990000";
    if ($ratio < 0.2) return "#880000";
    if ($ratio < 0.225) return "#770000";
    if ($ratio < 0.25) return "#660000";
    if ($ratio < 0.275) return "#550000";
    if ($ratio < 0.3) return "#440000";
    if ($ratio < 0.325) return "#330000";
    if ($ratio < 0.35) return "#220000";
    if ($ratio < 0.375) return "#110000";
    return "#000000";
  }

function write_log($text)
{
  $text = sqlesc($text);
  $added = sqlesc(get_date_time());
  mysql_query("INSERT INTO log (added, txt) VALUES($added, $text)") or sqlerr();
}

function get_elapsed_time($ts)
{
  $mins = floor((gmtime() - $ts) / 60);
  $hours = floor($mins / 60);
  $mins -= $hours * 60;
  $days = floor($hours / 24);
  $hours -= $days * 24;
  $weeks = floor($days / 7);
  $days -= $weeks * 7;
  $t = "";
  if ($weeks)
    return "$weeks week" . ($weeks > 1 ? "s" : "");
  if ($days)
    return "$days day" . ($days > 1 ? "s" : "");
  if ($hours)
    return "$hours hour" . ($hours > 1 ? "s" : "");
  if ($mins)
    return "$mins min" . ($mins > 1 ? "s" : "");
  return "< 1 min";
}

if (! function_exists('hex2bin')) {
    function hex2bin($hexdata) {
      $bindata = '';
      for ($i=0; $i<strlen($hexdata); $i+=2) {
        $bindata.=chr(hexdec(substr($hexdata,$i,2)));
      }
     
      return $bindata;
    }
}

function guestadd()
{
    $ip = $_SERVER["REMOTE_ADDR"];
    $sql = DB::fetchAssoc('
        SELECT time
        FROM guests
        WHERE ip = ?',
        [$ip]
    );
    $ctime = time();
    if ($sql) {
        DB::update('guests', 
            ['ip' => $ip, 'time' => $ctime],
            ['ip' => $ip]
        );
    } else {
        DB::insert('guests', ['ip' => $ip, 'time' => $ctime]);
    }
}

function getguests() {
    $ip = $_SERVER["REMOTE_ADDR"];
    $past = time()-2400;
	@mysql_query("DELETE FROM guests WHERE time < $past");
	$guests = number_format(get_row_count("guests"));
	return $guests;
}

function str_contains($haystack, $needle, $ignoreCase = false) {
   if ($ignoreCase) {
       $haystack = strtolower($haystack);
       $needle  = strtolower($needle);
   }
   $needlePos = strpos($haystack, $needle);
   return ($needlePos === false ? false : ($needlePos+1));
} 

function time_ago($addtime) {
   $addtime = get_elapsed_time(sql_timestamp_to_unix_timestamp($addtime));
   return $addtime;
}

function CutName ($vTxt, $Car) {
	while(strlen($vTxt) > $Car) {
		return substr($vTxt, 0, $Car) . "...";
	} return $vTxt;
}

function textbbcode($form,$name,$content="") {?><script language=javascript>function SmileIT(smile,form,text){  document.forms[form].elements[text].value = document.forms[form].elements[text].value+" "+smile+" ";  document.forms[form].elements[text].focus();}function PopMoreSmiles(form,name) {        link='moresmiles.php?form='+form+'&text='+name        newWin=window.open(link,'moresmile','height=500,width=300,resizable=no,scrollbars=yes');        if (window.focus) {newWin.focus()}}function BBTag(tag,s,text,form){switch(tag)  {  case '[quote]':  if (document.forms[form].elements[s].value=="QUOTE ")     {      document.forms[form].elements[text].value = document.forms[form].elements[text].value+"[quote]";      document.forms[form].elements[s].value="QUOTE*";      }     else         {         document.forms[form].elements[text].value = document.forms[form].elements[text].value+"[/quote]";         document.forms[form].elements[s].value="QUOTE ";         }      break;  case '[img]':  if (document.forms[form].elements[s].value=="IMG ")     {      document.forms[form].elements[text].value = document.forms[form].elements[text].value+"[img]";      document.forms[form].elements[s].value="IMG*";      }     else         {         document.forms[form].elements[text].value = document.forms[form].elements[text].value+"[/img]";         document.forms[form].elements[s].value="IMG ";         }      break;  case '[url]':  if (document.forms[form].elements[s].value=="URL ")     {      document.forms[form].elements[text].value = document.forms[form].elements[text].value+"[url]";      document.forms[form].elements[s].value="URL*";      }     else         {         document.forms[form].elements[text].value = document.forms[form].elements[text].value+"[/url]";         document.forms[form].elements[s].value="URL ";         }      break;  case '[*]':  if (document.forms[form].elements[s].value=="List ")     {      document.forms[form].elements[text].value = document.forms[form].elements[text].value+"[*]";      }      break;  case '':  if (document.forms[form].elements[s].value=="B ")     {      document.forms[form].elements[text].value = document.forms[form].elements[text].value+"[b]";      document.forms[form].elements[s].value="B*";      }     else         {         document.forms[form].elements[text].value = document.forms[form].elements[text].value+"";         document.forms[form].elements[s].value="B ";         }      break;  case '':  if (document.forms[form].elements[s].value=="I ")     {      document.forms[form].elements[text].value = document.forms[form].elements[text].value+"[i]";      document.forms[form].elements[s].value="I*";      }     else         {         document.forms[form].elements[text].value = document.forms[form].elements[text].value+"";         document.forms[form].elements[s].value="I ";         }      break;  case '':  if (document.forms[form].elements[s].value=="U ")     {      document.forms[form].elements[text].value = document.forms[form].elements[text].value+"[u]";      document.forms[form].elements[s].value="U*";      }     else         {         document.forms[form].elements[text].value = document.forms[form].elements[text].value+"";         document.forms[form].elements[s].value="U ";         }      break;  }  document.forms[form].elements[text].focus();}</script><table width="100%" style='margin: 3px' cellpadding="0" cellspacing="0">  <tr>    <td class=embedded colspan=3>    <table cellpadding="2" cellspacing="1">    <tr>    <td class=embedded><input style="font-weight: bold;" type="button" name="bold" value="B " onclick="javascript: BBTag('[b]','bold','<?= $name; ?>','<?= $form; ?>')" /></td>    <td class=embedded><input style="font-style: italic;" type="button" name="italic" value="I " onclick="javascript: BBTag('[i]','italic','<?= $name; ?>','<?= $form; ?>')" /></td>    <td class=embedded><input style="text-decoration: underline;" type="button" name="underline" value="U " onclick="javascript: BBTag('[u]','underline','<?= $name; ?>','<?= $form; ?>')" /></td>    <td class=embedded><input type="button" name="li" value="List " onclick="javascript: BBTag('[*]','li','<?= $name; ?>','<?= $form; ?>')" /></td>    <td class=embedded><input type="button" name="quote" value="QUOTE " onclick="javascript: BBTag('[quote]','quote','<?= $name; ?>','<?= $form; ?>')" /></td>    <td class=embedded><input type="button" name="url" value="URL " onclick="javascript: BBTag('[url]','url','<?= $name; ?>','<?= $form; ?>')" /></td>    <td class=embedded><input type="button" name="img" value="IMG " onclick="javascript: BBTag('[img]','img','<?= $name; ?>','<?= $form; ?>')" /></td>    </tr>    </table>    </td>  </tr>  <tr>    <td class=embedded>    <textarea name="<?= $name; ?>" rows="15" cols="80"><?= $content; ?></textarea>    </td>    <td class=embedded>    <table cellpadding="3" cellspacing="1">    <?php global $smilies;    while ((list($code, $url) = each($smilies)) && $count<36) {       if ($count % 4==0)          print("<tr>");          print("\n<td class=embedded style='padding: 3px; margin: 2px'><a href=\"javascript: SmileIT('".str_replace("'","\'",$code)."','$form','$name')\"><img border=0 src=pic/smilies/".$url."></a></td>");          $count++;       if ($count % 4==0)          print("</tr>");    }    ?>    </table> <center><a href="javascript: PopMoreSmiles('<?= $form; ?>','<?= $name; ?>')"><?= MORE_SMILES;?></a></center>    </td>  </tr></table>
<?php }

