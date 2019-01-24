<?php
//
// - Theme And Language Updated 25.Nov.05
//
ob_start("ob_gzhandler");
require_once("backend/functions.php");
hit_start();
dbconn();
loggedinorreturn();

stdhead("Requests Page");


begin_frame("" . REQUESTS . "");
if($REQUESTSON){

print("<a href=requests.php>Add New Request</a> | <a href=viewrequests.php?requestorid=$CURUSER[id]>View my requests</a>");


$categ = (int)$_GET["category"];
$requestorid = (int)$_GET["requestorid"];
$sort = $_GET["sort"];
$search = $_GET["search"];
$filter = $_GET["filter"];

$search = " AND requests.request like '%$search%' ";


if ($sort == "votes")
$sort = " order by hits desc ";
else if ($sort == "request")
$sort = " order by request ";
else
$sort = " order by added desc ";


if ($filter == "true")
$filter = " AND requests.filledby = 0 ";
else
$filter = "";


if ($requestorid <> NULL)
{
if (($categ <> NULL) && ($categ <> 0))
 $categ = "WHERE requests.cat = " . $categ . " AND requests.userid = " . $requestorid;
else
 $categ = "WHERE requests.userid = " . $requestorid;
}

else if ($categ == 0)
$categ = '';
else
$categ = "WHERE requests.cat = " . $categ;

/*
if ($categ == 0)
$categ = 'WHERE requests.cat > 0 ';
else
$categ = "WHERE requests.cat = " . $categ;
*/


$res = mysql_query("SELECT count(requests.id) FROM requests inner join categories on requests.cat = categories.id inner join users on requests.userid = users.id  $categ $filter $search") or die(mysql_error());
$row = mysql_fetch_array($res);
$count = $row[0];

$perpage = 50;

list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] ."?" . "category=" . $_GET["category"] . "&sort=" . $_GET["sort"] . "&" );

$res = mysql_query("SELECT users.downloaded, users.uploaded, users.username, users.privacy, requests.filled, requests.filledby, requests.id, requests.userid, requests.request, requests.added, requests.hits, categories.name as cat FROM requests inner join categories on requests.cat = categories.id inner join users on requests.userid = users.id  $categ $filter $search $sort $limit") or sqlerr();
$num = mysql_num_rows($res);

print("<br><br><CENTER><form method=get action=viewrequests.php>");
print("" . SEARCH . ": <input type=text size=30 name=search>");
print("<input type=submit align=center value=" . SEARCH . " style='height: 22px'>\n");
print("</form></CENTER><br>");

echo $pagertop;

echo "<Table border=0 width=100% cellspacing=0 cellpadding=0><TR><TD width=50% align=left valign=bottom>";

print("<p>" . SORT_BY . " <a href=" . $_SERVER[PHP_SELF] . "?category=" . $_GET[category] . "&filter=" . $_GET[filter] . "&sort=votes>" . VOTES . "</a>, <a href=". $_SERVER[PHP_SELF] ."?category=" . $_GET[category] . "&filter=" . $_GET[filter] . "&sort=request>Request Name</a>, or <a href=" . $_SERVER[PHP_SELF] ."?category=" . $_GET[category] . "&filter=" . $_GET[filter] . "&sort=added>" . DATE_ADDED . "</a>.</p>");

print("<form method=get action=viewrequests.php>");
?>
</td><td width=100% align=right valign=bottom>
<select name="category">
<option value="0"><? print("" . SHOW_ALL . "\n"); ?></option>
<?

$cats = genrelist();
$catdropdown = "";
foreach ($cats as $cat) {
   $catdropdown .= "<option value=\"" . $cat["id"] . "\"";
   $catdropdown .= ">" . h($cat["name"]) . "</option>\n";
}

?>
<?= $catdropdown ?>
</select>
<?
print("<input type=submit align=center value=" . DISPLAY . " style='height: 22px'>\n");
print("</form></td></tr></table>");

print("<form method=post action=takedelreq.php>");
print("<table width=100% cellspacing=0 cellpadding=3 class=table_table>\n");
print("<tr><td class=table_head align=left>" . REQUESTS . "</td><td class=table_head align=center>" . TYPE . "</td><td class=table_head align=center width=150>" . DATE_ADDED . "</td><td class=table_head align=center>" . ADDED_BY . "</td><td class=table_head align=center>" . FILLED . "</td><td class=table_head align=center>" . FILLED_BY . "</td><td class=table_head align=center>" . VOTES . "</td><td class=table_head align=center>" . DEL . "</td></tr>\n");
for ($i = 0; $i < $num; ++$i)
{



 $arr = mysql_fetch_assoc($res);

$privacylevel = $arr["privacy"];

if ($arr["downloaded"] > 0)
   {
     $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 2);
     $ratio = "<font color=" . get_ratio_color($ratio) . "><b>$ratio</b></font>";
   }
   else if ($arr["uploaded"] > 0)
       $ratio = "Inf.";
   else
       $ratio = "---";


$res2 = mysql_query("SELECT username from users where id=" . $arr[filledby]);
$arr2 = mysql_fetch_assoc($res2);  
if ($arr2[username])
$filledby = $arr2[username];
else
$filledby = " ";     

if ($privacylevel == "strong"){
		if (get_user_class() >= UC_JMODERATOR){
			$addedby = "<td class=table_col2 align=center><a href=account-details.php?id=$arr[userid]><b>$arr[username] ($ratio)</b></a></td>";
		}else{
			$addedby = "<td class=table_col2 align=center><a href=account-details.php?id=$arr[userid]><b>$arr[username] (----)</b></a></td>";
		}
}else{
		$addedby = "<td class=table_col2 align=center><a href=account-details.php?id=$arr[userid]><b>$arr[username] ($ratio)</b></a></td>";
}

$filled = $arr[filled];
if ($filled){
$filled = "<a href=$filled><font color=green><b>Yes</b></font></a>";
$filledbydata = "<a href=account-details.php?id=$arr[filledby]><b>$arr2[username]</b></a>";
}
else{
$filled = "<a href=reqdetails.php?id=$arr[id]><font color=red><b>No</b></font></a>";
$filledbydata  = "<i>nobody</i>";
}

print("<tr><td class=table_col1 align=left><a href=reqdetails.php?id=$arr[id]><b>".h($arr[request])."</b></a></td>" .
"<td class=table_col2 align=center>$arr[cat]</td><td align=center class=table_col1>$arr[added]</td>$addedby<td class=table_col2>$filled</td><td class=table_col1>$filledbydata</td><td class=table_col2><a href=votesview.php?requestid=$arr[id]><b>$arr[hits]</b></a></td>");
if (($CURUSER[id] == $arr[userid]) || get_user_class() > UC_JMODERATOR){
 print("<td class=table_col1><input type=\"checkbox\" name=\"delreq[]\" value=\"" . $arr[id] . "\" /></td>");
} else {
 print("<td class=table_col1>&nbsp;</td>");
}
print("</tr>\n");

}

print("</table>\n");

print("<p align=right><input type=submit value=" . DO_DELETE . "></p>");
print("</form>");

echo $pagerbottom;
}else{
echo "" . REQUESTS_OFFLINE . "";
}
end_frame();

stdfoot();
die;

?>