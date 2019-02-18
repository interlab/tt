<?php 

require_once __DIR__ . '/../backend/functions.php';

dbconn();
loggedinorreturn();

stdhead('Staff');

// Display Staff List to all users
begin_frame($txt['STAFF']);

// Get current datetime
$dt = get_date_time(gmtime() - 180);
// Search User Database for Moderators and above and display in alphabetical order
$res = DB::query('
    SELECT *
    FROM users
    WHERE class >= ' . UC_UPLOADER . '
        AND status = \'confirmed\'
    ORDER BY username');

    $col = [];
    $staff_table = [];
    while ($arr = $res->fetch()) {
        $staff_table[$arr['class']] = ($staff_table[$arr['class']] ?? '').
        	"<td><img src=images/button_o".($arr['last_access'] > $dt ? "n" : "ff")."line.gif></td>".
            "<td><a href=account-details.php?id=$arr[id]>$arr[username]</a></td>".
   		    "<td><a href=account-inbox.php?receiver=$arr[username]>".
            "<img src=images/button_pm.gif border=0></a></td><td>&nbsp;</td>";
        // Show 3 staff per row, separated by an empty column
        $col[$arr['class']] = ($col[$arr['class']] ?? 0) + 1;
        if ($col[$arr['class']] <= 3) {
            $staff_table[$arr['class']] = $staff_table[$arr['class']]."<td>&nbsp;</td>";
        } else {
            $staff_table[$arr['class']] = $staff_table[$arr['class']]."</tr><tr height=15>";
            $col[$arr['class']] = 1;
        }
	}
?>
<BR><BR>
<br>
<table width=725 cellspacing=0 align=center>
<?php if (get_user_class() >= UC_JMODERATOR) { ?>
<tr>
	<td colspan=14><b>Administrators</b><font color="#FF0000"> [HIDDEN FROM PUBLIC]</font></td>
</tr>
<tr>
	<td colspan=14><hr color="#4040c0" size=1></td>
</tr>
<tr height=15>
	<?= $staff_table[UC_ADMINISTRATOR] ?? '' ?>
</tr>
<tr>
	<td colspan=14>&nbsp;</td>
</tr>
<?php } ?>
<tr>
	<td colspan=14><b>Super Moderators</b></td>
</tr>
<tr>
	<td colspan=14><hr color="#4040c0" size=1></td>
</tr>
<tr height=15>
	<?= $staff_table[UC_MODERATOR] ?? '' ?>
</tr>
<tr>
	<td colspan=14>&nbsp;</td>
</tr>
<tr>
	<td colspan=14><b>Moderators</b></td>
</tr>
<tr>
	<td colspan=14><hr color="#4040c0" size=1></td>
</tr>
<tr height=15>
	<?= $staff_table[UC_JMODERATOR] ?? '' ?>
</tr>
<?php if (get_user_class() >= UC_JMODERATOR) { ?>
<tr>
	<td colspan=14>&nbsp;</td>
</tr>
<tr>
	<td colspan=14><b>VIP Members</b><font color="#FF0000"> [HIDDEN FROM PUBLIC]</font></td>
</tr>
<tr>
	<td colspan=14><hr color="#4040c0" size=1></td>
</tr>
<tr height=15>
	<?= $staff_table[UC_VIP] ?? '' ?>
</tr>
<tr>
	<td colspan=14>&nbsp;</td>
</tr>
<tr>
	<td colspan=14><b>Uploaders</b><font color="#FF0000"> [HIDDEN FROM PUBLIC]</font></td>
</tr>
<tr>
	<td colspan=14><hr color="#4040c0" size=1></td>
</tr>
<tr height=15>
	<?= $staff_table[UC_UPLOADER] ?? '' ?>
</tr>
<tr>
    <!-- Define table column widths -->
    <td width="20"></td>
    <td width="100"></td>
    <td width="25"></td>
    <td width="35"></td>
    <td width="90"></td>
    <td width="20"></td>
    <td width="100"></td>
    <td width="25"></td>
    <td width="35"></td>
    <td width="90"></td>
    <td width="20"></td>
    <td width="100"></td>
    <td width="25"></td>
    <td width="35"></td>
</tr>
<?php } ?>

</table>
<?php 
end_frame();

stdfoot();
