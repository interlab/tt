<?php

require_once('backend/functions.php');

$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
    die('bad id');
}

dbconn(false);

// $quicktags
require_once 'backend/quicktags.php';


$torrow = DB::fetchAssoc('SELECT title FROM news WHERE id = ' . $id . ' LIMIT 1');
if (!$torrow) {
    bark('found error', 'news not found');
}

stdhead('Add a comment to "' . $torrow['title'] . '"');
$query = 'SELECT title, user, date, text, comments FROM news WHERE id=\'' . $_GET['id'] . '\'';
$resu = DB::query($query);
while ($row = $resu->fetch()) {
    begin_frame($row['title']);
    echo $row['text'] . ' <br><br><I>Posted By ' . $row['user'] . '</i> On ' . $row['date'] . '
    | Comments: '.$row['comments'];
    end_frame();
}

echo'<br><br><br>';
$query = 'SELECT comment FROM news_options';
$resu = DB::query($query);
while ($row = $resu->fetch()) {
    if ($row['comment'] == 'on') {
        if ($CURUSER) {
            begin_frame('Add a comment to "' . h($torrow['title']) . '"', 'center');
?>
<p>

<table border=0 cellpadding=5>
<form name=Form method="post" action="take-ncomment.php">
<input type="hidden" name="id" value="<?= $id ?>">
<input type="hidden" name="sa" value="create">
<tr>
<td><?= $quicktags?></td><td><textarea name="body" rows="10" cols="60"></textarea></td>
</tr>
<tr><td colspan=2><center><input type="submit" class=btn value="Add Comment"></center></td></tr></table>

<?php
            $res = DB::query('
                SELECT comments.id, text, comments.added,
                    username, users.id as user, users.avatar, users.downloaded,
                    users.uploaded, users.signature, users.title, users.privacy
                FROM comments
                    LEFT JOIN users ON comments.user = users.id
                WHERE news = '.$id.'
                ORDER BY comments.id DESC
                LIMIT 5');

            $allrows = [];
            while ($row = $res->fetch()) {
                $allrows[] = $row;
            }

            if (count($allrows)) {
                commenttable($allrows, 'take-ncomment.php', 'news');
            }

            end_frame();
        } else {
            begin_frame('Error');
            echo 'You Must Be Logged In To View/Add Comments';
            end_frame();
            stdfoot();
        }
    }
}
stdfoot();

