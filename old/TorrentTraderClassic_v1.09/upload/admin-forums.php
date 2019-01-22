<?
ob_start();
require_once("backend/functions.php");
dbconn(false);
loggedinorreturn();
adminonly();

stdhead("Edit Forums");
require_once("backend/admin-functions.php");

begin_frame();
echo"<BR><BR><center><a href=admin.php>RETURN TO STAFF CP</a></center><BR><BR>";

//Delete Forums
if($_GET['action'] == 'delforum'){
	$delid = $_GET['forumid'];
	$name = $_GET['forumname'];
	$sure = $_GET['sure'];
	if($sure == 'yes') {
		$query = "DELETE FROM forum_forums WHERE id = " .sqlesc($delid) . " LIMIT 1";
		$sql= mysql_query($query);
		echo("Forum, '$name' has been succesfully deleted! [ <a href='admin-forums.php'>Back to Edit</a> ]");
		end_frame();
		stdfoot();
		die();
	}else{
		if($delid >= 0) {
			echo("Are you sure you want to delete forum '$name'?
			( <strong><a href='". $_SERVER['PHP_SELF'] . "?action=delforum&forumid=$delid&forumname=$name&sure=yes'>Y</a></strong>
			/ <strong><a href='". $_SERVER['PHP_SELF'] . "'>N</a></strong> )");
			}
	}
	end_frame();
	stdfoot();
	die();
}

//Add new forum

if($_GET['action'] == 'add') {
	echo("<a href='admin-forums.php'><strong>Back</strong></a>");
	echo("<br><form name='add' method='get' action='". $_SERVER['PHP_SELF'] ."'>");
	echo("<input type='hidden' name='action' value='takeadd'>");
	echo("<table cellspacing=0 cellpadding=5 width=50%>");
	echo("<tr><td>Name: </td><td align='center'><input type='text' size=50 name='name'></td></tr>");
	echo("<tr><td>Description: </td><td align='center'><input type='text' size=50 name='desc'></td></tr>");
	echo("<tr><td>Sort: </td><td align='center'><input type='text' size=50 name='sort'></td></tr>");
	echo("<tr><td>Viewable By: </td><td align=center><select name=viewby>");
	for($i=0; $i<7; $i++)
	{
	echo("<option value=$i>". get_user_class_name($i) ."</option>/n");
	}
	echo("</select></td></tr>\n");
	echo("<tr><td>Able to post: </td><td align=center><select name=apost>");
	for($i=0; $i<7; $i++)
	{
	echo("<option value=$i>". get_user_class_name($i) ."</option>/n");
	}
	echo("</select></td></tr>\n");

	echo("<tr><td colspan=2><div align='center'><input type='Submit'></div></td></tr></table>");
}

if($_GET['action'] == 'takeadd') {
	$sort = $_GET['sort'];
	$name = $_GET['name'];
	$desc = $_GET['desc'];
	$viewby = $_GET['viewby'];
	$apost = $_GET['apost'];
	if(!$sort || !$name || !$desc) {
		echo("Please fill in all required fields <a href='admin-forums.php?action=add'><strong>back</strong></a>");
		end_frame();
		stdfoot();
		die();
	}
	$query = "SELECT sort FROM forum_forums WHERE sort = $sort";
	$loc = mysql_query($query);
	if(mysql_num_rows($loc) > '0')	{
		echo("That sort number already exists! <a href='admin-forums.php?action=add'><strong>back</strong></a>");
	}else{
		$query = "INSERT INTO forums SET
		sort = '$sort',
		name = '$name',
		description = '$desc',
		minclasswrite = '$viewby',
		minclassread = '$apost'";
		mysql_query($query) or sqlerr(__FILE__,__LINE__);
		echo("Forum added <a href='admin-forums.php?action=add'><strong>back</strong></a>");
	}
	end_frame();
	stdfoot();
	die();
}

// Edit forum

if($_GET['action'] == 'editforum') {
$edid = $_GET['forumid'];
$query = "SELECT * FROM forum_forums WHERE id = $edid";
$loc = mysql_query($query);
$row = mysql_fetch_assoc($loc);

echo("<a href='admin-forums.php'><strong>Back</strong></a>");
echo("<br><form name='add' method='get' action='". $_SERVER['PHP_SELF'] ."'>");
echo("<input type='hidden' name='action' value='takeedit'>");
echo("<input type='hidden' name='forumid' value='$edid'>");
echo("<table cellspacing=0 cellpadding=5 width=50%>");
echo("<tr><td>Name: </td><td align='center'><input type='text' value='".$row['name']."' size=50 name='name'></td></tr>");
echo("<tr><td>Description: </td><td align='center'><input type='text' value='".$row['description']."' size=50 name='desc'></td></tr>");
echo("<tr><td>Sort: </td><td align='center'><input type='text' value='".$row['sort']."' size=50 name='sort'></td></tr>");

echo("<tr><td>Viewable by:</td><td align=center><select name=viewby>\n");
for($i=0; $i<7; $i++)
{
echo("<option value ='$i'". ($i == $row['minclassread'] ? "selected" : "") .">".get_user_class_name($i)."</option>\n");
}
echo("</select> or higher</td></tr>");
echo("<tr><td>Able to post:</td><td align=center><select name=apost>\n");
for($i=0; $i<7; $i++)
{
echo("<option value ='$i'". ($i == $row['minclasswrite'] ? "selected" : "") .">" . get_user_class_name($i) . "</option>\n");
}
echo("</select> or higher</td></tr>");
echo("<tr><td colspan=2><div align='center'><input type='Submit'></div></td></tr></table>");
}

if($_GET['action'] == 'takeedit') {
	$newname = $_GET['name'];
	$newdesc = $_GET['desc'];
	$newsort = $_GET['sort'];
	$newread = $_GET['viewby'];
	$newwrite = $_GET['apost'];
	$id = $_GET['forumid'];
	$query = "SELECT * FROM forum_forums WHERE id = $id";
	$loc = mysql_query($query);
	$row = mysql_fetch_assoc($loc);
	$query2 = "SELECT sort FROM forum_forums";
	$loc2 = mysql_query($query2);
	while($row2 = mysql_fetch_assoc($loc2))
	{
	if($newsort == $row2['sort'] && $row['sort'] !== $newsort)
	{
	$se = 'TRUE';
	}
	}
	if($newname == $row['name'] && $newdesc == $row['description'] && $newsort == $row['sort'] && $newread == $row['minclassread']
	&& $newwrite == $row['minclasswrite'])
	echo("No change has been made <a href='admin-forums.php?action=editforum&forumid=$id'><strong>back</strong></a><br>");
	elseif($se == 'TRUE')
	echo("This sort number already exists <a href='admin-forums.php?action=editforum&forumid=$id'><strong>back</strong></a><br>");
	else{

	if($newname != $row['name'])
	$updateset[] = "name = ". sqlesc($newname);
	if($newdesc != $row['description'])
	$updateset[] = "description = ". sqlesc($newdesc);
	if($newsort != $row['sort'])
	$updateset[] = "sort = $newsort";
	if($newread != $row['minclassread'])
	$updateset[] = "minclassread = $newread";
	if($newwrite != $row['minclasswrite'])
	$updateset[] = "minclasswrite = $newwrite";

	//echo $updateset[0] . "<br>" . $updateset[1] . "<br>";

	mysql_query("UPDATE forum_forums SET " . implode(", ", $updateset) . " WHERE id=$id") or sqlerr(__FILE__, __LINE__);

	echo("Forum updated! <a href='admin-forums.php'><strong>back</strong></a><br>");
	}
	end_frame();
	stdfoot();
	die();
}

// Current Forums

echo("<br><table cellspacing=0 cellpadding=3 border=1>");
echo("<tr>
<td class=colhead>Sort:</td>
<td class=colhead>ID:</td>
<td class=colhead>Category:</td>
<td class=colhead>Name:</td>
<td class=colhead>Description:</td>
<td class=colhead>Viewable By:</td>
<td class=colhead>Able To Post:</td>
<td class=colhead>Edit:</td>
<td class=colhead>Delete:</td>
</tr>");
$query = "SELECT * FROM forum_forums ORDER BY sort ASC";
$loc = mysql_query($query);
while($row = mysql_fetch_array($loc))
{
$id = $row['id'];
$sort = $row['sort'];
$category = $row['category'];
$name = $row['name'];
$desc = $row['description'];
$minr = $row['minclassread'];
$minw = $row['minclasswrite'];
echo"<tr>
<td><strong>$sort</strong></td><td><strong>$id</strong></td><td><strong>$category</strong></td><td><a href='forums.php?action=viewforum&forumid=$id'>$name</a></td><td>$desc</td>
<td>".get_user_class_name($minr)."s or higher</td>
<td>".get_user_class_name($minw)."s or higher</td>
<td><div align='center'><a href='admin-forums.php?action=editforum&forumid=$id'>
<img src='$SITEURL/images/edit.gif' border='0' /></a></div></td>
<td><div align='center'><a href='admin-forums.php?action=delforum&forumid=$id&forumname=$name'>
<img src='$SITEURL/images/delete.gif' border='0' /></a></div></td>
</tr>";
}
echo"</table>";
if(!$_GET['action'])
{
echo"<br><a href=".$_SERVER['PHP_SELF']."?action=add>Add new Forum</a><br><br>";
}else{
echo"<br><br>";
}
end_frame();
stdfoot(); 
?>