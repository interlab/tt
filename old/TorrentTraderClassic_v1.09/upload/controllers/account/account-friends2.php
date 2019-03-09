<?php


// delete friend
if (isset($_REQUEST['sa']) && $_REQUEST['sa'] === 'delete') {
    dbconn();
    loggedinorreturn();

    $delid = (int) ($_GET['delid'] ?? 0);
    $user = $CURUSER['id'];

    DB::executeUpdate('DELETE FROM friends WHERE userid = '.$user.' AND friendid = '.$delid);

    header('Refresh: 0; url=' . $_SERVER['HTTP_REFERER']);
    die('');
}


dbconn();
loggedinorreturn();
stdhead();

if (!isset($_GET['user'])) {
  bark2('Error', ' ... No friend selected');
  stdfoot();
  exit;
}

$fid = (int) $_GET['user'];

$row = DB::fetchAssoc('
    SELECT username, last_access
    FROM users
    WHERE id = ' . $fid . '
    LIMIT 1'
);

if ( empty($row['username']) ) {
    bark2('Ошибка', 'Пользователь не найден.');
    stdfoot();
    exit;
}

$fusername = h($row['username']);

if (get_row_count('friends', 'WHERE userid = '.$CURUSER['id'].' AND friendid = '.$fid)) {
    bark2('Ошибка', '<a href="account-details.php?id='.$fid.'">'.$fusername.'</a> уже есть в вашем списке друзей.');
	stdfoot();
	exit;
} elseif ($CURUSER['id'] == $fid) {
	bark2('Ошибка', 'Нет смысла добавлять себя в свой список друзей.');
    stdfoot();
	exit;
}

DB::insert('friends', ['userid' => $CURUSER['id'], 'friendid' => $fid]);

$friendmsg = '[url=account-details.php?id=' . $CURUSER['id'] . '][b]'
    .$CURUSER['username']. '[/b][/url] добавил(а) вас в свой список друзей!';

DB::executeUpdate('
    INSERT INTO messages (poster, sender, receiver, added, msg)
    VALUES (?, ?, ?, ?, ?)',
    [0, 0, $fid, date('Y-m-d H:i:s'), $friendmsg]
);

begin_frame ('Succeeded', 'center', '200');
echo '<br><b><a href="account-details.php?id='.$fid.'">'.$fusername
    .'</a></b> добавлен в ваш <a href="account-friends.php">список друзей</a>.
    <br>(См. Мой Аккаунт -&gt; Мои друзья)<br>&nbsp;';
end_frame();

stdfoot();

