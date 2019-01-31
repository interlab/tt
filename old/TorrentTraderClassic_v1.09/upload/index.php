<?php
//
// - Theme And Language Updated 25.Nov.05
//
ob_start('ob_gzhandler');
require_once('backend/functions.php');
dbconn(true);

global $CURUSER, $RATIO_WARNINGON, $SHOUTBOX, $DISCLAIMERON, $minvotes;

if ($RATIO_WARNINGON && $CURUSER)
{
    include('ratiowarn.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $choice = (int) ($_POST['choice'] ?? 0);
    if ($CURUSER && $choice > -1 && $choice < 256) {
        $arr = DB::fetchAssoc('SELECT * FROM polls ORDER BY added DESC LIMIT 1');
        if (!$arr)
            die('No poll');
        $pollid = $arr['id'];
        $userid = $CURUSER['id'];
        $arr = DB::fetchAssoc('SELECT * FROM pollanswers WHERE pollid = ? AND userid = ? LIMIT 1', [$pollid, $userid]);
        if ($arr) {
            die('Dupe vote');
        }
        $res = DB::executeUpdate('INSERT INTO pollanswers VALUES(?, ?, ?, ?)', [0, $pollid, $userid, $choice]);
        if (!$res)
            stderr('Error', 'An error occured. Your vote has not been counted.');
        header('Location: ' . $SITEURL . '/');
        die;
    } else {
        stderr('Error', 'Please select an option.');
    }
}

$time_start = getmicrotime();

$sortmod = Helper::sortMod($_GET['sort'] ?? '', $_GET['type'] ?? '');
$orderby = 'ORDER BY ' . $sortmod['column'] . ' ' . $sortmod['by'];
$addparam = $sortmod['pagerlink'];

$wherea = [];
$wherea[] = "banned != 'yes'";
// $wherea[] = "visible = 'yes'"; // todo: uncomment in production
$where = implode(" AND ", $wherea);
if ($where !== '') {
    $where = 'WHERE ' . $where;
}
$limit = 15;

$count = DB::fetchColumn('SELECT COUNT(*) FROM torrents ' . $where . ' LIMIT 1');

if ($count) {
	list($pagertop, $pagerbottom, $limit) = pager(25, $count, 'browse.php?' . $addparam);

	$query = '
        SELECT t.id, t.category, t.leechers, t.nfo, t.seeders, t.name, t.times_completed,
            t.size, t.added, t.comments,t.numfiles, t.filename,t.owner, IF(t.nfo <> \'\', 1, 0) as nfoav,
            IF(t.numratings < ' . $minvotes . ', NULL, ROUND(t.ratingsum / t.numratings, 1)) AS rating,
            c.name AS cat_name, c.image AS cat_pic, u.username, u.privacy
        FROM torrents AS t
            LEFT JOIN categories AS c ON t.category = c.id
            LEFT JOIN users AS u ON t.owner = u.id
        ' . $where . '
        ' . $orderby . '
        ' . $limit;
	$tor = DB::query($query);
}
else {
    unset($tor);
}

stdhead();

$cats = genrelist();

$catdropdown = '';
foreach ($cats as $cat) {
	$catdropdown .= '<option value="browse.php?cat=' . $cat['id'] . '"';
	if ($cat["id"] == ($_GET["cat"] ?? '')) {
        $catdropdown .= " selected=\"selected\"";
    }
	$catdropdown .= ">" . h($cat["name"]) . "</option>\n";
}

// Here we decide if the site notice/welcome text is on or off
if ($SITENOTICEON) {
    begin_frame($txt['NOTICE'], 'center');
    echo '<BR>'.$SITENOTICE.'<BR><BR>';
    end_frame();
}

// NEWS BLOCK
if ($NEWSON) {
    begin_frame($txt['SITENEWS'], 'center');
    include_once 'news.php';
    end_frame();
}

if ($POLLON) {
    begin_frame('Poll', 'center');
    if (! $CURUSER) {
        echo 'You must log in to vote and view the poll';
    } else {
        // Get current poll
        $arr = DB::fetchAssoc('SELECT * FROM polls ORDER BY added DESC LIMIT 1');
        $pollok = !empty($arr);
        if ($pollok) {
            $pollid = (int) $arr['id'];
            $userid = (int) $CURUSER['id'];
            $question = $arr['question'];

            $o = [];
            for ($i = 0; $i < 20; $i++) {
                $o[$i] = $arr['option' . $i] ?? '';
            }

            // Check if user has already voted
            $voted = DB::fetchAssoc('SELECT * FROM pollanswers WHERE pollid = ' . $pollid . ' AND userid = ' . $userid);
        }

        if (get_user_class() >= UC_MODERATOR) {
            print('<div align=right><font class=small>');
            print('Moderator Options - [<a class=altlink href=makepoll.php?returnto=main><b>New</b></a>]');
            if ($pollok) {
                print(" - [<a class=altlink href=makepoll.php?action=edit&pollid=$arr[id]&returnto=main><b>Edit</b></a>]\n");
                print(" - [<a class=altlink href=polls.php?action=delete&pollid=$arr[id]&returnto=main><b>Delete</b></a>]");
            }
            echo '</font></div>';
        }

        if ($pollok) {
            // begin_table();
            echo '
            <table width=400 class=main border=1 cellspacing=0 cellpadding=5 align=center><tr><td class=text>
            <p align=center><b>' . $question . '</b></p>';

            if ($voted) {
                $uservote = isset($voted['selection']) ? $voted['selection'] : -1;
                // we reserve 255 for blank vote.
                $res = DB::fetchAll("SELECT selection FROM pollanswers WHERE pollid = $pollid AND selection < 20");
                $tvotes = count($res[0] ?? []);

                $vs = []; // array of
                $os = [];

                // Count votes
                foreach ($res as $arr3) {
                    $vs[$arr3['selection']] = ($vs[$arr3['selection']] ?? 0) + 1;
                }

                // dump($vs, $o);
                for ($i = 0; $i < count($o); ++$i) {
                    if ($o[$i]) {
                        $os[$i] = [ $vs[$i] ?? 0, $o[$i], $i ];
                    }
                }
                // dump($voted, $os, $o);

                // now os is an array like this: array(array(123, "Option 1"), array(45, "Option 2"))
                if ($arr['sort'] === 'yes') {
                    usort($os, function($a, $b) { return $b[0] <=> $a[0]; });
                }

                echo '
                <table class=main width=400 border=0 cellspacing=0 cellpadding=0>';

                $i = 0;
                // dump($voted, $os, $o);
                while (isset($os[$i])) {
                    $a = $os[$i];
                    // if ($a[1] == $o[$uservote]) {
                    if ($a[2] == $uservote) {
                        $a[1] .= "&nbsp;*";
                    }
                    if ($tvotes == 0) {
                        $p = 0;
                    } else {
                        $p = round($a[0] / $tvotes * 100);
                    }
                    if ($i % 2) {
                        $c = '';
                    } else {
                        $c = '';
                    }
                    echo '<tr><td width=1% class=embedded' . $c . '><nobr>' . $a[1] . '&nbsp;&nbsp;</nobr></td>
                        <td width=99% class=embedded' . $c . '>
                        <div class="tt-poll-line" style="width: ' . ($p * 3) . 'px"></div></td></tr>';
                    ++$i;
                }
                print("</table>\n");
                $tvotes = number_format($tvotes);
                print("<p align=center>Votes: $tvotes</p>
                    </table>");
            } else {
                print("<form method=post action=index.php>\n");
                $i = 0;
                while ($a = $o[$i]) {
                    print("<input type=radio name=choice value=$i>$a<br>\n");
                    ++$i;
                }
                print("<br>");
                //print("<input type=radio name=choice value=255>View Results<br>\n");
                print("<div align=center><input type=submit value='Vote' class=btn><br><a href=polls.php>View Results</a></div></table>");
            }
            //end_table();
        }
    }
    end_frame();
}


begin_frame($txt['TORRENT_CATEGORIES'], 'center');
print "<B><font color=#FF6600>•</font></B> <a href=browse.php>" . $txt['BROWSE_TORRENTS'] . "</a> ";
print "<B><font color=#FF6600>•</font></B> <a href=today.php>" . $txt['TODAYS_TORRENTS'] . "</a> ";
print "<B><font color=#FF6600>•</font></B> <a href=torrents-search.php>" . $txt['SEARCH'] . "</a><hr>";
$bull = "<B><font color=#FF6600>&bull;</font></B>";

    // DATE(NOW() - INTERVAL 27 DAY)
    // AND UNIX_TIMESTAMP(" . get_dt_num() . ") - UNIX_TIMESTAMP(t.added) < 3600
    $sql = '
    SELECT c.id, c.name,
        (SELECT COUNT(*) FROM torrents AS t
        WHERE t.added > DATE(NOW() - INTERVAL 1 HOUR)
            AND t.category = c.id
        ) AS new_tor
    FROM categories AS c
    ORDER BY c.sort_index, c.id';
    $res = DB::query($sql);
    while (list($id, $name, $newcount) = $res->fetch(\PDO::FETCH_NUM)) {
        if ($newcount > 0) {
            echo $bull, ' <a href="/browse.php?cat=', $id, '">', $name, '</a> (', $newcount, ') ';
        } else {
            echo $bull, ' <a href="/browse.php?cat=', $id, '">', $name, '</a> ';
        }
    }
    echo $bull, '<br><br>';
end_frame();


begin_frame($txt['BROWSE_TORRENTS'], 'center');

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
<option value="#"><?= $txt['CATEGORIES'] ?></option>
<?= $catdropdown ?>
<option value=browse.php><?= $txt['SHOWALL'] ?></option>
</select></td></form>
</tr>
<tr>
<td vAlign=top colspan="2" width=100%>
<?php

if ($LOGGEDINONLY && !$CURUSER) {
    echo "<BR><BR><b><CENTER>You Are Not Logged In<br>Only Members Can View Torrents Please Signup.</CENTER><BR><BR>";
} else {
    if ($count) {
        torrenttable($tor);
        print($pagerbottom);
    } else {
        if (isset($cleansearchstr)) {
            bark2($txt['NOTHING_FOUND'], $txt['NO_UPLOADS']);
        } else {
            bark2($txt['NOTHING_FOUND'], $txt['NO_RESULTS']);
        }
    }
}

?>
</td></tr></table>
<?php
end_frame();

//Here we decide if the shoutbox is on or off
if ($SHOUTBOX) {
    begin_frame($txt['SHOUTBOX'], 'center');
    echo '<IFRAME name="shout_frame" src="'.$SITEURL.'/ttshout.php" frameborder="0" marginheight="0" marginwidth="0" width="95%" 
    height="210" scrolling="no" align="middle"></IFRAME>';
    end_frame();
}


//Here we decide if the block is on or off
if ($DISCLAIMERON) {
    begin_frame($txt['DISCLAIMER']); 
    echo file_get_contents(ST_ROOT_DIR . '/disclaimer.txt');
    end_frame();
}

// REMOVE THIS IF YOUR LOAD IS HIGH
updateUserLastBrowse();

stdfoot();
hit_end();

