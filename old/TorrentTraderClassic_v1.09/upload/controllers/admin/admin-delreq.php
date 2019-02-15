<?php

require_once '../../backend/functions.php';

dbconn(true);
loggedinorreturn();
jmodonly();
stdhead('Delete Requests');
require_once '../../backend/admin-functions.php';
adminmenu();

$count = DB::fetchColumn('SELECT count(id) FROM requests');

echo '<center>';

$perpage = 50;

list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER['PHP_SELF'] .'?' );

begin_frame('Delete Requests', true);
begin_table();

echo $pagertop;

?>
<form method="post" action="requests.php">
    <input type="hidden" name="sa" value="delete">
    <tr><td class="colhead" align="left"><?= $txt['REQUESTS'] ?></td>
    <td class="colhead" align="left"><?= $txt['DATE_ADDED'] ?></td>
    <td class="colhead" align="left"><?= $txt['ADDED_BY'] ?></td>
    <td class="colhead"><?= $txt['TTYPE'] ?></td>
    <td class="colhead"><?= $txt['FILLED'] ?></td>
    <td class="colhead"><?= $txt['ACCOUNT_DELETE'] ?></td></tr>
<?php

$res = DB::query('
    SELECT r.filled, r.filledby, r.id, r.userid, r.request, r.added,
        users.username, categories.name as cat
    FROM requests AS r
        inner join categories on r.cat = categories.id
        inner join users on r.userid = users.id
    order by r.request
    ' . $limit);

while ($arr = $res->fetch()) {
    // todo: subquery
    $carr = DB::fetchAssoc('SELECT id, username FROM users WHERE id = ' . $arr['userid']);
    if ($carr) {
        $addedby = "<a href=account-details.php?id=$carr[id]><b>$carr[username]</b></a>";
    }
    $filled = $arr['filled'];
    if ($filled)
        $filled = "<a href=$filled><font color=green><b>Yes</b></font></a>";
    else
        $filled = "<a href=requests.php?details=$arr[id]><font color=red><b>No</b></font></a>";

    echo '<tr><td align="left"><b>' . $arr['request'] . '</b></td>
        <td align="left">' . $arr['added'] . '</td>
        <td align="center">' . $addedby . '</td>
        <td align=center>' . $arr['cat'] .'</td>
        <td align=center>' . $filled . '</td>
        <td><input type="checkbox" name="delreq[]" value="' . $arr['id'] . '" /></td></tr>';
}
?>
<tr><td colspan="5" align="right"><input type="submit" value="Apply" /></td></tr>
</form></center>
<?php 

end_table();

echo $pagerbottom;
end_frame();

stdfoot();
