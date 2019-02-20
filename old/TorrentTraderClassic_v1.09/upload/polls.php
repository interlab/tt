<?php

require 'backend/functions.php';
dbconn(false);
loggedinorreturn();

$sa = $_GET['sa'] ?? '';
$pollid = (int) ($_GET['pollid'] ?? 0);
$returnto = $_GET['returnto'] ?? '';

function df($datetime)
{
    return gmdate('Y-m-d', strtotime($datetime)) . ' GMT ('
        . (get_elapsed_time(sql_timestamp_to_unix_timestamp($datetime))) . ' ago)';
}

if ($sa == 'delete') {
    if (get_user_class() < UC_MODERATOR)
        stderr('Error', 'Permission denied.');

    if (! is_valid_id($pollid))
        stderr('Error', 'Invalid ID.');

    $sure = (int) ($_GET['sure'] ?? 0);
    if (! $sure) {
        stderr('Delete poll', "Do you really want to delete a poll? Click\n" .
            "<a href=?sa=delete&pollid=$pollid&returnto=$returnto&sure=1>here</a> if you are sure.");
    }

    DB::executeUpdate("DELETE FROM pollanswers WHERE pollid = $pollid");
    DB::executeUpdate("DELETE FROM polls WHERE id = $pollid");
    if ($returnto == "main")
        header("Location: $SITEURL");
    else
        header("Location: $SITEURL/polls.php?deleted=1");
    die;
} elseif ($sa === 'view') {
    // $quicktags
    require_once 'backend/quicktags.php';

    stdhead();
    $id = (int) ($_GET['id'] ?? 0);
    if ($POLLON) {
        if (! $CURUSER) {
            bark('not perm', 'You must log in to vote and view the poll');
        } else {
            // Get current poll
            $arr = DB::fetchAssoc('SELECT * FROM polls WHERE id = ? LIMIT 1', [$id]);
            $pollok = !empty($arr);
            if (! $pollok) {
                bark('not found', 'Poll not found.');
            }

            $pollid = (int) $arr['id'];
            $userid = (int) $CURUSER['id'];
            $question = $arr['question'];

            $o = [];
            for ($i = 0; $i < 20; $i++) {
                $o[$i] = $arr['option' . $i] ?? '';
            }

            // Check if user has already voted
            $voted = DB::fetchAssoc('SELECT * FROM pollanswers WHERE pollid = ' . $pollid . ' AND userid = ' . $userid);

            begin_frame('Poll', 'center');
            if (get_user_class() >= UC_MODERATOR) {
                print('<div align=right><font class=small>');
                print('Moderator Options - [<a class=altlink href=makepoll.php?returnto=main><b>New</b></a>]');
                if ($pollok) {
                    print(" - [<a class=altlink href=makepoll.php?action=edit&pollid=$arr[id]&returnto=main><b>Edit</b></a>]\n");
                    print(" - [<a class=altlink href=polls.php?sa=delete&pollid=$arr[id]&returnto=main><b>Delete</b></a>]");
                }
                echo '</font></div>';
            }

            echo '
            <table width=400 class=main border=1 cellspacing=0 cellpadding=5 align=center><tr><td class=text>
            <p align=center><b>' . $question . '</b></p>';

            if ($voted) {
                $uservote = isset($voted['selection']) ? $voted['selection'] : -1;
                // we reserve 255 for blank vote
                $res = DB::fetchAll("SELECT selection FROM pollanswers WHERE pollid = $pollid AND selection < 20");
                $tvotes = count($res);

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
                echo '<div align=right>[<a href=""><b>Add comment</b></a>]</div>';
            } else {
                print("<form method=post action=index.php>\n");
                $i = 0;
                while ($a = $o[$i]) {
                    print("<input type=radio name=choice value=$i>$a<br>\n");
                    ++$i;
                }
                print("<br>");
                //print("<input type=radio name=choice value=255>View Results<br>\n");
                print("<div align=center><input type=submit value='Vote' class=btn>
                <br><a href=polls.php>View Results</a></div></table>");
            }
        }
        end_frame();

        if ($CURUSER) {
            begin_frame('Add a comment', 'center');

            ?>
            <div align="center">
            <p style="margin: 0;">Please type your comment here, please remember to obey the <a href="rules.php">Rules</a>.</p>
            <table border=0 cellpadding=5>
            <form name=Form method="post" action="take-ncomment.php">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="sa" value="create">
            <input type="hidden" name="type" value="poll">
            <tr>
            <td><?= $quicktags ?></td><td><textarea name="body" rows="10" cols="60"></textarea></td>
            </tr>
            <tr><td colspan=2><center><input type="submit" class=btn value="Add Comment"></center></td></tr>
            </form></table>
            </div>
            <?php

            $res = DB::query('
                SELECT c.id, text, c.added, u.username, u.id as user,
                    u.avatar, u.title, u.signature, u.downloaded, u.uploaded, u.privacy
                FROM comments AS c
                    LEFT JOIN users AS u ON c.user = u.id
                WHERE c.poll =  ' . $id . '
                ORDER BY c.id ASC');

            $allrows = [];
            while ($row = $res->fetch()) {
                $allrows[] = $row;
            }

            if (count($allrows)) {
                commenttable($allrows, 'take-ncomment.php', 'poll');
            }

            end_frame();
        }
    }
    stdfoot();
} else {
    // GET

    $pollcount = DB::fetchColumn("SELECT COUNT(*) FROM polls");
    if (! $pollcount) {
        stderr("Sorry...", "There are no polls!");
    }

    [$pagertop, $pagerbottom, $limit] = pager(15, $pollcount, 'polls.php?', ['lastpagedefault' => 1]);

    // todo: pager
    $polls = DB::query("SELECT * FROM polls ORDER BY id DESC");

    stdhead("Polls");
    begin_frame("Polls");

    echo '<style>
    .tt-poll-table {
        width: 100%;
        border-spacing: 5px;
        border-collapse: separate;
        border-collapse: collapse;
    }
    .tt-poll-table th {
        text-align: center;
    }
    .tt-poll-table td {
        border: 1px solid black;
        text-align: center;
        padding: 4px;
    }
    .tt-poll-table th:first-child,
    .tt-poll-table td:first-child {
        text-align: left;
    }
    </style>';

    echo '<div align="right">Moderator Options - [<a class=altlink href=makepoll.php?returnto=main><b>New</b></a>]</div>';
    echo $pagertop;

    echo '<table class="tt-poll-table"><thead><th>Вопрос</th>
        <th>Голосовало</th>
        <th>Comments</th>
        <th>Added</th>
        <th>Окончание</th></thead><tbody>';
    while ($poll = $polls->fetch()) {
        // dump($poll, substr($poll['ending'], 0, 3));
        echo '<tr>';

        $added = df($poll['added']);
        $ending = substr($poll['ending'], 0, 4) === '0000' ? '-' : df($poll['ending']);
        // todo: subquery
        $count = DB::fetchColumn("SELECT COUNT(*) FROM pollanswers WHERE pollid = " . $poll["id"] . " AND  selection < 20");
        $link = '<a href="polls.php?sa=view&id='.$poll['id'].'">'.$poll['question'].'</a>';

        echo '<td><b>' . $link . '</b>';

        /*
        if (get_user_class() >= UC_MODERATOR) {
            print(" - [<a href=makepoll.php?action=edit&pollid=$poll[id]><b>Edit</b></a>]\n");
            print(" - [<a href=polls.php?action=delete&pollid=$poll[id]><b>Delete</b></a>]\n");
        }
        */

        echo '
                </td><td>', $count, '</td><td>', $poll['comments'], '</td>
                <td>', $added, '</td><td>', $ending, '</td>
            </tr>';
    }
    echo '</tbody></table>';
    echo $pagerbottom;

    end_frame();
    stdfoot();
}
