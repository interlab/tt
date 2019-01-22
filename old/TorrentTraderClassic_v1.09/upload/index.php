<?
//
// - Theme And Language Updated 25.Nov.05
//
ob_start("ob_gzhandler");
require_once("backend/functions.php");
dbconn(true);
if ($RATIO_WARNINGON && $CURUSER)
{
    include("ratiowarn.php");
}


if ($_SERVER["REQUEST_METHOD"] == "POST")
{
  $choice = (int) $_POST["choice"];
  if ($CURUSER && $choice > 0 && $choice < 256)
  {
    $res = mysql_query("SELECT * FROM polls ORDER BY added DESC LIMIT 1") or sqlerr();
    $arr = mysql_fetch_assoc($res) or die("No poll");
    $pollid = $arr["id"];
    $userid = $CURUSER["id"];
    $res = mysql_query("SELECT * FROM pollanswers WHERE pollid=$pollid && userid=$userid") or sqlerr();
    $arr = mysql_fetch_assoc($res);
    if ($arr) die("Dupe vote");
    mysql_query("INSERT INTO pollanswers VALUES(0, $pollid, $userid, $choice)") or sqlerr();
    if (mysql_affected_rows() != 1)
      stderr("Error", "An error occured. Your vote has not been counted.");
    header("Location: $SITEURL/");
    die;
  }
  else
    stderr("Error", "Please select an option.");
}

function getmicrotime(){
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
    }
$time_start = getmicrotime();


$searchstr = unesc($_GET["search"]);
$cleansearchstr = searchfield($searchstr);
if (empty($cleansearchstr))
	unset($cleansearchstr);

$searchstr = unesc($_GET["search"]);
$cleansearchstr = searchfield($searchstr);
if (empty($cleansearchstr))
unset($cleansearchstr);

$orderby = "ORDER BY torrents.id DESC";

$addparam = "";
$wherea = array();

if ($_GET["incldead"] == 1) {
	$addparam .= "incldead=1&amp;";
	if (!isset($CURUSER) || get_user_class < UC_ADMINISTRATOR)
	$wherea[] = "banned != 'yes'";
}
else
if ($_GET["incldead"] == 2)
$wherea[] = "visible = 'no'";
else
$wherea[] = "visible = 'yes'";

if ($_GET["cat"]) {
	$wherea[] = "category = " . sqlesc($_GET["cat"]);
	$addparam .= "cat=" . urlencode($_GET["cat"]) . "&amp;";
}
$wherebase = $wherea;
if (isset($cleansearchstr)) {
	$wherea[] = "MATCH (search_text, ori_descr) AGAINST (" . sqlesc($searchstr) . ")";
	$addparam .= "search=" . urlencode($searchstr) . "&amp;";
	$orderby = "";
}
$where = implode(" AND ", $wherea);
if ($where != "")
$where = "WHERE $where";

$tor = mysql_query("SELECT COUNT(*) FROM torrents $where")
or die(mysql_error());
$row = mysql_fetch_array($tor);
$count = $row[0];

if (!$count && isset($cleansearchstr)) {
	$wherea = $wherebase;
	$orderby = "ORDER BY id DESC";
	$searcha = explode(" ", $cleansearchstr);
	$sc = 0;
	foreach ($searcha as $searchss) {
		if (strlen($searchss) <= 1)
		continue;
		$sc++;
		if ($sc > 5)
		break;
		$ssa = array();
		foreach (array("search_text", "ori_descr") as $sss)
		$ssa[] = "$sss LIKE '%" . sqlwildcardesc($searchss) . "%'";
		$wherea[] = "(" . implode(" OR ", $ssa) . ")";
	}
	if ($sc) {
		$where = implode(" AND ", $wherea);
		if ($where != "")
		$where = "WHERE $where";
		$tor = mysql_query("SELECT COUNT(*) FROM torrents $where");
		$row = mysql_fetch_array($tor);
		$count = $row[0];
	}
}

if ($count) {
	list($pagertop, $pagerbottom, $limit) = pager(25, $count, "browse.php?" . $addparam);

	$query = "SELECT torrents.id, torrents.category, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.size,torrents.added, torrents.comments,torrents.numfiles,torrents.filename,torrents.owner,IF(torrents.nfo <> '', 1, 0) as nfoav," .

	"IF(torrents.numratings < $minvotes, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, categories.name AS cat_name, categories.image AS cat_pic, users.username, users.privacy FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id $where $orderby $limit";
	$tor = mysql_query($query)
	or die(mysql_error());
}
else
unset($tor);

stdhead();

$cats = genrelist();

$catdropdown = "";
foreach ($cats as $cat) {
	$catdropdown .= "<option value=\"browse.php?cat=" . $cat["id"] . "\"";
	if ($cat["id"] == $_GET["cat"])
	$catdropdown .= " selected=\"selected\"";
	$catdropdown .= ">" . htmlspecialchars($cat["name"]) . "</option>\n";
}

//Here we decide if the site notice/welcome text is on or off
if ($SITENOTICEON)
{
begin_frame("" . NOTICE . "", center);
echo "<BR>$SITENOTICE<BR><BR>";
end_frame();
}

//NEWS BLOCK
if ($NEWSON)
{
begin_frame("". SITENEWS . "", center);
include "news.php";
end_frame();
}

if ($POLLON)
{
begin_frame("Poll", center);
if ($CURUSER)
{
  // Get current poll
  $res = mysql_query("SELECT * FROM polls ORDER BY added DESC LIMIT 1") or sqlerr();
  if($pollok=(mysql_num_rows($res)))
  {
  	$arr = mysql_fetch_assoc($res);
  	$pollid = $arr["id"];
  	$userid = $CURUSER["id"];
  	$question = $arr["question"];
  	$o = array($arr["option0"], $arr["option1"], $arr["option2"], $arr["option3"], $arr["option4"],
    	$arr["option5"], $arr["option6"], $arr["option7"], $arr["option8"], $arr["option9"],
    	$arr["option10"], $arr["option11"], $arr["option12"], $arr["option13"], $arr["option14"],
    	$arr["option15"], $arr["option16"], $arr["option17"], $arr["option18"], $arr["option19"]);

  // Check if user has already voted
  	$res = mysql_query("SELECT * FROM pollanswers WHERE pollid=$pollid AND userid=$userid") or sqlerr();
  	$arr2 = mysql_fetch_assoc($res);
  }


  if (get_user_class() >= UC_MODERATOR)
  {
  	print("<div align=right><font class=small>");
		print("Moderator Options - [<a class=altlink href=makepoll.php?returnto=main><b>New</b></a>]\n");
		if($pollok) {
  		print(" - [<a class=altlink href=makepoll.php?action=edit&pollid=$arr[id]&returnto=main><b>Edit</b></a>]\n");
			print(" - [<a class=altlink href=polls.php?action=delete&pollid=$arr[id]&returnto=main><b>Delete</b></a>]");
		}
		print("</font></div>");
	}
	print("\n");
	if($pollok) {
    //begin_table();
    print("<table width=400 class=main border=1 cellspacing=0 cellpadding=5 align=center><tr><td class=text>\n");
  	print("<p align=center><b>$question</b></p>\n");
  	$voted = $arr2;
  	if ($voted)
  	{
    	// display results
    	if ($arr["selection"])
      	$uservote = $arr["selection"];
    	else
      	$uservote = -1;
			// we reserve 255 for blank vote.
    	$res = mysql_query("SELECT selection FROM pollanswers WHERE pollid=$pollid AND selection < 20") or sqlerr();

    	$tvotes = mysql_num_rows($res);

    	$vs = array(); // array of
    	$os = array();

    	// Count votes
    	while ($arr2 = mysql_fetch_row($res))
      	$vs[$arr2[0]] += 1;

    	reset($o);
    	for ($i = 0; $i < count($o); ++$i)
      	if ($o[$i])
        	$os[$i] = array($vs[$i], $o[$i]);

    	function srt($a,$b)
    	{
      	if ($a[0] > $b[0]) return -1;
      	if ($a[0] < $b[0]) return 1;
      	return 0;
    	}

    	// now os is an array like this: array(array(123, "Option 1"), array(45, "Option 2"))
    	if ($arr["sort"] == "yes")
    		usort($os, srt);

    	print("<table class=main width=400 border=0 cellspacing=0 cellpadding=0>\n");
    	$i = 0;
    	while ($a = $os[$i])
    	{
      	if ($i == $uservote)
        	$a[1] .= "&nbsp;*";
      	if ($tvotes == 0)
      		$p = 0;
      	else
      		$p = round($a[0] / $tvotes * 100);
      	if ($i % 2)
        	$c = "";
      	else
        	$c = "";
      	print("<tr><td width=1% class=embedded$c><nobr>" . $a[1] . "&nbsp;&nbsp;</nobr></td><td width=99% class=embedded$c>" .
        	"<img src=$SITEURL/images/bar_left.gif><img src=$SITEURL/images/bar.gif height=9 width=" . ($p * 3) .
        	"><img src=$SITEURL/images/bar_right.gif> $p%</td></tr>\n");
      	++$i;
    	}
    	print("</table>\n");
			$tvotes = number_format($tvotes);
    	print("<p align=center>Votes: $tvotes</p>\n");
        print("</table>");
  	}
  	else
  	{
    	print("<form method=post action=index.php>\n");
    	$i = 0;
    	while ($a = $o[$i])
    	{
      	print("<input type=radio name=choice value=$i>$a<br>\n");
      	++$i;
    	}
    	print("<br>");
    	//print("<input type=radio name=choice value=255>View Results<br>\n");
    	print("<div align=center><input type=submit value='Vote' class=btn><br><a href=polls.php>View Results</a></div></table>");
  	}
    //end_table();
    }

}else{
    echo "You must log in to vote and view the poll";
}
end_frame();
}

begin_frame("" . TORRENT_CATEGORIES . "", center);
print "<B><font color=#FF6600>�</font></B> <a href=browse.php>" . BROWSE_TORRENTS . "</a> ";
print "<B><font color=#FF6600>�</font></B> <a href=today.php>" . TODAYS_TORRENTS . "</a> ";
print "<B><font color=#FF6600>�</font></B> <a href=torrents-search.php>" . SEARCH . "</a><hr>";

$rq = "SELECT id, name FROM categories ORDER BY sort_index, id";
$resq = mysql_query($rq);
 while ($row = mysql_fetch_array($resq))
{
 extract ($row);
//comment out the get_row_count part if high server load

 $newcount = get_row_count("torrents", "WHERE UNIX_TIMESTAMP(" . get_dt_num() . ") - UNIX_TIMESTAMP(added) < 3600 AND category = '" . $id . "'");
   if($newcount > 0){
  echo "<B><font color=#FF6600>�</font></B> <a href=\"browse.php?cat=$id\">$name</a> ($newcount) \n";
		}else{
  echo "<B><font color=#FF6600>�</font></B> <a href=\"browse.php?cat=$id\">$name</a> \n";
	}
}
print "<br><br>";
end_frame();

begin_frame("" . BROWSE_TORRENTS . "", center);

//$date=gmdate("D M Y H:i");
$date=gmdate("D M Y H:i", time() + $CURUSER['tzoffset'] * 60);

?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td colspan="2"><img border="0" src="images/space.gif" width="8" height="5"></td></tr>
<tr>
<td><b><?= $date ?></b></td>
<form name="jump">
<td style="border-style: none; border-width: medium" align="right">
<select name="menu" onChange="location=document.jump.menu.options[document.jump.menu.selectedIndex].value;" value="GO" style="font-family: Verdana; font-size: 8pt; border: 1px solid #000000; background-color: #CCCCCC" size="1">
<option value="#"><? print("" . CATEGORIES . "\n"); ?></option>
<?= $catdropdown ?>
<option value=browse.php><? print("" . SHOWALL . "\n"); ?></option>
</select></td></form>
</tr>
<tr>
<td vAlign=top colspan="2" width=100%>
<?
if (!$LOGGEDINONLY){
	if ($count) {
			torrenttable($tor);
			print($pagerbottom);
	}else {
		if (isset($cleansearchstr)) {
			bark2("" . NOTHING_FOUND . "", "" . NO_UPLOADS . "");
		}else{
			bark2("" . NOTHING_FOUND . "", "" . NO_RESULTS . "");
		}
	}
}//end 

if ($LOGGEDINONLY){
	if (!$CURUSER){
		echo "<BR><BR><b><CENTER>You Are Not Logged In<br>Only Members Can View Torrents Please Signup.</CENTER><BR><BR>";
	}else{
		if ($count) {
				torrenttable($tor);
				print($pagerbottom);
		}else {
			if (isset($cleansearchstr)) {
				bark2("" . NOTHING_FOUND . "", "" . NO_UPLOADS . "");
			}else{
				bark2("" . NOTHING_FOUND . "", "" . NO_RESULTS . "");
		}
	}
	}
}//end 


?>
</td></tr></table>
<?
end_frame();

//Here we decide if the shoutbox is on or off
if ($SHOUTBOX)
{
begin_frame("" . SHOUTBOX . "", center);
echo '<IFRAME name="shout_frame" src="'.$SITEURL.'/ttshout.php" frameborder="0" marginheight="0" marginwidth="0" width="95%" height="210" scrolling="no" align="middle"></IFRAME>';
end_frame();
}


//Here we decide if the block is on or off
if ($DISCLAIMERON)
{
begin_frame("" . DISCLAIMER . ""); 
echo file_get_contents("disclaimer.txt") ;
end_frame();
}

//update users last browse time
//REMOVE THIS IF YOUR LOAD IS HIGH.
mysql_query("UPDATE users SET last_browse=".gmtime()." where id=".$CURUSER['id']);

stdfoot();
hit_end();
?>