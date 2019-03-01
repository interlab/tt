<?php

dbconn();

$sa = $_REQUEST['sa'] ?? '';
$fname = 'take-ncomment.php';

function _getCommentOrError(int $id)
{
    global $CURUSER;

    $arr = DB::fetchAssoc('
    SELECT *
    FROM comments
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

if ($sa === 'create') {
    $body = trim($_POST["body"] ?? '');
    if (! $body) {
       bark("Oops...", "You must enter something!");
       exit;
    }

    if (! isset($CURUSER)) {
        die('not for quest');
    }

    $id = (int) ($_POST['id'] ?? 0);
    if (! $id) {
        die('bad id');
    }

    $type = $_POST['type'] ?? '';
    $column = $type === 'poll' ? 'poll' : 'news';
    $table = $type === 'poll' ? 'polls' : 'news';
    $return_url = $type === 'poll' ? "polls.php?sa=view&id=$id" : "show-archived.php?id=$id";

    $col = DB::fetchColumn('SELECT 1 FROM '.$table.' WHERE id = '.$id.' LIMIT 1');
    if (! $col) {
        die($column.' not found');
    }

    DB::executeUpdate('INSERT INTO comments (user, '.$column.', added, text, ori_text) VALUES (?, ?, ?, ?, ?)',
        [ $CURUSER["id"], $id, get_date_time(), $body, $body ]
    );

    $newid = DB::lastInsertId();

    DB::query("UPDATE $table SET comments = comments + 1 WHERE id = $id");

    header("Refresh: 0; url=$return_url");
    die('');
}
// get: edit form by id comment
elseif ($sa === 'edit') {
    // $quicktags
    require_once '../backend/quicktags.php';

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
    <form name="Form" method="post" action="<?= $fname ?>?cid=<?= $commentid ?>">
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
        header('Location: ' . $SITEURL . '/'.$fname.'?id=' . $commentid);
    }
    die('');
}

// get: delete by id comment
elseif ($sa === 'delete') {
    $commentid = (int) ($_GET['cid'] ?? 0);
    if (! is_valid_id($commentid)) {
        bark("Error", "Invalid ID $commentid.");
    }
    $type = $_GET['type'] ?? '';
    $arr = _getCommentOrError($commentid);
    $query = 'DELETE FROM comments WHERE id = ' . $commentid;
    $result = DB::executeUpdate($query);
    if ($result) {
        if ($type === 'poll') {
            $count = DB::fetchColumn('SELECT COUNT(*) FROM comments WHERE poll = ?', [$arr['poll']]);
            $result = DB::executeUpdate('UPDATE polls SET comments = ? WHERE id = ?', [$count, $arr['poll']]);
        } elseif ($type === 'news') {
            $count = DB::fetchColumn('SELECT COUNT(*) FROM comments WHERE news = ?', [$arr['news']]);
            $result = DB::executeUpdate('UPDATE news SET comments = ? WHERE id = ?', [$count, $arr['news']]);
        }
    }
    
    $file = $arr['news'] ? 'show-archived.php?id='.$arr['news']
        : 'polls.php?sa=view&id='.$arr['poll'];
    $name = $arr['news'] ? 'news' : 'poll';
    stdhead("Delete comment:");
    begin_frame("Delete Comment");
    echo '<br><br>Comment Deleted OK<br><br>
    Return to <b><a href="'.$file.'">' . $name . '</a></b>';
    end_frame();
    stdfoot();
    die;
}

