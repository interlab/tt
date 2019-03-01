<?php
#=============================================================================#
# RSS Backend
#=============================================================================#
#
# Howto use: rss.php // last 15 torrents (all cats)
# rss.php?cat=1 // last 15 torrents of cat 1 etc etc
#
#=============================================================================#

header("Content-Type: application/xml");

dbconn();

$cat = (int) ($_GET["cat"] ?? 0);

// by category ?
if (!$cat)
    $catvar = "";
else
    $catvar = "category='$cat' AND";

// start the RSS feed output
echo("<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>");
echo("<rss version=\"2.0\"><channel>" .
"<title>" . $SITENAME . "</title><link>" . $SITEURL
. "</link><language>en-usde</language><copyright>Copyright "
. $SITENAME . "</copyright>");

// get all vars
$res = DB::query("
    SELECT id, name, filename, size, category, seeders, leechers, added
    FROM torrents
    WHERE $catvar visible='yes'
    ORDER BY added DESC
    LIMIT 15");
while ($row = $res->fetch(\PDO::FETCH_NUM)) {
    [$id, $name, $filename, $size, $category, $seeders, $leechers, $added, $catname] = $row;

    // seeders ?
    if (($seeders) >= 1) {
        $s = "s";
        $aktivs="$seeders seeder$s";
    }
    else
        $aktivs="no seeders";

    // leechers ?
    if ($leechers >= 1) {
        $l = "s";
        $aktivl="$leechers leecher$l";
    }
    else
        $aktivl="no leecher";

    $link = $SITEURL . '/torrents-details.php?id=' . $id;

    // measure the totalspeed
    if ($seeders >= 1 && $leechers >= 1){
        $b = DB::fetchAssoc("
    SELECT (t.size * t.times_completed + SUM(p.downloaded)) / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS totalspeed
    FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent
    WHERE p.seeder = 'no' AND p.torrent = '$id'
    GROUP BY t.id
    ORDER BY added ASC LIMIT 15");
        $totalspeed = mksize($a["totalspeed"]) . "/s";
    }
    else
        $totalspeed = "no traffic";

    // name a category
    $b = DB::fetchAssoc("SELECT name FROM categories WHERE id = '$category'");

    // output of all data
    echo("<item><title>" . h($name) . "</title><link>"
    . $link . "</link><description>Category: " . $b["name"] . "  Size: "
    . mksize($size) . " Status: " . $aktivs . " and " . $aktivl . " Added: "
    . $added . "</description></item>");
}

echo("</channel></rss>");

