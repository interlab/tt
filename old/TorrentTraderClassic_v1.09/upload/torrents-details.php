<?
//
// - Theme And Language Updated 26.Nov.05
//
require_once("backend/functions.php");
dbconn(false);
IF ($LOGGEDINONLY){
	loggedinorreturn();
}

//AGENT DETECT
function getagent($httpagent, $peer_id="")
{
if (preg_match("/^Azureus ([0-9]+\.[0-9]+\.[0-9]+\.[0-9]\_B([0-9][0-9|*])(.+)$)/", $httpagent, $matches))
return "Azureus/$matches[1]";
elseif (preg_match("/^Azureus ([0-9]+\.[0-9]+\.[0-9]+\.[0-9]\_CVS)/", $httpagent, $matches))
return "Azureus/$matches[1]";
elseif (preg_match("/^Java\/([0-9]+\.[0-9]+\.[0-9]+)/", $httpagent, $matches))
return "Azureus/<2.0.7.0";
elseif (preg_match("/^Azureus ([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $httpagent, $matches))
return "Azureus/$matches[1]";
elseif (preg_match("/BitTorrent\/S-([0-9]+\.[0-9]+(\.[0-9]+)*)/", $httpagent, $matches))
return "Shadow's/$matches[1]";
elseif (preg_match("/BitTorrent\/U-([0-9]+\.[0-9]+\.[0-9]+)/", $httpagent, $matches))
return "UPnP/$matches[1]";
elseif (preg_match("/^BitTor(rent|nado)\\/T-(.+)$/", $httpagent, $matches))
return "BitTornado/$matches[2]";
elseif (preg_match("/^BitTornado\\/T-(.+)$/", $httpagent, $matches))
return "BitTornado/$matches[1]";
elseif (preg_match("/^BitTorrent\/ABC-([0-9]+\.[0-9]+(\.[0-9]+)*)/", $httpagent, $matches))
return "ABC/$matches[1]";
elseif (preg_match("/^ABC ([0-9]+\.[0-9]+(\.[0-9]+)*)\/ABC-([0-9]+\.[0-9]+(\.[0-9]+)*)/", $httpagent, $matches))
return "ABC/$matches[1]";
elseif (preg_match("/^ABC\/ABC-([0-9]+\.[0-9]+(\.[0-9]+)*)/", $httpagent, $matches))
return "ABC $matches[1]";
elseif (preg_match("/^Python-urllib\/.+?, BitTorrent\/([0-9]+\.[0-9]+(\.[0-9]+)*)/", $httpagent, $matches))
return "BitTorrent/$matches[1]";
elseif (ereg("^BitTorrent\/BitSpirit$", $httpagent))
return "BitSpirit";
elseif (substr($peer_id, 0, 5) == "-BB09")
return "BitBuddy/0.9xx";
elseif (ereg("^DansClient", $httpagent))
return "XanTorrent";
elseif (substr($peer_id, 0, 8) == "-KT1100-")
return "KTorrent/1.1";
elseif (preg_match("/^BitTorrent\/brst(.+)/", $httpagent, $matches))
return "Burst/$matches[1]";
elseif (preg_match("/^RAZA (.+)$/", $httpagent, $matches))
return "Shareaza/$matches[1]";
elseif (preg_match("/Rufus\/([0-9]+\.[0-9]+\.[0-9]+)/", $httpagent, $matches))
return "Rufus/$matches[1]";
elseif (preg_match("/^BitTorrent\\/([0-9]+\\.[0-9]+(\\.[0-9]+)*)/", $httpagent, $matches))
{
if(substr($peer_id, 0, 6) == "exbc\08")
return "BitComet/0.56";
elseif(substr($peer_id, 0, 6) == "exbc\09")
return "BitComet/0.57";
elseif(substr($peer_id, 0, 6) == "exbc\0:")
return "BitComet/0.58";
elseif(substr($peer_id, 0, 8) == "-BC0059-")
return "BitComet/0.59";
elseif(substr($peer_id, 0, 8) == "-BC0060-")
return "BitComet/0.60";
elseif(substr($peer_id, 0, 8) == "-BC0061-")
return "BitComet/0.61";
elseif ((strpos($httpagent, 'BitTorrent/4.1.2')!== false) && (substr($peer_id, 2, 2) == "BS"))
return "BitSpirit/v3";
elseif(substr($peer_id, 0, 7) == "exbc\0L")
return "BitLord/1.0";
elseif(substr($peer_id, 0, 7) == "exbcL")
return "BitLord/1.1";
else
return "BitTorrent/$matches[1]";
}
elseif (preg_match("/^Python-urllib\\/([0-9]+\\.[0-9]+(\\.[0-9]+)*)/", $httpagent, $matches))
return "G3 Torrent";
elseif (preg_match("/MLdonkey( |\/)([0-9]+\\.[0-9]+).*/", $httpagent, $matches))
return "MLdonkey$matches[1]";
elseif (preg_match("/ed2k_plugin v([0-9]+\\.[0-9]+).*/", $httpagent, $matches))
return "eDonkey/$matches[1]";
elseif (ereg("^uTorrent", $httpagent))
{
if(substr($peer_id, 0, 8) == "-UT1130-")
return "uTorrent 1.1.3";
if(substr($peer_id, 0, 8) == "-UT1140-")
return "uTorrent 1.1.4";
if(substr($peer_id, 0, 8) == "-UT1150-")
return "uTorrent 1.1.5";
if(substr($peer_id, 0, 8) == "-UT1161-")
return "uTorrent 1.1.6.1";
if(substr($peer_id, 0, 8) == "-UT1171-")
return "uTorrent 1.1.7.1";
if(substr($peer_id, 0, 8) == "-UT1172-")
return "uTorrent 1.1.7.2";
if(substr($peer_id, 0, 8) == "-UT1200-")
return "uTorrent/1.2";
if(substr($peer_id, 0, 8) == "-UT1220-")
return "uTorrent/1.2.2";
if(substr($peer_id, 0, 8) == "-UT123B-")
return "uTorrent/1.2.3b";
if(substr($peer_id, 0, 8) == "-UT1300-")
return "uTorrent/1.3.0";
if(substr($peer_id, 0, 8) == "-UT1400-")
return "uTorrent/1.4.0";
else
return "uTorrent";
}
else
return ($httpagent != "" ? $httpagent : "---");
}

//PEERS TABLE FUNCTION
function dltable($name, $arr, $torrent)
{
	global $CURUSER;
	$s = "<b>" . count($arr) . " $name</b>\n";
	if (!count($arr))
		return $s;
	$s .= "\n";
	$s .= "<table class=table_table cellspacing=0 cellpadding=3 width=95%>\n";
	$s .= "<tr><td class=table_head>" . USERNAME . "/IP</td>" .
          "<td class=table_head>" . PORT . "</td>".
          "<td class=table_head>" . UPLOADED . "</td>".
          "<td class=table_head>" . DOWNLOADED . "</td>" .
          "<td class=table_head>" . RATIO . "</td>" .
          "<td class=table_head>" . COMPLETE . "</td>" .
          "<td class=table_head>" . CONNECTED . "</td>" .
          "<td class=table_head><b>" . IDLE . "</b></td>".
          "<td class=table_head><b>Client</b></td></tr>\n";
	$now = time();
	
	//DEFINE MODERATOR
	$moderator = (isset($CURUSER) && get_user_class() >= UC_JMODERATOR);
	$mod = get_user_class() >= UC_JMODERATOR;
	foreach ($arr as $e) {
		$s .= "<tr>\n";

 ($unr = mysql_query("SELECT id,username,privacy FROM users WHERE ip='" . $e["ip"] .
                  "' ORDER BY last_access DESC LIMIT 1")) or die;
                $una = mysql_fetch_array($unr);
					//mysql_free_result($unr);
if ($una["privacy"] == "strong" && get_user_class() < UC_JMODERATOR AND $CURUSER["id"] != $una["owner"]){
                $s .= "<td class=table_col1><a href=#><b>Anonymous</b></a></td>\n"; }
                elseif ($una["username"])
                $s .= "<td class=table_col1><a href=account-details.php?id=$una[id]><b>$una[username]</b></a></td>\n";
                else
                  $s .= "<td class=table_col1>" . ($mod ? $e["ip"] : preg_replace('/\.\d+$/', ".xxx", $e["ip"])) . "</td>\n";
        $s .= "<td class=table_col2>" . ($e[connectable] == "yes" ? $e["port"] : "---") . "</td>\n";
		$s .= "<td class=table_col1>" . mksize($e["uploaded"]) . "</td>\n";
		$s .= "<td class=table_col2>" . mksize($e["downloaded"]) . "</td>\n";
                if ($e["downloaded"])
                {
                  $ratio = $e["uploaded"] / $e["downloaded"];
                  if ($ratio < 0.1)
                    $s .= "<td class=table_col2><font color=#ff0000>" . number_format($ratio, 2) . "</font></td>\n";
                  else if ($ratio < 0.2)
                    $s .= "<td class=table_col2><font color=#ee0000>" . number_format($ratio, 2) . "</font></td>\n";
                  else if ($ratio < 0.3)
                    $s .= "<td class=table_col2><font color=#dd0000>" . number_format($ratio, 2) . "</font></td>\n";
                  else if ($ratio < 0.4)
                    $s .= "<td class=table_col2><font color=#cc0000>" . number_format($ratio, 2) . "</font></td>\n";
                  else if ($ratio < 0.5)
                    $s .= "<td class=table_col2><font color=#bb0000>" . number_format($ratio, 2) . "</font></td>\n";
                  else if ($ratio < 0.6)
                    $s .= "<td class=table_col2><font color=#aa0000>" . number_format($ratio, 2) . "</font></td>\n";
                  else if ($ratio < 0.7)
                    $s .= "<td class=table_col2><font color=#990000>" . number_format($ratio, 2) . "</font></td>\n";
                  else if ($ratio < 0.8)
                    $s .= "<td class=table_col2><font color=#880000>" . number_format($ratio, 2) . "</font></td>\n";
                  else if ($ratio < 0.9)
                    $s .= "<td class=table_col2><font color=#770000>" . number_format($ratio, 2) . "</font></td>\n";
                  else if ($ratio < 1)
                    $s .= "<td class=table_col2><font color=#660000>" . number_format($ratio, 2) . "</font></td>\n";
                  else
                    $s .= "<td class=table_col2>" . number_format($ratio, 2) . "</td>\n";
                }
                else
                  if ($e["uploaded"])
                    $s .= "<td class=table_col2>Inf.</td>\n";
                  else
                    $s .= "<td class=table_col2>---</td>\n";
		$s .= "<td class=table_col1>" . sprintf("%.2f%%", 100 * (1 - ($e["to_go"] / $torrent["size"]))) . "</td>\n";
		$s .= "<td class=table_col2>" . mkprettytime($now - $e["st"]) . "</td>\n";
		$s .= "<td class=table_col1>" . mkprettytime($now - $e["la"]) . "</td>\n";
		$s .= "<td class=table_col2 align=right>" . h(getagent($e["client"],$e["peer_id"])) . "</td>\n";
		$s .= "</tr>\n";
	}
	$s .= "</table>\n";
	return $s;
}
//END PEERS TABLE FUNCTION

//************ DO SOME "GET" STUFF BEFORE PAGE LAYOUT ***************

$id = $_GET["id"];
$id = 0 + $id;
if (!isset($id) || !$id)
	die();
//GET ALL MYSQL VALUES FOR THIS TORRENT
$res = mysql_query("SELECT torrents.seeders, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo AS nfo, UNIX_TIMESTAMP() - UNIX_TIMESTAMP(torrents.last_action) AS lastseed, torrents.numratings, torrents.name, IF(torrents.numratings < $minvotes, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.numfiles, categories.name AS cat_name, users.username, users.privacy FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id")
	or sqlerr();
$row = mysql_fetch_array($res);

//DECIDE IF USER IS OWNER/MOD
$owned = $moderator = 0;
	if (get_user_class() >= UC_MODERATOR)
		$owned = $moderator = 1;
	elseif ($CURUSER["id"] == $row["owner"])
		$owned = 1;

//DECIDE IF TORRENT EXISTS
if (!$row || ($row["banned"] == "yes" && !$moderator)){
	stdhead();
	begin_frame("Error");
	print("<br><BR><center>" . TORRENT_NOT_FOUND . "</center><br><BR>");
	end_frame();
	stdfoot();
	exit();
}else {
	if ($_GET["hit"]) {
		mysql_query("UPDATE torrents SET views = views + 1 WHERE id = $id");
		if ($_GET["tocomm"])
			header("Location: torrents-details.php?id=$id#startcomments");
			//header("Location: torrents-details.php?id=$id&page=0#startcomments");
		elseif ($_GET["filelist"])
			header("Location: torrents-details.php?id=$id&filelist=1#filelist");
		elseif ($_GET["toseeders"])
			header("Location: torrents-details.php?id=$id&dllist=1#seeders");
		elseif ($_GET["todlers"])
			header("Location: torrents-details.php?id=$id&dllist=1#leechers");
		else
			header("Location: torrents-details.php?id=$id");
		exit();
}


		if (!isset($_GET["page"]) || isset($_GET["page"])) {
		stdhead("Details for torrent \"" . $row["name"] . "\"");

		if ($CURUSER["id"] == $row["owner"] || get_user_class() >= UC_MODERATOR)
			$owned = 1;
		else
			$owned = 0;

		$spacer = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

		if ($_GET["uploaded"]) {
			bark2("Successfully uploaded!", "You can start seeding now. <b>Note</b> that the torrent won't be visible until you do that!");
		}
		elseif ($_GET["edited"]) {
			bark2("Success", "Edited OK!");
			if (isset($_GET["returnto"]))
				print("<p><b>Go back to <a href=\"" . h($_GET["returnto"]) . "\">previous page</a>.</b></p>\n");
		}
		elseif (isset($_GET["searched"])) {
			bark2("Success", "Your search for \"" . h($_GET["searched"]) . "\" gave a single result:");
		}
		elseif ($_GET["rated"])
			bark2("Success", "" . RATING_THANK . "");
//END "GET" STUFF

//DEFINE SOME VARIABLES
// $S IS RATING VARIABLE
		$s = "";
		$s .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td valign=\"top\" class=embedded>";
		if (!isset($row["rating"])) {
			if ($minvotes > 1) {
				$s .= "none yet (needs at least $minvotes votes and has got ";
				if ($row["numratings"])
					$s .= "only " . $row["numratings"];
				else
					$s .= "none";
				$s .= ")";
			}
			else
				$s .= "No votes yet";
		}
		else {
			$rpic = ratingpic($row["rating"]);
			if (!isset($rpic))
				$s .= "invalid?";
			else
				$s .= "$rpic (" . $row["rating"] . " out of 5 with " . $row["numratings"] . " vote(s) total)";
		}
		$s .= "\n";
		$s .= "</td><td class=embedded>$spacer</td><td valign=\"top\" class=embedded>";
		if (!isset($CURUSER))
			$s .= "(<a href=\"account-login.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&amp;nowarn=1\">Log in</a> to rate it)";
		else {
			$ratings = array(
					5 => "Cool!",
					4 => "Pretty good",
					3 => "Decent",
					2 => "Pretty bad",
					1 => "Sucks!",
			);
			if (!$owned || $moderator) {
				$xres = mysql_query("SELECT rating, added FROM ratings WHERE torrent = $id AND user = " . $CURUSER["id"]);
				$xrow = mysql_fetch_array($xres);
				if ($xrow)
					$s .= "(you rated this torrent as \"" . $xrow["rating"] . " - " . $ratings[$xrow["rating"]] . "\")";
				else {
					$s .= "<form method=\"post\" action=\"take-rating.php\"><input type=\"hidden\" name=\"id\" value=\"$id\" />\n";
					$s .= "<select name=\"rating\">\n";
					$s .= "<option value=\"0\">(add rating)</option>\n";
					foreach ($ratings as $k => $v) {
						$s .= "<option value=\"$k\">$k - $v</option>\n";
					}
					$s .= "</select>\n";
					$s .= "<input type=\"submit\" value=\"Vote!\" />";
					$s .= "</form>\n";
				}
			}
		}
		$s .= "</td></tr></table>";
//END DEFINE RATING VARIABLE

$keepget = "";
$url = "torrents-edit.php?id=" . $row["id"];
	if (isset($_GET["returnto"])) {
		$addthis = "&amp;returnto=" . urlencode($_GET["returnto"]);
		$url .= $addthis;
		$keepget .= $addthis;
	}
		
$editlink = "a href=\"$url\" class=\"sublink\"";
if ($owned)
	$editit .= "| <$editlink> [" . EDIT_TORRENT . "]</a>";

//progress bar
$seedersProgressbar = array();
$leechersProgressbar = array();
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
//end progress bar

//START OF PAGE LAYOUT HERE
begin_frame("" . TORRENT_DETAILS_FOR . " \"" . $row["name"] . "\"");

echo "<TABLE BORDER=0 WIDTH=100%><TR><TD ALIGN=RIGHT><a href=report.php?torrent=$id>" . REPORT_TORRENT . "</a> " . $editit . "</TD></TR></TABLE>";

echo "<BR><table cellpadding=3 width=100% border=0>";
echo "<TR><TD width=70% align=left valign=top><table width=100% cellspacing=0 cellpadding=3 border=0>";

print("<tr><td align=left colspan=2><b>" . TDESC . ":</b><br>" .  format_comment($row['descr']) . "</td></tr>");

print("<tr><td align=left><b>" . NAME . ":</b></td><td>" . h($row["name"]) . "</td></tr>");

print("<tr><td align=left><b>" . TORRENT . ":</b></td><td><a href=\"download.php?id=$id&name=" . rawurlencode($row["filename"]) . "\">" . h($row["filename"]) . "</a></td></tr>");

print("<tr><td align=left><b>" . TTYPE . ":</b></td><td>" . $row["cat_name"] . "</td></tr>");

print("<tr><td align=left><b>" . TOTAL_SIZE . ":</b></td><td>" . mksize($row["size"]) . " </td></tr>");

print("<tr><td align=left><b>" . INFO_HASH . ":</b></td><td>" . $row["info_hash"] . "</td></tr>");
		
if($row["privacy"] == "strong" && get_user_class() < UC_JMODERATOR AND $CURUSER["id"] != $row["owner"]){
print("<tr><td align=left><b>" . ADDED_BY . ":</b></td><td>Anonymous</td></tr>");
}else{
print("<tr><td align=left><b>" . ADDED_BY . ":</b></td><td><a href=account-details.php?id=" . $row["owner"] . ">" . $row["username"] . "</a></td></tr>");
}

print("<tr><td align=left><b>" . DATE_ADDED . ":</b></td><td>" . $row["added"] . "</td></tr>");
print("<tr><td align=left><b>" . VIEWS . ":</b></td><td>" . $row["views"] . "</td></tr>");
print("<tr><td align=left><b>" . HITS . ":</b></td><td>" . $row["hits"] . "</td></tr>");
print("<tr><td align=left><b>" . RATINGS . ":</b></td><td>" . $s . "</td></tr>");

echo "</table></TD><TD align=right valign=top><table width=100% cellspacing=0 cellpadding=3 border=0>";

if ($row["banned"] == "yes"){
	print ("<tr><td valign=top align=right><B>" . DOWNLOAD . ": </B>BANNED!</td></tr>");
}else{
	print ("<tr><td valign=top align=right><a href=\"download.php?id=$id&name=" . rawurlencode($row["filename"]) . "\"><img src=images/download.png border=0></td></tr>");
}

print("<tr><td valign=top align=right><B>" . AVAILABILITY . ":</B><br>" . get_percent_completed_image(floor($progressTotal)) . " (".round($progressTotal)."%)</td></tr>");
print("<tr><td valign=top align=right><B>" . SEEDS . ": <font color=green>" . $row["seeders"] . "</font></B></td></tr>");
print("<tr><td valign=top align=right><B>" . LEECH . ": <font color=red>" . $row["leechers"] . "</font></B></td></tr>");
//speed mod
$resSpeed = mysql_query("SELECT seeders,leechers FROM torrents WHERE visible='yes' and id = $id ORDER BY added DESC LIMIT 15") or sqlerr(__FILE__, __LINE__); 
if ($rowTmp = mysql_fetch_row($resSpeed))
       list($seedersTmp,$leechersTmp) = $rowTmp;  
if ($seedersTmp >= 1 && $leechersTmp >= 1){ 
       $speedQ = mysql_query("SELECT (t.size * t.times_completed + SUM(p.downloaded)) / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS totalspeed FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' AND p.torrent = '$id' GROUP BY t.id ORDER BY added ASC LIMIT 15") or sqlerr(__FILE__, __LINE__); 
       $a = mysql_fetch_assoc($speedQ); 
       $totalspeed = mksize($a["totalspeed"]) . "/s"; 
} 
else 
       $totalspeed = "No traffic currently recorded";  
print("<tr><td valign=top align=right><B>Total Speed: <font color=green>");
echo $totalspeed;
print("</font></B></td></tr>");
//end speed mod
print("<tr><td valign=top align=right><B>" . COMPLETED . ": " . $row["times_completed"] . "</B></td></tr>");
//print("<tr><td valign=top align=right><a href=completed.php?id=" . $id . ">[" . SEE_WHO_COMPLETED . "]</a></td></tr>");
print("<tr><td valign=top align=right><a href=completed-advance.php?id=" . $id . ">[" . SEE_WHO_COMPLETED . "]</a></td></tr>");
print("<tr><td valign=top align=right><B>" . LAST_SEEDED . ": </b>" . mkprettytime($row["lastseed"]) . " ago</td></tr>");

if ($row['seeders'] < 3 && $row['times_completed'] >= 1){
	print("<tr><td valign=top align=right><B>Request a re-seed: </b><a href=re-seed.php?id=" . $id . ">[SEND REQUEST!]</a></td></tr>");
}

echo "</table>";

if (get_user_class() >= UC_JMODERATOR) {
	echo "<br><BR><table width=100% cellspacing=0 cellpadding=3 style='border-collapse: collapse' bordercolor=#33CC00 border=1>";
	print("<tr><td valign=top align=center><B>" . MODERATOR_ONLY . "</B></td></tr>");

	echo "<br /><br />";
    print("<tr><td><form method=\"post\" action=\"torrents-delete.php\">\n");
    print("<input type=\"hidden\" name=\"id\" value=\"$id\">\n");
    if (isset($_GET["returnto"]))
        print("<input type=\"hidden\" name=\"returnto\" value=\"" . h($_GET["returnto"]) . "\" />\n");
    print("<B>" . REASON_FOR_DELETE . ":</B> <input type=text size=33 name=reason> <input type=submit value='" . DELETE_IT . "' style='height: 25px'>\n");
    print("</form>\n");
    print("</p>\n");
	print("</td></tr>");

	print("<tr><td valign=top align=left><B>" . BANNED . ": </B>" . $row["banned"] . "<br><B>" . VISIBLE . ": </B>" . $row["visible"] . "</td></tr>");

////////
	if (get_user_class() >= UC_JMODERATOR){        
		if (!$_GET["ratings"])
                print("<tr><td valign=top align=left><B>" . RATINGS . "</B> (" . $row["numratings"] . ") &nbsp; <a href=\"torrents-details.php?id=$id&amp;ratings=1$keepget#ratings\">[See Who Rated]</a>");
          else {
			  print("<tr><td valign=top align=left><B>" . RATINGS . "</B> (" . $row["numratings"] . ")");

			$s = "<table border=0 cellspacing=0 cellpadding=2>\n";
            $subres = mysql_query("SELECT * FROM ratings WHERE torrent = $id ORDER BY user");

			$s.="<tr><td><B>User</B></td><td align=right><B>Rated This</B></td></tr>\n";

            while ($subrow = mysql_fetch_array($subres)) {
				$ratingid=$subrow["user"];
				$sd=mysql_query("SELECT username FROM users WHERE id=$ratingid");
				$fetched_result = mysql_fetch_array($sd);
				$sd = $fetched_result['username'];
                $s .= "<tr><td><a href=account-details.php?id=$ratingid>" . $sd .
                "</a></td><td align=\"right\">" . $subrow["rating"] . "</td></tr>\n";
            }

			$s .= "</table>\n";

			print("<tr><td valign=top align=left>" .  $s . "<BR><a name=\"filelist\"><a href=\"torrents-details.php?id=$id$keepget\">[Hide list]</a>");
            }
	}
/////////
	echo "</table>";
}

echo "</td></tr></table>";

echo "<table width=100%>";
//DO FILE LIST STUFF
if ($row["type"] == "multi") {
			if (!$_GET["filelist"]){
				print("<tr><td valign=top align=left><B>" . FILE_LIST . ": </b><a href=\"torrents-details.php?id=$id&amp;filelist=1$keepget#filelist\" class=\"sublink\">[" . SHOW . "]</a></td></tr>");
			}else {
				print("<tr><td valign=top align=left><B>" . FILE_LIST . ": </b></tr>");

				$s = "<table class=main border=\"1\" cellspacing=0 cellpadding=\"5\">\n";

				$subres = mysql_query("SELECT * FROM files WHERE torrent = $id ORDER BY id");
$s.="<tr><td class=colhead>" . PATH . "</td><td class=colhead align=left>" . SIZE . "</td></tr>\n";
				while ($subrow = mysql_fetch_array($subres)) {
					$s .= "<tr><td>" . $subrow["filename"] .
                            "</td><td class=table_col2>" . mksize($subrow["size"]) . "</td></tr>\n";
				}

				$s .= "</table>\n";
				tr("<a name=\"filelist\">" . FILE_LIST . "</a><br /><a href=\"torrents-details.php?id=$id$keepget\" class=\"sublink\">[" . HIDE . "]</a>", $s, 1);
			}
}

//DO PEERS LIST STUFF
if (!$_GET["dllist"]) {
	$subres = mysql_query("SELECT seeder, COUNT(*) FROM peers WHERE torrent = $id GROUP BY seeder");
	$resarr = array(yes => 0, no => 0);
			$sum = 0;
			while ($subrow = mysql_fetch_array($subres)) {
				$resarr[$subrow[0]] = $subrow[1];
				$sum += $subrow[1];
			}
	print("<tr><td valign=top align=left><B>" . PEERS . ": $sum </b><a href=\"torrents-details.php?id=$id&amp;dllist=1$keepget#seeders\" class=\"sublink\">[" . SHOW . "]</a></td></tr>");
}else {
	$downloaders = array();
	$seeders = array();
	$subres = mysql_query("SELECT peer_id, client, seeder, ip, port, uploaded, downloaded, to_go, UNIX_TIMESTAMP(started) AS st, connectable, UNIX_TIMESTAMP(last_action) AS la FROM peers WHERE torrent = $id") or sqlerr();
		while ($subrow = mysql_fetch_array($subres)) {
				if ($subrow["seeder"] == "yes")
					$seeders[] = $subrow;
				else
					$downloaders[] = $subrow;
			}

function leech_sort($a,$b) {
  if ( isset( $_GET["usort"] ) ) return seed_sort($a,$b);
        $x = $a["to_go"];
		$y = $b["to_go"];
				if ($x == $y)
					return 0;
				if ($x < $y)
					return -1;
				return 1;
}
			
function seed_sort($a,$b) {
		$x = $a["uploaded"];
		$y = $b["uploaded"];
				if ($x == $y)
					return 0;
				if ($x < $y)
					return 1;
				return -1;
}

usort($seeders, "seed_sort");
usort($downloaders, "leech_sort");

print("<tr><td valign=top align=left><B>" . SEEDS . " </b>" . dltable(" " . SEEDS . "(s) <a href=\"torrents-details.php?id=$id$keepget\" class=\"sublink\">[" . HIDE. "]</a>", $seeders, $row) . " </td></tr>");

print("<tr><td valign=top align=left><B>" . LEECH . " </b>" . dltable(" " . LEECH . "(s) <a href=\"torrents-details.php?id=$id$keepget\" class=\"sublink\">[" . HIDE . "]</a>", $downloaders, $row) . " </td></tr>");

		}
	}
echo "</table>";

echo "<BR><BR>";

//DISPLAY NFO BLOCK
$nfo = h($row["nfo"]);
//-----------------------------------------------
function my_nfo_translate($nfo)
{
        $trans = array(
        "\x80" => "&#199;", "\x81" => "&#252;", "\x82" => "&#233;", "\x83" => "&#226;", "\x84" => "&#228;", "\x85" => "&#224;", "\x86" => "&#229;", "\x87" => "&#231;", "\x88" => "&#234;", "\x89" => "&#235;", "\x8a" => "&#232;", "\x8b" => "&#239;", "\x8c" => "&#238;", "\x8d" => "&#236;", "\x8e" => "&#196;", "\x8f" => "&#197;", "\x90" => "&#201;",
        "\x91" => "&#230;", "\x92" => "&#198;", "\x93" => "&#244;", "\x94" => "&#246;", "\x95" => "&#242;", "\x96" => "&#251;", "\x97" => "&#249;", "\x98" => "&#255;", "\x99" => "&#214;", "\x9a" => "&#220;", "\x9b" => "&#162;", "\x9c" => "&#163;", "\x9d" => "&#165;", "\x9e" => "&#8359;", "\x9f" => "&#402;", "\xa0" => "&#225;", "\xa1" => "&#237;",
        "\xa2" => "&#243;", "\xa3" => "&#250;", "\xa4" => "&#241;", "\xa5" => "&#209;", "\xa6" => "&#170;", "\xa7" => "&#186;", "\xa8" => "&#191;", "\xa9" => "&#8976;", "\xaa" => "&#172;", "\xab" => "&#189;", "\xac" => "&#188;", "\xad" => "&#161;", "\xae" => "&#171;", "\xaf" => "&#187;", "\xb0" => "&#9617;", "\xb1" => "&#9618;", "\xb2" => "&#9619;",
        "\xb3" => "&#9474;", "\xb4" => "&#9508;", "\xb5" => "&#9569;", "\xb6" => "&#9570;", "\xb7" => "&#9558;", "\xb8" => "&#9557;", "\xb9" => "&#9571;", "\xba" => "&#9553;", "\xbb" => "&#9559;", "\xbc" => "&#9565;", "\xbd" => "&#9564;", "\xbe" => "&#9563;", "\xbf" => "&#9488;", "\xc0" => "&#9492;", "\xc1" => "&#9524;", "\xc2" => "&#9516;", "\xc3" => "&#9500;",
        "\xc4" => "&#9472;", "\xc5" => "&#9532;", "\xc6" => "&#9566;", "\xc7" => "&#9567;", "\xc8" => "&#9562;", "\xc9" => "&#9556;", "\xca" => "&#9577;", "\xcb" => "&#9574;", "\xcc" => "&#9568;", "\xcd" => "&#9552;", "\xce" => "&#9580;", "\xcf" => "&#9575;", "\xd0" => "&#9576;", "\xd1" => "&#9572;", "\xd2" => "&#9573;", "\xd3" => "&#9561;", "\xd4" => "&#9560;",
        "\xd5" => "&#9554;", "\xd6" => "&#9555;", "\xd7" => "&#9579;", "\xd8" => "&#9578;", "\xd9" => "&#9496;", "\xda" => "&#9484;", "\xdb" => "&#9608;", "\xdc" => "&#9604;", "\xdd" => "&#9612;", "\xde" => "&#9616;", "\xdf" => "&#9600;", "\xe0" => "&#945;", "\xe1" => "&#223;", "\xe2" => "&#915;", "\xe3" => "&#960;", "\xe4" => "&#931;", "\xe5" => "&#963;",
        "\xe6" => "&#181;", "\xe7" => "&#964;", "\xe8" => "&#934;", "\xe9" => "&#920;", "\xea" => "&#937;", "\xeb" => "&#948;", "\xec" => "&#8734;", "\xed" => "&#966;", "\xee" => "&#949;", "\xef" => "&#8745;", "\xf0" => "&#8801;", "\xf1" => "&#177;", "\xf2" => "&#8805;", "\xf3" => "&#8804;", "\xf4" => "&#8992;", "\xf5" => "&#8993;", "\xf6" => "&#247;",
        "\xf7" => "&#8776;", "\xf8" => "&#176;", "\xf9" => "&#8729;", "\xfa" => "&#183;", "\xfb" => "&#8730;", "\xfc" => "&#8319;", "\xfd" => "&#178;", "\xfe" => "&#9632;", "\xff" => "&#160;",
        );
        $trans2 = array("\xe4" => "&auml;",        "\xF6" => "&ouml;",        "\xFC" => "&uuml;",        "\xC4" => "&Auml;",        "\xD6" => "&Ouml;",        "\xDC" => "&Uuml;",        "\xDF" => "&szlig;");
        $all_chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $last_was_ascii = False;
        $tmp = "";
        $nfo = $nfo . "\00";
        for ($i = 0; $i < (strlen($nfo) - 1); $i++)
        {
                $char = $nfo[$i];
                if (isset($trans2[$char]) and ($last_was_ascii or strpos($all_chars, ($nfo[$i + 1]))))
                {
                        $tmp = $tmp . $trans2[$char];
                        $last_was_ascii = True;
                }
                else
                {
                        if (isset($trans[$char]))
                        {
                                $tmp = $tmp . $trans[$char];
                        }
                        else
                        {
                            $tmp = $tmp . $char;
                        }
                        $last_was_ascii = strpos($all_chars, $char);
                }
        }
        return $tmp;
}
$nfo = my_nfo_translate($nfo);
//-----------------------------------------------

if (!$nfo) {
	print("<BR>");
}else{
    begin_frame("" . NFO . " for $row[name]</a>");
    begin_table();
        print("<tr><td class=alt2>\n");
   //     print("<br><pre><font face='MS Linedraw' size=2 style='font-size: 10pt; line-height: 10pt'>" . format_urls($nfo) . "</font></pre>\n");
     print("<br><pre>" . format_urls($nfo) . "</pre>\n");
    end_table();
        //print("<p align=center>" . FOR_BEST_RESULTS . "</p>\n");
    end_frame();
}


//start comments block
begin_frame("" . COMMENTS . "");
	print("<p><a name=\"startcomments\"></a></p>\n");

	$commentbar = "<p align=center><a class=index href=torrents-comment.php?id=$id>" . ADDCOMMENT . "</a></p>\n";

	$subres = mysql_query("SELECT COUNT(*) FROM comments WHERE torrent = $id");
	$subrow = mysql_fetch_array($subres);
	$count = $subrow[0];

	if (!$count) {
		print("<BR><b><CENTER>" . NOCOMMENTS . "</CENTER></b><BR>\n");
	}
	else {
		list($pagertop, $pagerbottom, $limit) = pager(20, $count, "torrents-details.php?id=$id&", array(lastpagedefault => 1));

		$subres = mysql_query("SELECT comments.id, text, user, comments.added, avatar, signature, ".
                  "username, title, class, uploaded, downloaded, privacy, donated FROM comments LEFT JOIN users ON comments.user = users.id WHERE torrent = " .
                  "$id ORDER BY comments.id $limit");
		$allrows = array();
		while ($subrow = mysql_fetch_array($subres))
			$allrows[] = $subrow;

		print($commentbar);
		print($pagertop);

		commenttable($allrows);

		print($pagerbottom);
	}

	print($commentbar);
	end_frame();
}
end_frame();

stdfoot();


?>