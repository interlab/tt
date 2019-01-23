<?php

ob_start();
require_once("backend/functions.php");

dbconn(false);
loggedinorreturn();
modonly();

stdhead("Categories");
require_once("backend/admin-functions.php");
adminmenu();
begin_frame($txt['CATEGORIES']);

//DELETE CAT
$sure = $_GET['sure'];
if($sure == "yes") {
$delid = $_GET['delid'];
$query = "DELETE FROM categories WHERE id='$delid' LIMIT 1";
$sql = mysql_query($query);
echo("<br><BR><center>Category deleted succesfully! [<a href='admin-category.php'>Back</a>]</center><br>");
end_frame();
stdfoot();
die();
}
$delid = $_GET['delid'];
$name = $_GET['cat'];
if($delid > 0) {
echo("<br><center>Are you sure you would like to delete this category? <br>($name)<BR><br> ( <strong><a href='admin-category.php?delid=$delid&cat=$name&sure=yes'>Y</a></strong> / <strong><a href='admin-category.php'>N</a></strong> )<br><BR><i>Please note: If you delete a category there will be no category assigned to torrents already in that category</i></center>");
end_frame();
stdfoot();
die();

}

//EDIT CAT
$edited = $_GET['edited'];
if($edited == 1) {
$id = $_GET['id'];
$cat_name = $_GET['cat_name'];
$cat_img = $_GET['cat_pic'];
$sort_index = $_GET['sort_index'];
$query = "UPDATE categories SET
sort_index = '$sort_index',
name = '$cat_name',
image = '$cat_img' WHERE id='$id'";
$sql = mysql_query($query);
if($sql) {
echo("<CENTER><table cellspacing=0 cellpadding=5 width=50%>");
echo("<tr><td><div align='center'><br><br>Your category has been edited <strong>succesfully!</strong><br> [<a href='admin-category.php'>Back</a>]</div></tr>");
echo("</table><BR><BR></CENTER>");
end_frame();
stdfoot();
die();
}
}

$editid = $_GET['editid'];
$name = $_GET['name'];
$img = $_GET['img'];
$sort_index = $_GET['sort_index'];
if($editid > 0) {
print("<strong>Edit Category!</strong><br><i>Please note that if no image is specified, the category name will be displayed</i>");
print("<br />");
print("<br />");
echo("<form name='form1' method='get' action='admin-category.php'>");
echo("<table cellspacing=0 cellpadding=5 width=75%>");
echo("<div align='center'><input type='hidden' name='edited' value='1'>Now editing category <strong>&quot;$name&quot;</strong></div>");
echo("<br>");
echo("<input type='hidden' name='id' value='$editid'<table cellspacing=0 cellpadding=5 width=50%>");
echo("<tr><td><B>Category Name:</B> </td><td align='left'><input type='text' size=50 name='cat_name' value='$name'></td></tr>");
echo("<tr><td><B>Image Filename:</B> </td><td align='left'><input type='text' size=50 name='cat_pic' value='$img'> <i>(/images/categories)</i></td></tr>");
echo("<tr><td><B>Sort Order:</B> </td><td align='left'><input type='text' size=50 name='sort_index' value='$sort_index'></td></tr>");
echo("<tr><td></td><td><div align='left'><input type='Submit' value='Apply Changes'></div></td></tr>");
echo("</table></form>");
echo "<BR><BR><CENTER>[<a href='admin-category.php'>Back</a>]</CENTER><BR>";
end_frame();
stdfoot();
die();
}

//DEFAULT PAGE VIEW FOLLOWS...

//ADD CATEGORY
$add = $_GET['add'];
if($add == 'true') {
$cat_name = $_GET['cat_name'];
$cat_img = $_GET['cat_pic'];
$sort_index = $_GET['sort_index'];
$query = "INSERT INTO categories SET
sort_index = '$sort_index',
name = '$cat_name',
image = '$cat_img'";
$sql = mysql_query($query);
if($sql) {
$success = TRUE;
} else {
$success = FALSE;
}
}
print("<strong>Add A New Category!</strong><br><i>Please note that if no image is specified, the category name will be displayed</i>");
print("<br />");
print("<br />");
echo("<form name='form1' method='get' action='admin-category.php'>");
echo("<table cellspacing=0 cellpadding=5 width=75%>");
echo("<tr><td><B>Category Name:</B> </td><td align='left'><input type='text' size=50 name='cat_name'></td></tr>");
echo("<tr><td><B>Image Filename:</B> </td><td align='left'><input type='text' size=50 name='cat_pic'> <i>(/images/categories)</i><input type='hidden' name='add' value='true'></td></tr>");
echo("<tr><td><B>Sort Order:</B> </td><td align='left'><input type='text' size=50 name='sort_index'></td></tr>");
echo("<tr><td></td><td><div align='left'><input type='Submit' value='Add New Category'></div></td></tr>");
echo("</table>");
if($success == TRUE) {
print("<strong>Success! New Category added.</strong>");
}
echo("<br>");
echo("</form>");

//VIEW EXISTING CATEGORY TABLE

print("<HR><strong>Existing Categories:</strong><br><i>Please note that if no image is specified, the category name will be displayed</i>");
print("<br><br>");
echo("<center><table cellspacing=0 cellpadding=3 width=75% style='border-collapse: collapse' bordercolor=#646262 border=1>");
echo("<td width=100><B>ORDER PRIORITY</B></td><td><B>NAME</B></td><td><B>IMAGE</B></td><td width=30><B>EDIT</B></td><td width=30><B>DELETE</B></td>");
$query = "SELECT * FROM categories WHERE 1=1 ORDER BY sort_index ASC";
$sql = mysql_query($query);
while ($row = mysql_fetch_array($sql)) {
$id = $row['id'];
$name = $row['name'];
$priority = $row['sort_index'];
$img = $row['image'];
echo("<tr> <td><strong>$priority</strong></td><td><strong><a href='index.php?cat=$id'>$name</a></strong></td> <td><img src='".$SITEURL."/images/categories/$img' border=0></td> <td><a href='admin-category.php?editid=$id&name=$name&img=$img&sort_index=$priority'><div align='center'><img src='images/edit.gif' alt=edit border=0></a></div></td> <td><div align='center'><a href='admin-category.php?delid=$id&cat=$name'><img src='images/delete.gif' alt=delete align='center' border=0></a></div></td></tr>");
}
echo("</table></center>");
echo "<BR><BR><CENTER>[<a href='admin.php'>Back To Admin CP</a>]</CENTER><BR>";
end_frame();

stdfoot();
?>