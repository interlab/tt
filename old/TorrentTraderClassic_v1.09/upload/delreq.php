<?php

require "backend/functions.php";
dbconn(true);
loggedinorreturn();

if (!(get_user_class() > 3)) {
    stderr("Sorry", "Access denied!");
} else {

    stdhead("Delete Requests");
    begin_frame("Delete Requests", true);
    begin_table();

    $count = DB::fetchColumn('SELECT COUNT(id) FROM requests');

    $perpage = 50;

    list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] ."?" );

    echo $pagertop;

?>
<form method="post" action="requests.php">
    <input type="hidden" name="action" value="delete">
<tr><td class="colhead" align="left">Requests</td>
<td class="colhead" align="left">Added</td>
<td class="colhead" align="left">Requested by</td>
<td class="colhead">Category</td>
<td class="colhead">Filled</td>
<td class="colhead">Del</td></tr>
<?php

$res = DB::query('
    SELECT users.username, requests.filled, requests.filledby, requests.id,
        requests.userid, requests.request, requests.added, categories.name as cat
    FROM requests
        inner join categories on requests.cat = categories.id
        inner join users on requests.userid = users.id
    order by requests.request
    ' . $limit);
// ------------------

$filled = '';
$addedby = '';
while ($arr = $res->fetch()) {
    $cres = DB::query("SELECT id, username FROM users WHERE id = " . $arr['userid']);
    if ($cres) {
        $carr = $cres->fetch();
        $addedby = "<a href=userdetails.php?id=$carr[id]><b>$carr[username]</b></a> <a href=sendmessage.php?receiver=$carr[id]>PM</a>";
    }
    $filled = $arr['filled'];

    if ($filled)
        $filled = "<a href=$filled><font color=green><b>Yes</b></font></a>";
    else
        $filled = "<a href=requests.php?details=$arr[id]><font color=red><b>No</b></font></a>";


    echo "<tr><td align=\"left\"><b>" . $arr['request'] . "</b></td><td align=\"left\">" . $arr['added'] .
        "</td><td align=\"center\">$addedby</td><td align=center>" . $arr['cat'] . "</td><td align=center>$filled</td>
        <td><input type=\"checkbox\" name=\"delreq[]\" value=\"" . $arr['id'] . "\" /></td></tr>";
}
?>
<tr><td colspan="5" align="right"><input type="submit" value="Do it!" /></td></tr>
</form>
<?php

    end_table();

    echo $pagerbottom;
    end_frame();

    stdfoot();
}

