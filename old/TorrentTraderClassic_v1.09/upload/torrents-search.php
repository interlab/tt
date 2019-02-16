<?php

require_once("backend/functions.php");
dbconn(false);
loggedinorreturn();

global $RATIO_WARNINGON, $CURUSER, $minvotes;

if ($RATIO_WARNINGON && $CURUSER) {
    include("ratiowarn.php");
}

$searchstr = $_GET["search"] ?? '';
$cleansearchstr = searchfield($searchstr);
if (empty($cleansearchstr)) {
    unset($cleansearchstr);
}

$_GET['incldead'] = (int) ($_GET['incldead'] ?? 0);
$_GET['cat'] = (int) ($_GET['cat'] ?? 0);

$sortmod = Helper::sortMod($_GET['sort'] ?? '', $_GET['type'] ?? '');
$orderby = 'ORDER BY torrents.' . $sortmod['column'] . ' ' . $sortmod['by'];
$pagerlink = $sortmod['pagerlink'];

$addparam = '';
$wherea = [];
$params = [];

if ($_GET["incldead"] === 1) {
    $addparam .= "incldead=1&amp;";
    if (!isset($CURUSER) || get_user_class() < UC_ADMINISTRATOR) {
        $wherea[] = 'banned != ?';
        $params[] = 'yes';
    }
}
elseif ($_GET["incldead"] === 2) {
    $wherea[] = 'visible = ?';
    $params[] = 'no';
} else {
    $wherea[] = 'visible = ?';
    $params[] = 'yes';
}

if ($_GET["cat"]) {
    $wherea[] = 'category = ?';
    $params[] = $_GET["cat"];
    $addparam .= "cat=" . urlencode($_GET["cat"]) . "&amp;";
}

$wherebase = $wherea;
$paramsbase = $params;

if (isset($cleansearchstr)) {
    $wherea[] = 'MATCH (search_text, ori_descr) AGAINST (?)';
    $params[] = $searchstr;
    // dump($params);
    $addparam .= "search=" . urlencode($searchstr) . "&amp;";
    //$orderby = '';  //REMOVE FOR SORT MOD
}
$where = implode(' AND ', $wherea);
if ($where != '') {
    $where = 'WHERE ' . $where;
}

$count = DB::fetchColumn('SELECT COUNT(*) FROM torrents ' . $where, $params);

if (!$count && isset($cleansearchstr)) {
    $wherea = $wherebase;
    $params = $paramsbase;
    //$orderby = 'ORDER BY id DESC';  //REMOVE FOR SORT MOD
    $searcha = explode(' ', $cleansearchstr);
    $sc = 0;
    foreach ($searcha as $word) {
        if (strlen($word) <= 1) {
            continue;
        }
        $sc++;
        if ($sc > 5) {
            break;
        }
        $ssa = [];
        foreach (['search_text', 'descr'] as $field) {
            // todo: quote
            $ssa[] = $field . ' LIKE ?';
            $params[] = '%' . sqlwildcardesc2($word) . '%';
        }
        $wherea[] = "(" . implode(" OR ", $ssa) . ")";
    }
    if ($sc) {
        $where = implode(" AND ", $wherea);
        if ($where != '') {
            $where = "WHERE $where";
        }
        // dump('SELECT COUNT(*) FROM torrents ' . $where, $params);
        $count = DB::fetchColumn('SELECT COUNT(*) FROM torrents ' . $where, $params);
    }
}

if ($count) {
    // SORT MOD
    if ($addparam != '' && $pagerlink != '') {
        $addparam = $addparam . (endsWith($addparam, '&amp;') ? '' : '&amp;') . $pagerlink;
    } else {
        $addparam = $pagerlink;
    }
    // END SORT MOD

    [$pagertop, $pagerbottom, $limit] = pager(13, $count, "torrents-search.php?$addparam");

    $query = "
    SELECT torrents.id, torrents.category, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name,
        torrents.times_completed, torrents.size, torrents.added, torrents.comments,torrents.numfiles,
        torrents.filename,torrents.owner,IF(torrents.nfo <> '', 1, 0) as nfoav,
        IF(torrents.numratings < $minvotes, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating,
        categories.name AS cat_name, categories.image AS cat_pic, users.username, users.privacy
    FROM torrents
        LEFT JOIN categories ON category = categories.id
        LEFT JOIN users ON torrents.owner = users.id
    $where
    $orderby
    $limit";

    $res = DB::executeQuery($query, $params);
}
else {
    unset($res);
}

if (isset($cleansearchstr)) {
    stdhead('Search results for "'.$searchstr.'"');
} else {
    stdhead('Browse Torrents');
}

begin_frame($txt['SEARCH_TITLE'], 'center');

?><CENTER>
<form method="get" action="torrents-search.php"><br>
<?= $txt['SEARCH'] ?>
<input type="text" name="search" size="40" value="<?= h($searchstr) ?>" />
<?= $txt['IN'] ?>
<select name="cat">
<option value="0">(All types)</option>

<?php
$cats = genrelist();
$catdropdown = '';
foreach ($cats as $cat) {
    $catdropdown .= '<option value="' . $cat['id'] . '"';
    if ($cat['id'] == $_GET['cat'])
        $catdropdown .= ' selected="selected"';
    $catdropdown .= '>' . h($cat['name']) . '</option>';
}

$deadchkbox = '<input type="checkbox" name="incldead" value="1"';
if ($_GET['incldead'])
    $deadchkbox .= ' checked="checked"';
$deadchkbox .= '> ' . $txt['INC_DEAD'];

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


<select name="incldead">
<option value="0">Active</option>
<option value="1">Including dead</option>
<option value="2">Only dead</option>
</select>
<input type="submit" class="btn" value="<?= $txt['DISPLAY'] ?>" style="margin-left: 10px">
</form>
</CENTER>

<?php
if ($count) {
	end_frame();
	echo '<br><br>';
    begin_frame($txt['SEARCH_RESULTS']);
    print($pagertop);
    torrenttable($res);
    print($pagerbottom);
} else {
	if (isset($cleansearchstr)) {
        bark2($txt['NOTHING_FOUND'], $txt['NO_UPLOADS']);
    } else {
        bark2($txt['NOTHING_FOUND'], $txt['NO_RESULTS']);
    }
}

end_frame();
stdfoot();

