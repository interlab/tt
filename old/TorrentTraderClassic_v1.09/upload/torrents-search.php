<?php
//
// - Theme And Language Updated 25.Nov.05
//
ob_start("ob_gzhandler");
require_once("backend/functions.php");
dbconn(false);
loggedinorreturn();
if ($RATIO_WARNINGON && $CURUSER)
{
    include("ratiowarn.php");
}

$searchstr = unesc($_GET["search"]);
$cleansearchstr = searchfield($searchstr);
if (empty($cleansearchstr))
unset($cleansearchstr);

///////////////////////////MOD FOR SORTING///////////////////////////////////
if ($_GET['sort'] && $_GET['type']) {

$column = '';
$ascdesc = '';

switch($_GET['sort']) {
 case '1': $column = "name"; break;
 case '2': $column = "nfo"; break;
 case '3': $column = "Comments"; break;
 case '4': $column = "size"; break;
 case '5': $column = "times_completed"; break;
 case '6': $column = "seeders"; break;
 case '7': $column = "leechers"; break;
 case '8': $column = "category"; break;
 default: $column = "id"; break;
}

switch($_GET['type']) {
 case 'asc': $ascdesc = "ASC"; break;
 case 'desc': $ascdesc = "DESC"; break;
 default: $ascdesc = "DESC"; break;
}

$orderby = "ORDER BY torrents." . $column . " " . $ascdesc;
$pagerlink = "sort=" . $_GET['sort'] . "&type=" . $_GET['type'] . "&";

} else {


$pagerlink = "";
$orderby = "ORDER BY torrents.id DESC";

}
////////////////////////END SORTING MOD/////////////////////////////////

//$orderby = "ORDER BY torrents.id DESC";  //REMOVE FOR SORT MOD

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
	//$orderby = "";  //REMOVE FOR SORT MOD
}
$where = implode(" AND ", $wherea);
if ($where != "")
$where = "WHERE $where";

$res = mysql_query("SELECT COUNT(*) FROM torrents $where")
or die(mysql_error());
$row = mysql_fetch_array($res);
$count = $row[0];

if (!$count && isset($cleansearchstr)) {
	$wherea = $wherebase;
	//$orderby = "ORDER BY id DESC";  //REMOVE FOR SORT MOD
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

if ($count) {
//////////////////////////////////SORT MOD///////////////////////////////////////
if ($addparam != "") { 
	if ($pagerlink != "") {
		if ($addparam{strlen($addparam)-1} != ";") { // & = &amp;
			$addparam = $addparam . "&" . $pagerlink;
		} else {
			$addparam = $addparam . $pagerlink;
		}
	}
} else {
	$addparam = $pagerlink;
}

//////////////////////////////////////END SORT MOD////////////////////////////////

	list($pagertop, $pagerbottom, $limit) = pager(13, $count, "torrents-search.php?$addparam");

	$query = "SELECT torrents.id, torrents.category, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.size, torrents.added, torrents.comments,torrents.numfiles,torrents.filename,torrents.owner,IF(torrents.nfo <> '', 1, 0) as nfoav," .

	"IF(torrents.numratings < $minvotes, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, categories.name AS cat_name, categories.image AS cat_pic, users.username, users.privacy FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id $where $orderby $limit";
	$res = mysql_query($query)
	or die(mysql_error());
}
else
unset($res);
if (isset($cleansearchstr))
stdhead("Search results for \"$searchstr\"");
else
stdhead("Browse Torrents");

$cats = genrelist();

$catdropdown = "";
foreach ($cats as $cat) {
	$catdropdown .= "<option value=\"" . $cat["id"] . "\"";
	if ($cat["id"] == $_GET["cat"])
	$catdropdown .= " selected=\"selected\"";
	$catdropdown .= ">" . htmlspecialchars($cat["name"]) . "</option>\n";
}

begin_frame("" . SEARCH_TITLE . "",center);

?><CENTER>
<form method="get" action="torrents-search.php"><br />
<?= $txt['SEARCH'] ?>
<input type="text" name="search" size="40" value="<?= h($searchstr) ?>" />
<?= $txt['IN'] ?>
<select name="cat">
<option value="0">(All types)</option>

<?php
$cats = genrelist();
$catdropdown = "";
foreach ($cats as $cat) {
    $catdropdown .= "<option value=\"" . $cat["id"] . "\"";
    if ($cat["id"] == $_GET["cat"])
        $catdropdown .= " selected=\"selected\"";
    $catdropdown .= ">" . h($cat["name"]) . "</option>\n";
}

$deadchkbox = "<input type=\"checkbox\" name=\"incldead\" value=\"1\"";
if ($_GET["incldead"])
    $deadchkbox .= " checked=\"checked\"";
$deadchkbox .= " /> " . INC_DEAD . "\n";

?>
<?= $catdropdown ?>
</select>
<?= $deadchkbox ?>
<input type="submit" value="<?= $txt['SEARCH'] ?>" />
</form><br><hr><br>
<?= $txt['SHOW_ALL'] ?>
<form method="get" action="torrents-search.php">
<select name="cat">
<option value="0">(Any type)</option>
<?= $catdropdown ?>
</select>



<select name=incldead>
<option value="0">Active</option>
<option value="1">Including dead</option>
<option value="2">Only dead</option>
</select>
<input type="submit" class=btn value="<?= $txt['DISPLAY'] ?>" style="margin-left: 10px"/>
</form>
</CENTER>

<?php
if ($count) {
		end_frame();
		echo "<br /><br />\n";
		begin_frame("" . SEARCH_RESULTS . "");
		print($pagertop);
		torrenttable($res);
		print($pagerbottom);
}
else {
	if (isset($cleansearchstr)) {
		bark2("" . NOTHING_FOUND . "", "" . NO_UPLOADS . "");
	}
	else {
		bark2("" . NOTHING_FOUND . "", "" . NO_RESULTS . "");
	}
}

end_frame();
stdfoot();
?>
