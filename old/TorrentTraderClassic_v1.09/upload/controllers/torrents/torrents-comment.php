<?php

require_once __DIR__ . '/../../backend/functions.php';

dbconn(false);
loggedinorreturn();

function _getCommentOrError(int $id)
{
    global $CURUSER;

    $arr = DB::fetchAssoc('
    SELECT *, t.name as tname
    FROM comments
        LEFT JOIN torrents AS t ON t.id = comments.torrent
    WHERE comments.id = ' . $id);
    if (! $arr) {
        stderr("Error", "Comment not found.");
    }
    $arr['user'] = (int) $arr['user'];
    if ($CURUSER['id'] !== $arr['user'] && get_user_class() < UC_JMODERATOR) {
        stderr("Access Denied", "You can only edit your own comments.");
    }

    return $arr;
}

// $quicktags
require_once '../../backend/quicktags.php';

$sa = $_REQUEST["sa"] ?? '';

// post: create new comment
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
if ($sa === 'create') {
    $body = trim($_POST["body"] ?? '');
    if (! $body) {
        bark("Oops...", "You must enter something!");
        exit;
    }

    if (! isset($CURUSER)) {
        die('curuser not found.');
    }

    $id = (int) ($_POST['id'] ?? 0);
    if (! $id) {
        die('bad id.');
    }

    $arr = DB::fetchAssoc("SELECT name, owner FROM torrents WHERE id = $id LIMIT 1");
    if (! $arr) {
        die('Torrent not found!');
    }

    DB::executeUpdate('
        INSERT INTO comments (user, torrent, added, text, ori_text)
            VALUES (?, ?, ?, ?, ?)',
        [ $CURUSER["id"], $id, get_date_time(), $body, $body]
    );

    $newid = DB::lastInsertId();

    $num = get_row_count('comments', 'where torrent = ' . $id);

    DB::executeUpdate('UPDATE torrents SET comments = ? WHERE id = ' . $id, [$num]);

    // PM NOTIF
    $user = DB::fetchAssoc('SELECT commentpm FROM users WHERE id = ' . $arr['owner']);

    if ($user["commentpm"] === 'yes' && $CURUSER['id'] != $arr["owner"]) {
        $msg = 'You have received a comment on your torrent [url='. $SITEURL. '/torrents-details.php?id=' . $id . ']here[/url]';
        DB::executeUpdate('
            INSERT INTO messages (poster, sender, receiver, msg, added) VALUES(?, ?, ?, ?, ?)',
            [0, 0, $arr['owner'], $msg, get_date_time()]
        );
    }
    // end PM NOTIF

    header('Refresh: 0; url=torrents-details.php?id=' . $id . '&viewcomm=' . $newid . '#comm' . $newid);

    die('');
}

// get: edit form by id comment
elseif ($sa === 'edit') {
    $commentid = (int) ($_GET["cid"] ?? 0);
    if (!is_valid_id($commentid)) {
        bark("Error", "Invalid ID $commentid.");
    }
    $arr = _getCommentOrError($commentid);

    stdhead("Edit comment:");
    begin_frame("Edit Comment");
?>
    <div align="center">
    <p style="margin: 0;">Please type your comment here, please remember to obey the <a href="rules.php">Rules</a>.</p>
    <table border=0 cellpadding=5>
    <form name="Form" method="post" action="torrents-comment.php?cid=<?= $commentid ?>">
    <input type="hidden" name="returnto" value="<?= ($_SERVER["HTTP_REFERER"] ?? '') ?>">
    <input type="hidden" name="cid" value="<?= $commentid ?>">
    <input type="hidden" name="sa" value="update">
    <tr>
        <td><?= $quicktags ?></td><td><textarea name="body" rows="10" cols="60"><?= h($arr["text"]) ?></textarea></td>
    </tr>
    <tr>
        <td colspan=2><center><input type="submit" class="btn" value="Submit Changes"></center></td>
    </tr>
    </form>
    </table>
    </div>

<?php
    end_frame();
    stdfoot();
    die;
}

// post: update by id comment
elseif ($sa === "update") {
    $text = $_POST['body'] ?? '';
    if ($text === '') {
        bark("Error", "Empty message");
    }
    $commentid = (int) ($_POST['cid'] ?? 0);
    $returnto = $_POST['returnto'] ?? '';
    $arr = _getCommentOrError($commentid);
    $query = 'UPDATE comments SET text = ? WHERE id = ' . $commentid;
    $result = DB::executeUpdate($query, [$text]);

    if ($returnto) {
        header("Location: $returnto");
    } else {
        header('Location: ' . $SITEURL . '/torrents-details.php?id=' . $commentid);
    }
    die('');
}

// get: delete by id comment
elseif ($sa === "delete") {
    $commentid = (int) ($_GET["cid"] ?? 0);
    if (!is_valid_id($commentid)) {
        bark("Error", "Invalid ID $commentid.");
    }
    $arr = _getCommentOrError($commentid);
    $query = 'DELETE FROM comments WHERE id = ' . $commentid;
    $result = DB::executeUpdate($query);
    stdhead("Delete comment:");
    begin_frame("Delete Comment");
    echo '<br><br>Comment Deleted OK<br><br>
    Return to torrent <a href="torrents-details.php?id=' . $arr['torrent'] . '">' . $arr['tname'] . '</a>';
    end_frame();
    stdfoot();
    die;
}

// get: create form by id torrent
else {
    $id = (int) ($_GET['id'] ?? 0);
    if (! $id) {
        die('bad id');
    }

    $torrow = DB::fetchAssoc("SELECT name FROM torrents WHERE id = $id");
    if (!$torrow) {
        die('torrent not found!');
    }

    stdhead("Add a comment to \"" . $torrow["name"] . "\"");

    begin_frame('Add a comment to "<a href="torrents-details.php?id=' . $id . '">' . h($torrow['name']) . '</a>"', 'center');
    ?>
    <div align="center">
    <p style="margin: 0;">Please type your comment here, please remember to obey the <a href="rules.php">Rules</a>.</p>
    <table border=0 cellpadding=5>
    <form name=Form method="post" action="torrents-comment.php">
    <input type="hidden" name="id" value="<?= $id ?>">
    <input type="hidden" name="sa" value="create">
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
        WHERE c.torrent =  ' . $id . '
        ORDER BY c.id ASC');

    $allrows = [];
    while ($row = $res->fetch())
        $allrows[] = $row;

    if (count($allrows)) {
        commenttable($allrows);
    }

    end_frame();
    stdfoot();

    die('');
}
