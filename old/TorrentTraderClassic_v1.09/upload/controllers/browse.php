<?php

dbconn(false);

global $CURUSER, $LOGGEDINONLY, $SPECIAL_FREE_VIEW, $no_check_referer, $RATIO_WARNINGON;
global $RATIOWARN_TIME, $RATIOWARN_BAN, $RATIOWARN_AMMOUNT, $minvotes;

if ($LOGGEDINONLY) {
    loggedinorreturn();
}

if ($RATIO_WARNINGON && $CURUSER) {
    require_once TT_ROOT_DIR.'/ratiowarn.php';
}

$_GET['c'] = $_GET['c'] ?? [];

$cats = genrelist();

$orderby = 'ORDER BY torrents.id DESC';
$addparam = '';
$wherea = [];
$wherecatina = [];
$wherecatin = '';
$params = [];

$_GET['incldead'] = $_GET['incldead'] ?? '';
$_GET['sort'] = $_GET['sort'] ?? '';
$_GET['type'] = $_GET['type'] ?? '';

$category = (int) ($_GET['cat'] ?? 0);

$all = $_GET['all'] ?? '';

if (!$all && (!$_GET && $CURUSER['notifs'])) { // todo: check logik
    $all = true;
    foreach ($cats as $cat) {
        $all &= $cat['id'];
        if (strpos($CURUSER['notifs'], '[cat' . $cat['id'] . ']') !== false) {
            $wherecatina[] = (int) $cat['id'];
            $addparam .= 'c[]='.$cat['id'].'&amp;';
        }
    }
} elseif ($category) {
    if (!is_valid_id($category)) {
        stderr('Error', 'Invalid category ID '.$category.'.');
    }
    $wherecatina[] = $category;
    $addparam .= 'cat='.$category.'&amp;';
} else {
    $all = true;
    foreach ($cats as $cat) {
        if (in_array($cat['id'], $_GET['c'])) {
            $all = false;
            $wherecatina[] = (int) $cat['id'];
            $addparam .= 'c[]='.$cat['id'].'&amp;';
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

if ($_GET['incldead'] == 2)
    $wherea[] = "visible = 'no'";
else
    $wherea[] = "visible = 'yes'";

$where = implode(' AND ', $wherea);
if ($wherecatin) {
    $where .= ($where ? ' AND ' : '') . 'category IN(' . $wherecatin . ')';
}

if ($where != '') {
    $where = 'WHERE '.$where;
}

// dump($where);
$count = DB::fetchColumn('SELECT COUNT(*) FROM torrents ' . $where);

if ($_GET['sort']) {
    if ($addparam != '') {
        $addparam .= '&';
    }
    $addparam .= "sort=$_GET[sort]";
}
if ($_GET['type']) {
    $addparam .= "&type=$_GET[type]&";
}
if ($count) {
    [$pagertop, $pagerbottom, $limit] = pager(25, $count, 'browse.php?' . $addparam);
    $res = DB::query('
        SELECT
            torrents.id, torrents.category, torrents.leechers, torrents.nfo, torrents.seeders, 
            torrents.name, torrents.times_completed, torrents.size, torrents.added,
            torrents.comments,torrents.numfiles,torrents.filename,torrents.owner,
            IF(torrents.nfo <> \'\', 1, 0) as nfoav,
            IF(torrents.numratings < '.$minvotes.', NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, 
            categories.name AS cat_name, categories.image AS cat_pic, 
            users.username, users.privacy
        FROM torrents
            LEFT JOIN categories ON category = categories.id
            LEFT JOIN users ON torrents.owner = users.id 
        '.$where.'
        '.$orderby.'
        '.$limit
    );
}

stdhead();

begin_frame($txt['BROWSE_TORRENTS'], 'center');
?>

<form method="get" action="browse.php">
<table align="center" class=bottom>
<tr>
 <td class=bottom>
  <table class=bottom align="center">
   <tr align="right">

<?php
$i = 0;
foreach ($cats as $cat) {
    $catsperrow = 7;
    echo ($i && $i % $catsperrow == 0) ? '</tr><tr align="right">' : '';
    echo '<td style="padding-bottom: 2px;padding-left: 2px">
        <a class="catlink" href="browse.php?cat='.$cat['id'].'">' . 
        h($cat['name']) . '</a><input name="c[]" type="checkbox" '
        . (in_array($cat['id'], $wherecatina) ? 'checked ' : '') . 'value="'.$cat["id"].'"></td>';
    $i++;
}

$alllink = '<div align=left>(<a href=browse.php?all=1><b>Show all</b></a>)</div>';

$ncats = count($cats);
$nrows = ceil($ncats / $catsperrow);
$lastrowcols = $ncats % $catsperrow;

if ($lastrowcols != 0) {
    if ($catsperrow - $lastrowcols != 1) {
        print("<td class=bottom rowspan=" . ($catsperrow  - $lastrowcols - 1)
            . ">&nbsp;</td>");
    }
    echo '<td class=bottom style="padding-left: 5px;">'.$alllink.'</td>';
}
?>
   </tr>
  </table>
 </td>
</tr>
<?php
if ($ncats % $catsperrow == 0) {
    echo '<tr><td class=bottom style="padding-left: 15px" rowspan='.$nrows.' valign=center align=right>
        '.$alllink.'
        </td></tr>';
}
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

if ($LOGGEDINONLY && !$CURUSER) {
    echo "<BR><BR><b><CENTER>You Are Not Logged In<br>Only Members Can View Torrents Please Signup.</CENTER><BR><BR>";
} else {
    if ($count) {
        torrenttable($res);
        print($pagerbottom);
    } else {
        bark2($txt['NOTHING_FOUND'], $txt['NO_RESULTS']);
    }
}


end_frame();

// REMOVE THIS IF YOUR LOAD IS HIGH
updateUserLastBrowse();

stdfoot();

