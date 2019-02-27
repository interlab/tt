<?php

require_once '../../backend/functions.php';
dbconn(false);
loggedinorreturn();
jmodonly();

stdhead('Completed Reports');
begin_frame('Reported Items That Have Been Dealt With');

// Start reports block
$type = $_GET['type'] = $_GET['type'] ?? '';
if ($type === 'user')
    $where = " WHERE r.type = 'user'";
else if ($type === 'torrent')
    $where = " WHERE r.type = 'torrent'";
else if ($type === 'forum')
    $where = " WHERE r.type = 'forum'";
else
    $where = '';

$count = DB::fetchColumn('SELECT count(id) FROM reports ' . $where);

$perpage = 25;
[$pagertop, $pagerbottom, $limit] = pager($perpage, $count, $_SERVER['PHP_SELF'] . '?type=' . $_GET['type'] . '&' );

echo $pagertop;

echo '<table border=1 cellspacing=0 cellpadding=1 align=center width=95%>
    <tr><td class=colhead align=center>By</td>
    <td class=colhead align=center>Reported</td>
    <td class=colhead align=center>Type</td>
    <td class=colhead align=center>Reason</td>
    <td class=colhead align=center>Dealt With</td>
    <td class=colhead align=center>Mark Dealt With</td>';

if (get_user_class() >= UC_MODERATOR) {
    echo '<td class=colhead align=center>Delete</td>';
}

echo '</tr><form method="post" action="report.php">';

$res = DB::query('
    SELECT r.id, r.dealtwith, r.dealtby, r.addedby, r.votedfor, r.reason, r.type, u.username
    FROM reports AS r
        INNER JOIN users AS u on r.addedby = u.id
    ' . $where . '
    ORDER BY id desc
    ' . $limit
);

while ($arr = $res->fetch()) {
    if ($arr['dealtwith']) {
        $arr3 = DB::fetchAssoc('SELECT username FROM users WHERE id = '.$arr['dealtby']);
        $dealtwith = '<font color=green><b>Yes - <a href=account-details.php?id='.$arr['dealtby'].'>'
            .'<b>'.$arr3['username'].'</b></a></font>';
    } else {
        $dealtwith = '<div align=center><font color=red><b>No</b></font></div>';
    }

    if ($arr['type'] === 'user') {
        $type = 'userdetails';
        $arr2 = DB::fetchAssoc('SELECT username FROM users WHERE id = ' . $arr['votedfor']);
        $name = $arr2['username'];
    } elseif ($arr['type'] === 'forum') {
        $type = 'forums';
        $arr2 = DB::fetchAssoc('SELECT subject FROM forum_topics WHERE id = ' . $arr['votedfor']);
        $name = $arr2['subject'];
    } elseif ($arr['type'] === 'torrent') {
        $type = 'torrents-details';
        $arr2 = DB::fetchAssoc('SELECT name FROM torrents WHERE id = ' . $arr['votedfor']);
        $name = $arr2['name'];
        if ($name === '') {
            $name = '<b>[Deleted]</b>';
        }
    }

    echo '
    <tr><td align=center>
        <a href=account-details.php?id='.$arr['addedby'].'><b>'.$arr['username'].'</b></a>
    </td>
    <td align=center><a href='.$type.'.php?id='.$arr['votedfor'].'><b>'.$name.'</b></a></td>
    <td align=center>'.$arr['type'].'</td>
    <td align=center>'.$arr['reason'].'</td>
    <td align=center>'.$dealtwith.'</td>
    <td align=center><input type="checkbox" name="delreport[]" value="' . $arr['id'] . '"></td>';

    if (get_user_class() >= UC_MODERATOR) {
        echo '<td align=center><a href="admin-delreport.php?id='.$arr['id'].'">Delete</a></td>';
    }

    echo '</tr>';
}

echo '</table>
    <p align=right><input type=submit value=Confirm></p>
    </form>';

echo $pagerbottom;
end_frame();

stdfoot();
