<?php
require "backend/functions.php";
dbconn(true);
loggedinorreturn();
if (get_user_class() > 3) {
stdhead("Delete Requests");

// ===================================
begin_frame("Delete Requests", true);
begin_table();

$res = mysql_query("SELECT count(id) FROM requests") or die(mysql_error());
$row = mysql_fetch_array($res);
$count = $row[0];


$perpage = 50;

 list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] ."?" );

echo $pagertop;


?>
<form method="post" action="takedelreq.php">
<tr><td class="colhead" align="left">Requests</td><td class="colhead" align="left">Added</td><td class="colhead" align="left">Requested by</td><td class="colhead">Category</td><td class="colhead">Filled</td><td class="colhead">Del</td></tr>
<?

$res=mysql_query("SELECT users.username, requests.filled, requests.filledby, requests.id, requests.userid, requests.request, requests.added, categories.name as cat FROM requests inner join categories on requests.cat = categories.id inner join users on requests.userid = users.id  $categ  order by requests.request $limit") or print(mysql_error());
// ------------------
while ($arr = @mysql_fetch_assoc($res)) {
{
   $cres = mysql_query("SELECT id,username FROM users WHERE id=$arr[userid]");
   if (mysql_num_rows($cres) == 1)
   {
     $carr = mysql_fetch_assoc($cres);
     $addedby = "<a href=userdetails.php?id=$carr[id]><b>$carr[username]</b></a> <a href=sendmessage.php?receiver=$carr[id]>PM</a>";
   }
$filled = $arr[filled];
if ($filled)
$filled = "<a href=$filled><font color=green><b>Yes</b></font></a>";
else
$filled = "<a href=reqdetails.php?id=$arr[id]><font color=red><b>No</b></font></a>";

 }
echo "<tr><td align=\"left\"><b>" . $arr[request] . "</b></td><td align=\"left\">" . $arr[added] . "</td><td align=\"center\">$addedby</td><td align=center>$arr[cat]</td><td align=center>$filled</td><td><input type=\"checkbox\" name=\"delreq[]\" value=\"" . $arr[id] . "\" /></td></tr>";
}
?>
<tr><td colspan="5" align="right"><input type="submit" value="Do it!" /></td></tr>
</form>
<?
// ------------------
   end_table();

echo $pagerbottom;
   end_frame();
// ===================================

stdfoot();
}
else {
stderr("Sorry", "Access denied!");
}
?>