<?php

require_once '../../backend/functions.php';

dbconn(false);
loggedinorreturn();
adminonly();

$url = 'admin-pmessages.php';

$_SERVER['HTTP_REFERER'] = $_SERVER['HTTP_REFERER'] ?? '';

// POST

if ($HTTP_SERVER_VARS["REQUEST_METHOD"] === 'POST') {
    $sender_id = ($_POST['sender'] === 'system' ? 0 : $CURUSER['id']);
    $msg = $_POST['msg'] ?? '';
    if (!$msg) {
        stderr('Error', 'Please Enter Something!');
    }

    $updateset = array_map('intval', $_POST['clases']);

    $res = DB::executeQuery('SELECT id FROM users WHERE class IN ('.implode(',', $updateset).')');
    // todo: subquery
    while ($dat = $res->fetch()) {
        DB::executeUpdate('
            INSERT INTO messages (sender, receiver, added, msg)
            VALUES (?, ?, ?, ?)',
            [ $sender_id, $dat['id'], get_date_time(), $msg ]
        );
    }

    header('Refresh: 0; url='.$url.'?page=sent');
}

// GET

$page = $_POST['page'] ?? '';
$_GET['returnto'] = $_GET['returnto'] ?? '';

if ($page === 'sent') {
    stdhead('', false);
    bark2('Success', 'Message Sent OK');
	stdfoot();
} else {
    stdhead('', false);
    require_once('../../backend/admin-functions.php');
    adminmenu();
    begin_frame('To send message to the staff and others');

    $body = $_POST['body'] ?? '';
    $receiver = $_POST['receiver'] ?? '';
?>


<div align="center" class="main">
<form method="post" action="<?= $url ?>">
<?php

if ($_GET['returnto'] || $_SERVER['HTTP_REFERER']) {
?>
<input type="hidden" name="returnto" value="<?= $_GET['returnto'] ? $_GET['returnto'] : $_SERVER['HTTP_REFERER'] ?>">
<?php
}
?>

<table>
    <tr><td>
        <p>Select groups to send message to:</p>
        <table>
        <tr>
        <td><input type="checkbox" name="clases[]" value="0"> User</td>
        <td><input type="checkbox" name="clases[]" value="1"> Uploader</td>
        <td><input type="checkbox" name="clases[]" value="2"> VIP</td>
        </tr>
        <tr>
        <td><input type="checkbox" name="clases[]" value="3"> Moderator</td>
        <td><input type="checkbox" name="clases[]" value="4"> Super Moderator</td>
        <td><input type="checkbox" name="clases[]" value="5"> Administrator</td>
        </tr>
        </table>
    </td></td>
<tr><td><textarea name="msg" cols=80 rows=15><?= $body ?></textarea>
<br>
NOTE: Remember that BB can be used (NO HTML)</td></tr>
<tr>
<td colspan=2><div align="center"><b>Sender:&nbsp;&nbsp;</b>
<?= $CURUSER['username'] ?>
<input name="sender" type="radio" value="self" checked>
&nbsp; System
<input name="sender" type="radio" value="system">
</div></td></tr>
<tr><td colspan=2 align=center><input type=submit value="Send" class=btn></td></tr>
</table>
<input type="hidden" name="receiver" value="<?= $receiver ?>">
</form>

</div>

<?php
    end_frame();
    stdfoot();
}

