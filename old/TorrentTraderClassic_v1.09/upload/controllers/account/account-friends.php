<?php

dbconn(false);

loggedinorreturn();

global $CURUSER, $INVITEONLY;

$dt = get_date_time(gmtime() - 180);
$username = h($CURUSER['username']);

stdhead('Друзья '.$username);

$res = DB::query('
    SELECT f.friendid, u.avatar, u.username, u.last_access, u.class
    FROM friends AS f
         INNER JOIN users AS u ON u.id = f.friendid
    WHERE f.userid = '.$CURUSER['id'].'
    ORDER BY f.id
    LIMIT 250');

$i = 1;

begin_frame('Мои друзья', 'center');

while ($row = $res->fetch()) {
    $class = get_user_class_name($row['class']);
    $avatar = fix_avatar($row['avatar']);
    $fname = h($row['username']);

    $content1 = '
<table style="padding: 0px;" width="100%"><tbody><tr>
    <td class="bottom" style="padding: 5px;" align="center" width="50%">
    <table class="main" height="75" width="100%"><tbody><tr valign="top"><td style="padding: 0px;" align="center" width="75">
    <div style="overflow: hidden; width: 75px; height: 75px;"><img src="'.$avatar.'" width="75"></div>
    </td>
    <td>
        <table class="main"><tbody><tr><td class="embedded" style="padding: 5px;" width="80%">
        <a href="account-details.php?id='.$row['friendid'].'"><b>'.$row['username'].'</b></a>
        <img src="'.TT_IMG_URL.'/button_o'.($row['last_access'] > $dt ? "n" : "ff").'line.gif"> ('.$class.')
        <br><br>Последнее посещение '.$row['last_access'].'<br>&nbsp;</td>
        <td class="embedded" style="padding: 5px;" width="20%">
        <br><a href="account-friends2.php?sa=delete&delid='.$row['friendid'].'">Удалить из друзей</a>
        <br><br><a href="account-inbox.php?receiver='.$row['friendid'].'">Послать ЛС</a></td></tr>
        </tbody></table>
        </td></tr></tbody></table>
	</td>';

    $content2 = '
    <td class="bottom" style="padding: 5px;" align="center" width="50%">
        <table class="main" height="75" width="100%"><tbody><tr valign="top">
        <td style="padding: 0px;" align="center" width="75"><div style="overflow: hidden; width: 75px; height: 75px;">
        <img src="'.$avatar.'" width="75"></div>
        </td>
        <td>
        <table class="main"><tbody><tr><td class="embedded" style="padding: 5px;" width="80%">
        <a href="account-details.php?id='.$row['friendid'].'"><b>'.$row['username'].'</b></a>
        <img src="'.TT_IMG_URL.'/button_o'.($row['last_access'] > $dt ? "n" : "ff").'line.gif"> ('.$class.')
        <br><br>Последнее посещение '.$row['last_access'].'<br>&nbsp;</td>
        <td class="embedded" style="padding: 5px;" width="20%">
        <br><a href="account-friends2.php?sa=delete&delid='.$row['friendid'].'">Удалить из друзей</a>
        <br><br>
        <a href="account-inbox.php?receiver='.$row['friendid'].'">Послать ЛС</a></td></tr>
        </tbody></table>
        </td></tr></tbody></table>
    </td></tr></tbody></table>';

    if ($i === 1) {
        echo $content1;
        $i = 2;
    } else {
        echo $content2;
        $i = 1;
    }
}

if ($i === 2) {
    echo '<td class="bottom" width="50%">&nbsp;</td></tr></tbody></table>';
}

$res = DB::query('
    SELECT f.userid, u.username
    FROM friends f, users u
    WHERE friendid = '.$CURUSER['id'].'
        AND f.userid = u.id
    ORDER BY username');
$first = 1;
$friendsof = '';
while ($row = $res->fetch()) {
    if ($first == 0) {
        $friendsof .= ', ';
    }
    $first = 0;
    $friendsof .= '<a href="account-details.php?id='.$row['userid'].'">'.$row['username'].'</a>';
}

if ($friendsof) {
    print("<p><br><hr><b>В друзьях у:</b> $friendsof</p><hr>");
}

?>
<p><a href="extras-users.php"><b>Искать пользователя</b></a></p>
<?php if ($INVITEONLY) { ?>
<p><a href="invite.php"><b>Send an invitation</b></a> <b>(<?= $CURUSER['invites'] ?> restantes)</b></p>
<?php 
}

end_frame();

stdfoot();
