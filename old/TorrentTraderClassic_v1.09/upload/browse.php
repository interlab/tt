<?php
//
// - Theme And Language Updated 25.Nov.05
//
ob_start("ob_gzhandler");
require_once 'backend/functions.php';

dbconn(false);

global $CURUSER, $LOGGEDINONLY, $SPECIAL_FREE_VIEW, $no_check_referer, $RATIO_WARNINGON;
global $RATIOWARN_TIME, $RATIOWARN_BAN, $RATIOWARN_AMMOUNT, $minvotes;

if ($LOGGEDINONLY) {
    loggedinorreturn();
}

if ($RATIO_WARNINGON && $CURUSER) {
    require_once ST_ROOT_DIR.'/ratiowarn.php';
}

$time_start = getmicrotime();

$cats = genrelist();

$searchstr = unesc($_GET["search"] ?? '');
$cleansearchstr = searchfield($searchstr);
if (empty($cleansearchstr)) {
    unset($cleansearchstr);
}

$orderby = "ORDER BY torrents.id DESC";

$addparam = '';
$wherea = [];
$wherecatina = [];
$wherecatin = '';

$_GET["incldead"] = $_GET["incldead"] ?? '';
$_GET['sort'] = $_GET['sort'] ?? '';
$_GET['type'] = $_GET['type'] ?? '';

$category = (int) ($_GET["cat"] ?? 0);

$all = $_GET["all"] ?? '';

if (!$all && (!$_GET && $CURUSER["notifs"])) { // todo: check logik
    $all = true;
    foreach ($cats as $cat) {
        $all &= $cat['id'];
        if (strpos($CURUSER["notifs"], '[cat' . $cat['id'] . ']') !== false) {
            $wherecatina[] = (int) $cat['id'];
            $addparam .= "c$cat[id]=1&amp;";
        }
    }
}
elseif ($category) {
    if (!is_valid_id($category)) {
        stderr("Error", "Invalid category ID $category.");
    }
    $wherecatina[] = $category;
    $addparam .= "cat=$category&amp;";
}
else {
    $all = true;
    foreach ($cats as $cat) {
        if (isset($_GET["c$cat[id]"])) {
            $all = false;
            $wherecatina[] = (int) $cat['id'];
            $addparam .= "c$cat[id]=1&amp;";
        }
    }
}

if ($all) {
    $wherecatina = [];
    $addparam = '';
}

if (count($wherecatina) > 1)
    $wherecatin = implode(',', $wherecatina);
elseif (count($wherecatina) == 1)
    $wherea[] = 'category = ' . $wherecatina[0];

$wherebase = $wherea;

if ($_GET["incldead"] == 2)
    $wherea[] = "visible = 'no'";
else
    $wherea[] = "visible = 'yes'";

if (isset($cleansearchstr)) {
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

// dump($where);

$count = DB::fetchColumn('SELECT COUNT(*) FROM torrents ' . $where);

if (!$count && isset($cleansearchstr)) {
    $wherea = $wherebase;
    $orderby = "ORDER BY id DESC";
    $searcha = explode(" ", $cleansearchstr);
    $sc = 0;
    foreach ($searcha as $searchss) {
        if (strlen($searchss) <= 1) {
            continue;
        }
        $sc++;
        if ($sc > 5) {
            break;
        }
        $ssa = [];
        foreach (array("search_text", "ori_descr") as $sss)
            $ssa[] = "$sss LIKE '%" . sqlwildcardesc($searchss) . "%'";
        $wherea[] = "(" . implode(" OR ", $ssa) . ")";
    }
    if ($sc) {
        $where = implode(" AND ", $wherea);
        if ($where != "") {
            $where = "WHERE $where";
        }
        $count = DB::fetchColumn('SELECT COUNT(*) FROM torrents ' . $where);
    }
}

if ($_GET['sort']) {
    if ($addparam != '') $addparam .= "&";
        $addparam .= "sort=$_GET[sort]";
}
if ($_GET['type'])
    $addparam .= "&type=$_GET[type]&";
if ($count) {
    list($pagertop, $pagerbottom, $limit) = pager(25, $count, "browse.php?" . $addparam);
	$query = "SELECT torrents.id, torrents.category, torrents.leechers, torrents.nfo, torrents.seeders, 
    torrents.name, torrents.times_completed, torrents.size,
    torrents.added, torrents.comments,torrents.numfiles,torrents.filename,torrents.owner,IF(torrents.nfo <> '', 1, 0) as nfoav,
    IF(torrents.numratings < $minvotes, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, 
    categories.name AS cat_name, categories.image AS cat_pic, 
    users.username, users.privacy FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id 
    $where 
    $orderby 
    $limit";
    $res = DB::query($query);
}

if (isset($cleansearchstr))
    stdhead("Search results for \"$searchstr\"");
else
    stdhead();

begin_frame($txt['BROWSE_TORRENTS'], 'center');
?>


<form method="get" action="browse.php">
<table align="center" class=bottom>
<tr>
 <td class=bottom>
  <table class=bottom align="center">
   <tr align='right'>

<?php
$i = 0;
foreach ($cats as $cat) {
    $catsperrow = 7;
    echo ($i && $i % $catsperrow == 0) ? "</tr><tr align='right'>" : '';
    print("<td style=\"padding-bottom: 2px;padding-left: 2px\"><a class=catlink href=browse.php?cat={$cat["id"]}>" . 
    h($cat["name"]) . "</a><input name=c{$cat["id"]} type=\"checkbox\" " . (in_array($cat["id"], $wherecatina) ? "checked " : '') . "value=1></td>\n");
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
<?php
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

<?php

if (isset($cleansearchstr)) {
    echo '<h2>Search results for "' . h($searchstr) . '"</h2>';
}

if ($LOGGEDINONLY && !$CURUSER) {
    echo "<BR><BR><b><CENTER>You Are Not Logged In<br>Only Members Can View Torrents Please Signup.</CENTER><BR><BR>";
} else {
    if ($count) {
        torrenttable($res);
        print($pagerbottom);
    } else {
        if (isset($cleansearchstr)) {
            bark2($txt['NOTHING_FOUND'], $txt['NO_UPLOADS']);
        } else {
            bark2($txt['NOTHING_FOUND'], $txt['NO_RESULTS']);
        }
    }
}


end_Frame();

// REMOVE THIS IF YOUR LOAD IS HIGH
updateUserLastBrowse();

stdfoot();

