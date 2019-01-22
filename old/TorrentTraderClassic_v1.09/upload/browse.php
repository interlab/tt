<?
//
// - Theme And Language Updated 25.Nov.05
//
ob_start("ob_gzhandler");
require_once("backend/functions.php");

dbconn(false);

if ($LOGGEDINONLY){
  loggedinorreturn();
}

if ($RATIO_WARNINGON && $CURUSER)
{
    include("ratiowarn.php");
}

function getmicrotime(){
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}

$time_start = getmicrotime();

$cats = genrelist();

$searchstr = unesc($_GET["search"]);
$cleansearchstr = searchfield($searchstr);
if (empty($cleansearchstr))
unset($cleansearchstr);

$orderby = "ORDER BY torrents.id DESC";

$addparam = "";
$wherea = array();
$wherecatina = array();
$wherecatin = "";


$category = (int)$_GET["cat"];

$all = $_GET["all"];

if (!$all)
if (!$_GET && $CURUSER["notifs"])
{
  $all = True;
  foreach ($cats as $cat)
  {
    $all &= $cat[id];
    if (strpos($CURUSER["notifs"], "[cat" . $cat[id] . "]") !== False)
    {
      $wherecatina[] = $cat[id];
      $addparam .= "c$cat[id]=1&amp;";
    }
  }
}
elseif ($category)
{
  if (!is_valid_id($category))
    stderr("Error", "Invalid category ID $category.");
  $wherecatina[] = $category;
  $addparam .= "cat=$category&amp;";
}
else
{
  $all = True;
  foreach ($cats as $cat)
  {
    $all &= $_GET["c$cat[id]"];
    if ($_GET["c$cat[id]"])
    {
      $wherecatina[] = $cat[id];
      $addparam .= "c$cat[id]=1&amp;";
    }
  }
}

if ($all)
{
$wherecatina = array();
 $addparam = "";
}

if (count($wherecatina) > 1)
$wherecatin = implode(",",$wherecatina);
elseif (count($wherecatina) == 1)
$wherea[] = "category = $wherecatina[0]";

$wherebase = $wherea;

if ($_GET["incldead"] == 2)
$wherea[] = "visible = 'no'";
else
$wherea[] = "visible = 'yes'";

if (isset($cleansearchstr))
{
$wherea[] = "MATCH (search_text, ori_descr) AGAINST (" . sqlesc($searchstr) . ")";
//$wherea[] = "0";
$addparam .= "search=" . urlencode($searchstr) . "&amp;";
$orderby = "";
}

$where = implode(" AND ", $wherea);
if ($wherecatin)
$where .= ($where ? " AND " : "") . "category IN(" . $wherecatin . ")";

if ($where != "")
$where = "WHERE $where";

$res = mysql_query("SELECT COUNT(*) FROM torrents $where") or die(mysql_error());
$row = mysql_fetch_array($res);
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
 $res = mysql_query("SELECT COUNT(*) FROM torrents $where");
 $row = mysql_fetch_array($res);
 $count = $row[0];
}
}

if ($_GET['sort']) {
if ($addparam != "") $addparam .= "&";
$addparam .= "sort=$_GET[sort]";
}
if ($_GET['type']) $addparam .= "&type=$_GET[type]&";
if ($count)
{
list($pagertop, $pagerbottom, $limit) = pager(25, $count, "browse.php?" . $addparam);
	$query = "SELECT torrents.id, torrents.category, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.size,torrents.added, torrents.comments,torrents.numfiles,torrents.filename,torrents.owner,IF(torrents.nfo <> '', 1, 0) as nfoav," .

	"IF(torrents.numratings < $minvotes, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, categories.name AS cat_name, categories.image AS cat_pic, users.username, users.privacy FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id $where $orderby $limit";
$res = mysql_query($query) or die(mysql_error());
}
else
unset($res);


if (isset($cleansearchstr))
stdhead("Search results for \"$searchstr\"");
else
stdhead();

begin_frame("" . BROWSE_TORRENTS . "", center);
?>


<form method="get" action="browse.php">
<table align="center" class=bottom>
<tr>
 <td class=bottom>
  <table class=bottom align="center">
   <tr align='right'>

<?
$i = 0;
foreach ($cats as $cat)
{
$catsperrow = 7;
print(($i && $i % $catsperrow == 0) ? "</tr><tr align='right'>" : "");
print("<td style=\"padding-bottom: 2px;padding-left: 2px\"><a class=catlink href=browse.php?cat={$cat["id"]}>" . htmlspecialchars($cat["name"]) . "</a><input name=c{$cat["id"]} type=\"checkbox\" " . (in_array($cat["id"], $wherecatina) ? "checked " : "") . "value=1></td>\n");
$i++;
}

$alllink = "<div align=left>(<a href=browse.php?all=1><b>Show all</b></a>)</div>";

$ncats = count($cats);
$nrows = ceil($ncats/$catsperrow);
$lastrowcols = $ncats % $catsperrow;

if ($lastrowcols != 0)
{
if ($catsperrow - $lastrowcols != 1)
 {
  print("<td class=bottom rowspan=" . ($catsperrow  - $lastrowcols - 1) . ">&nbsp;</td>");
 }
print("<td class=bottom style=\"padding-left: 5px\">$alllink</td>\n");
}
?>
   </tr>
  </table>
 </td>
</tr> 
<?
if ($ncats % $catsperrow == 0)
print("<tr><td class=bottom style=\"padding-left: 15px\" rowspan=$nrows valign=center align=right>$alllink</td></tr>\n");
?>  
 <tr>
  <td class=bottom style="padding: 1px;padding-left: 10px">
  <div align=center>
   <input type="submit" class=btn value="Go!"/>
  </div>
  </td>
 </tr>
</table>
</form>

<br>

<?

if (isset($cleansearchstr))
print("<h2>Search results for \"" . htmlspecialchars($searchstr) . "\"</h2>\n");

if (!$LOGGEDINONLY){
	if ($count) {
			torrenttable($res);
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
				torrenttable($res);
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

end_Frame();
//update users last browse time
//REMOVE THIS IF YOUR LOAD IS HIGH.
mysql_query("UPDATE users SET last_browse=".gmtime()." where id=".$CURUSER['id']);

stdfoot();

?>
