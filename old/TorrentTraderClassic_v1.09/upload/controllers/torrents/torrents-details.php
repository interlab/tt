<?php

require_once __DIR__ . '/../../backend/functions.php';

dbconn(false);
IF ($LOGGEDINONLY){
    loggedinorreturn();
}

addJsFile('vue/dist/vue.min.js');
addJsFile('axios/dist/axios.min.js');
addJsFile('tt-torrent-files-app.js');
addJsFile('tt-torrent-peers-app.js');

global $minvotes;

// DO SOME "GET" STUFF BEFORE PAGE LAYOUT

$id = (int) ($_GET['id'] ?? 0);
if (!isset($id) || !$id) {
    die('bad id');
}

function torrent_404()
{
    global $txt;

    stdhead();
    begin_frame('Error');
    print('<br><BR><center>'.$txt['TORRENT_NOT_FOUND'].'</center><br><BR>');
    end_frame();
    stdfoot();
    die;
}

// GET ALL MYSQL VALUES FOR THIS TORRENT
$row = DB::fetchAssoc('
    SELECT
        t.seeders, t.banned, t.leechers,
        t.info_hash, t.filename, t.category,
        UNIX_TIMESTAMP() - UNIX_TIMESTAMP(t.last_action) AS lastseed, t.numratings, t.name,
        IF(t.numratings < ' . $minvotes . ', NULL, ROUND(t.ratingsum / t.numratings, 1)) AS rating,
        t.owner, t.save_as, t.descr, t.visible, t.size, t.added, t.views,
        t.hits, t.times_completed, t.id, t.type, t.numfiles, c.name AS cat_name,
        u.username, u.privacy
    FROM torrents AS t
        LEFT JOIN categories AS c ON t.category = c.id
        LEFT JOIN users AS u ON t.owner = u.id
    WHERE t.id = {int:id}
    LIMIT 1',
    [ 'id' => $id ]);
if (!$row) {
    torrent_404();
}

// DECIDE IF USER IS OWNER/MOD
$owned = $moderator = 0;
if (get_user_class() >= UC_MODERATOR) {
    $owned = $moderator = 1;
} elseif ($CURUSER['id'] == $row['owner']) {
    $owned = 1;
}

// DECIDE IF TORRENT EXISTS
if (!$row || ($row['banned'] == 'yes' && !$moderator)) {
    torrent_404();
}

// Update num views for this torrent
if (empty($_SESSION['last_read_torrent'])
    || $_SESSION['last_read_torrent'] != $id
    || ($row['views'] < 1 && $owned)
) {
    DB::executeUpdate('UPDATE torrents SET views = views + 1 WHERE id = ' . $id);
    $_SESSION['last_read_torrent'] = $id;
}

stdhead('Details for torrent "' . $row['name'] . '"');

$spacer = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

if (!empty($_GET['uploaded'])) {
    bark2('Successfully uploaded!', "You can start seeding now. <b>Note</b> that the torrent won't be visible until you do that!");
}
elseif (!empty($_GET['edited'])) {
    bark2('Success', 'Edited OK!');
    if (isset($_GET['returnto'])) {
        echo '<p><b>Go back to <a href="' . h($_GET['returnto']) . '">previous page</a>.</b></p>';
    }
}
elseif (isset($_GET['searched'])) {
    bark2('Success', 'Your search for "' . h($_GET['searched']) . '" gave a single result:');
}
elseif (!empty($_GET['rated'])) {
    bark2('Success', $txt['RATING_THANK']);
}
// END "GET" STUFF

//DEFINE SOME VARIABLES
// $S IS RATING VARIABLE
$s = '';
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
if (!isset($CURUSER)) {
    $s .= "(<a href=\"account-login.php?returnto=" . urlencode($_SERVER["REQUEST_URI"])
        . "&amp;nowarn=1\">Log in</a> to rate it)";
} else {
    $ratings = [
        5 => "Cool!",
        4 => "Pretty good",
        3 => "Decent",
        2 => "Pretty bad",
        1 => "Sucks!",
    ];
    if (!$owned || $moderator) {
        $xrow = DB::fetchAssoc("SELECT rating, added FROM ratings WHERE torrent = $id AND user = " . $CURUSER["id"]);
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
// END DEFINE RATING VARIABLE

$keepget = "";
$url = "torrents-edit.php?id=" . $row["id"];
    if (isset($_GET["returnto"])) {
        $addthis = "&returnto=" . urlencode($_GET["returnto"]);
        $url .= $addthis;
        $keepget .= $addthis;
    }

$editlink = "a href=\"$url\" class=\"sublink\"";
$editit = '';
if ($owned) {
    $editit .= "| <$editlink> [" . $txt['EDIT_TORRENT'] . "]</a>";
}

// progress bar
$seedersProgressbar = [];
$leechersProgressbar = [];
// @todo: этот запрос можно выкинуть и делать прогресс-бар по кол-ву сидов и личей
$resProgressbar = DB::query('
    SELECT p.seeder, p.to_go, t.size
    FROM torrents AS t
        LEFT JOIN peers AS p ON t.id = p.torrent
    WHERE  p.torrent = ' . $id);
$progressPerTorrent = 0;
$iProgressbar = 0;
while ($rowProgressbar = $resProgressbar->fetch()) {
    $rowProgressbar['to_go'] = (int) $rowProgressbar['to_go'];
    $rowProgressbar['size'] = (int) $rowProgressbar['size'];
    // $progressPerTorrent += sprintf("%.2f", 100 * (1 - ($rowProgressbar['to_go'] / $rowProgressbar["size"])));
    $progressPerTorrent += 100 * (1 - ($rowProgressbar['to_go'] / $rowProgressbar["size"]));
    $iProgressbar++;
}
if (! $iProgressbar) {
    $iProgressbar = 1;
}
$progressTotal = sprintf("%.2f", $progressPerTorrent / $iProgressbar);
// end progress bar

// START OF PAGE LAYOUT HERE
begin_frame($txt['TORRENT_DETAILS_FOR'] . ' "' . $row['name'] . '"');

echo "<TABLE BORDER=0 WIDTH=100%><TR><TD ALIGN=RIGHT><a href=report.php?torrent=$id>"
    . $txt['REPORT_TORRENT'] . "</a> " . $editit . "</TD></TR></TABLE>";
?>
<BR>
<table cellpadding=3 width=100% border=0>
<TR><TD width=70% align=left valign=top>
    <table width=100% cellspacing=0 cellpadding=3 border=0>
        <tr><td align=left colspan=2 class="tt-descr-box"><b><?= $txt['TDESC'] ?>:</b>
            <br><?= format_comment($row['descr']) ?></td></tr>

        <tr><td align=left><b><?= $txt['NAME'] ?>:</b></td>
            <td><?= h($row['name']) ?></td></tr>

        <tr><td align=left><b><?= $txt['TORRENT'] ?>:</b></td>
            <td><a href="download.php?id=<?= $id ?>"><?= h($row['filename']) ?></a></td>
        </tr>

        <tr><td align=left><b><?= $txt['TTYPE'] ?>:</b></td><td><?= $row['cat_name'] ?></td></tr>

        <tr><td align=left><b><?= $txt['TOTAL_SIZE'] ?>:</b></td>
            <td><?= mksize($row['size']) ?></td></tr>

        <tr><td align=left><b><?= $txt['INFO_HASH'] ?>:</b></td>
            <td><?= $row['info_hash'] ?></td></tr>
<?php
if ($row["privacy"] == "strong" && get_user_class() < UC_JMODERATOR AND $CURUSER["id"] != $row["owner"]){
    print("<tr><td align=left><b>" . $txt['ADDED_BY'] . ":</b></td><td>Anonymous</td></tr>");
} else {
    print("<tr><td align=left><b>" . $txt['ADDED_BY'] . ":</b></td><td><a href=account-details.php?id=" .
        $row["owner"] . ">" . $row["username"] . "</a></td></tr>");
}

print("<tr><td align=left><b>" . $txt['DATE_ADDED'] . ":</b></td><td>" . $row["added"] . "</td></tr>");
print("<tr><td align=left><b>" . $txt['VIEWS'] . ":</b></td><td>" . $row["views"] . "</td></tr>");
print("<tr><td align=left><b>" . $txt['HITS'] . ":</b></td><td>" . $row["hits"] . "</td></tr>");
print("<tr><td align=left><b>" . $txt['RATINGS'] . ":</b></td><td>" . $s . "</td></tr>");

echo "</table></TD><TD align=right valign=top><table width=100% cellspacing=0 cellpadding=3 border=0>";

if ($row["banned"] == "yes"){
    print ("<tr><td valign=top align=right><B>" . $txt['DOWNLOAD'] . ": </B>BANNED!</td></tr>");
} else {
    echo '<tr><td valign=top align=right><a href="download.php?id=' . $id. '"><img src=images/download.png border=0></td></tr>';
}

print("<tr><td valign=top align=right><B>" . $txt['AVAILABILITY']
    . ":</B><br>" . get_percent_completed_image(floor($progressTotal)) .
    " (".round($progressTotal)."%)</td></tr>");
print("<tr><td valign=top align=right><B>" . $txt['SEEDS'] . ": <font color=green>" . $row["seeders"] . "</font></B></td></tr>");
print("<tr><td valign=top align=right><B>" . $txt['LEECH'] . ": <font color=red>" . $row["leechers"] . "</font></B></td></tr>");

// speed mod
if ($row['seeders'] >= 1 && $row['leechers'] >= 1) { 
    $a = DB::fetchAssoc("
        SELECT (t.size * t.times_completed + SUM(p.downloaded)) / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS totalspeed
        FROM torrents AS t
            LEFT JOIN peers AS p ON t.id = p.torrent
        WHERE p.seeder = 'no'
            AND p.torrent = '$id'
        GROUP BY t.id
        ORDER BY added ASC
        LIMIT 15"); 
    $totalspeed = mksize($a["totalspeed"]) . "/s";
} else {
    $totalspeed = "No traffic currently recorded";
}

echo '<tr><td valign=top align=right><B>Total Speed: <font color=green>', $totalspeed, '</font></B></td></tr>';
// end speed mod

print("<tr><td valign=top align=right><B>" . $txt['COMPLETED'] . ": " . $row["times_completed"] . "</B></td></tr>");
//print("<tr><td valign=top align=right><a href=completed.php?id=" . $id . ">[" . $txt['SEE_WHO_COMPLETED'] . "]</a></td></tr>");
print("<tr><td valign=top align=right><a href=torrents-completed-advance.php?id=" . $id . ">[" . $txt['SEE_WHO_COMPLETED'] . "]</a></td></tr>");
print("<tr><td valign=top align=right><B>" . $txt['LAST_SEEDED'] . ": </b>" . mkprettytime($row["lastseed"]) . " ago</td></tr>");

if ($row['seeders'] < 3 && $row['times_completed'] >= 1){
    print("<tr><td valign=top align=right><B>Request a re-seed: </b><a href=re-seed.php?id=" . $id . ">[SEND REQUEST!]</a></td></tr>");
}

echo "</table>";

if (get_user_class() >= UC_JMODERATOR) {
    echo "<br><BR><table width=100% cellspacing=0 cellpadding=3 style='border-collapse: collapse' bordercolor=#33CC00 border=1>";
    print("<tr><td valign=top align=center><B>" . $txt['MODERATOR_ONLY'] . "</B></td></tr>");

    echo "<br /><br />";
    print("<tr><td><form method=\"post\" action=\"torrents-delete.php\">\n");
    print("<input type=\"hidden\" name=\"id\" value=\"$id\">\n");
    if (isset($_GET["returnto"]))
        print("<input type=\"hidden\" name=\"returnto\" value=\"" . h($_GET["returnto"]) . "\" />\n");
    print("<B>" . $txt['REASON_FOR_DELETE'] . ":</B> <input type=text size=33 name=reason> <input type=submit value='" . $txt['DELETE_IT'] .
        "' style='height: 25px'>\n");
    print("</form>\n");
    print("</p>\n");
    print("</td></tr>");

    print("<tr><td valign=top align=left><B>" . $txt['BANNED'] . ": </B>" . $row["banned"] . "<br><B>" . $txt['VISIBLE'] .
        ": </B>" . $row["visible"] . "</td></tr>");


    if (get_user_class() >= UC_JMODERATOR) {        
        if (!isset($_GET["ratings"])) {
            print("<tr><td valign=top align=left><B>" . $txt['RATINGS'] . "</B> (" . $row["numratings"] .
                ") &nbsp; <a href=\"torrents-details.php?id=$id&amp;ratings=1$keepget#ratings\">[See Who Rated]</a>");
        } else {
            print("<tr><td valign=top align=left><B>" . $txt['RATINGS'] . "</B> (" . $row["numratings"] . ")");

            $s = "<table border=0 cellspacing=0 cellpadding=2>\n";
            $subres = DB::query("SELECT * FROM ratings WHERE torrent = $id ORDER BY user");

            $s .= "<tr><td><B>User</B></td><td align=right><B>Rated This</B></td></tr>\n";

            while ($subrow = $subres->fetch()) {
                // todo: sub query
                $ratingid=$subrow["user"];
                $fetched_result = DB::fetchAssoc("SELECT username FROM users WHERE id = $ratingid");
                $sd = $fetched_result['username'];
                $s .= "<tr><td><a href=account-details.php?id=$ratingid>" . $sd .
                "</a></td><td align=\"right\">" . $subrow["rating"] . "</td></tr>\n";
            }

            $s .= "</table>\n";

            print("<tr><td valign=top align=left>" .  $s . "
                <BR><a name=\"filelist\"><a href=\"torrents-details.php?id=$id$keepget\">[Hide list]</a>");
        }
    }

    echo "</table>";
}

echo "</td></tr></table>";

// Filelist
echo '
<div id="tt-filelist-app" style="display:none;">
    <strong>Список файлов:</strong> 
    <button v-on:click="tt_load_filelist('.$id.')" v-if="!open">' . $txt['SHOW'] . '</button>
    <button v-on:click="tt_load_filelist('.$id.')" v-else>' . $txt['HIDE'] . '</button>
    <div id="tt-filelist-result" v-if="open">
        <div v-if="numfiles < 1"><h2 style="color: green;">Loading ...</h2></div>
        <div v-else style="overflow: auto; max-height: 400px;">
        <table class="main" border="1" cellspacing=0 cellpadding="5">
        <thead>
        <tr>
            <th>ID</th>
            <th class=colhead>' . $txt['PATH'] . '</th>
            <th class=colhead align=left>' . $txt['SIZE'] . '</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="item in files">
            <td>{{ item[0] }}</td>
            <td>{{ item[1] }}</td>
            <td class=table_col2>{{ item[2] }}</td>
        </tr>
        </tbody>
        <tfoot>
        <tr><td colspan="3">Всего файлов: {{ numfiles }}<br>
        Общий размер: {{ fullhumansize }}<br>
        Точный размер раздачи: {{ fullsize }}<br></td></tr>
        </tfoot>
        </table>
        </div>
    </div>
</div>';

$subres = DB::query('SELECT seeder, COUNT(*) FROM peers WHERE torrent = '.$id.' GROUP BY seeder');
$sum = 0;
while ($subrow = $subres->fetch(\PDO::FETCH_NUM)) {
    $sum += $subrow[1];
}

// Peers list
echo '
<div id="tt-peerslist-app" style="display:none;">
    <B>', $txt['PEERS'], ': ', $sum, '</b>
    <button v-on:click="tt_load_peerslist('.$id.')" v-if="!open">', $txt['SHOW'], '</button>
    <button v-on:click="tt_load_peerslist('.$id.')" v-else>', $txt['HIDE'], '</button>
    <div v-html="result_html" v-if="open"></div>
</div>';

echo '
<BR><BR>';

// start comments block
begin_frame($txt['COMMENTS']);

echo '<p><a name="startcomments"></a></p>';

$commentbar = "<p align=center><a class=index href=torrents-comment.php?id=$id>" . $txt['ADDCOMMENT'] . "</a></p>\n";

$count = DB::fetchColumn('SELECT COUNT(*) FROM comments WHERE torrent = {int:id} LIMIT 1', ['id' => $id]);

if (!$count) {
    print("<BR><b><CENTER>" . $txt['NOCOMMENTS'] . "</CENTER></b><BR>\n");
} else {
    [$pagertop, $pagerbottom, $limit] = pager(15, $count, 'torrents-details.php?id='.$id, ['lastpagedefault' => 1]);

    $allrows = DB::fetchAll('
        SELECT
            comments.id, text, user, comments.added, avatar, signature,
            username, title, class, uploaded, downloaded, privacy, donated, ip
        FROM comments
            LEFT JOIN users ON comments.user = users.id
        WHERE torrent = {int:id}
        ORDER BY comments.id
        ' . $limit,
        ['id' => $id]
    );

    print($commentbar);
    print($pagertop);

    commenttable($allrows);

    print($pagerbottom);
}

print($commentbar);
end_frame();

end_frame();

stdfoot();

