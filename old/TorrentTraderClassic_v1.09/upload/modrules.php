<?
require_once("backend/functions.php");
dbconn(false);
loggedinorreturn();
jmodonly();

//ADD NEW RULE SECTION PAGE/FORM
if ($_GET["act"] == "newsect")
{
stdhead("Add Rule Section");
require_once("backend/admin-functions.php");
adminmenu();
begin_frame("Add Rule Section");

print("<form method=\"post\" action=\"modrules.php?act=addsect\">");
print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"10\" align=\"center\">\n");
print("<tr><td>Section Title:</td><td><input style=\"width: 400px;\" type=\"text\" name=\"title\"/></td></tr>\n");
print("<tr><td style=\"vertical-align: top;\">Rules:<br><a href=tags.php>[BB Tags]</a></td><td><textarea cols=90 rows=20 name=\"text\"></textarea><br>\n");
print("<br><a href=tags.php>[BB Tags]</a> are <b>on</b></td></tr>\n");

print("<tr><td colspan=\"2\" align=\"center\"><input type=\"radio\" name='public' value=\"yes\" checked>For everybody<input type=\"radio\" name='public' value=\"no\">&nbsp;Members Only - (Min User Class: <input type=\"text\" name='class' value=\"0\" size=1>)</td></tr>\n");
print("<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"Add\" style=\"width: 60px;\"></td></tr>\n");
print("</table></form>");
end_frame();
stdfoot();
}
//ADD NEW RULE SECTION TO DATABASE
elseif ($_GET["act"]=="addsect"){
$title = sqlesc($_POST["title"]);
$text = sqlesc($_POST["text"]);
$public = sqlesc($_POST["public"]);
$class = sqlesc($_POST["class"]);
mysql_query("insert into rules (title, text, public, class) values($title, $text, $public, $class)") or sqlerr(__FILE__,__LINE__);
header("Refresh: 0; url=modrules.php");
}
//EDIT RULE
elseif ($_GET["act"] == "edit"){
$id = (int) $_POST["id"];
$res = @mysql_fetch_array(@mysql_query("select * from rules where id='$id'"));
stdhead("Edit Rules");
require_once("backend/admin-functions.php");
adminmenu();
begin_frame("Edit Rule Section");

print("<form method=\"post\" action=\"modrules.php?act=edited\">");
print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"10\" align=\"center\">\n");
print("<tr><td>Section Title:</td><td><input style=\"width: 400px;\" type=\"text\" name=\"title\" value=\"$res[title]\" /></td></tr>\n");
print("<tr><td style=\"vertical-align: top;\">Rules:<br><a href=tags.php>[BB Tags]</a></td><td><textarea cols=90 rows=20 name=\"text\">" . stripslashes($res["text"]) . "</textarea><br><a href=tags.php>[BB Tags]</a> are <b>on</b></td></tr>\n");

print("<tr><td colspan=\"2\" align=\"center\"><input type=\"radio\" name='public' value=\"yes\" ".($res["public"]=="yes"?"checked":"").">For everybody<input type=\"radio\" name='public' value=\"no\" ".($res["public"]=="no"?"checked":"").">Members Only (Min User Class: <input type=\"text\" name='class' value=\"$res[class]\" size=1>)</td></tr>\n");
print("<tr><td colspan=\"2\" align=\"center\"><input type=hidden value=$res[id] name=id><input type=\"submit\" value=\"Save\" style=\"width: 60px;\"></td></tr>\n");
print("</table>");
end_frame();
stdfoot();
}
//DO EDIT RULE, UPDATE DB
elseif ($_GET["act"]=="edited"){
$id = (int) $_POST["id"];
$title = sqlesc($_POST["title"]);
$text = sqlesc($_POST["text"]);
$public = sqlesc($_POST["public"]);
$class = sqlesc($_POST["class"]);
mysql_query("update rules set title=$title, text=$text, public=$public, class=$class where id=$id") or sqlerr(__FILE__,__LINE__);
header("Refresh: 0; url=modrules.php");
}
else{
// STANDARD MENU OR HOMEPAGE ETC
$res = mysql_query("select * from rules order by id");
stdhead();
require_once("backend/admin-functions.php");
adminmenu();
begin_frame("Site Rules Editor");
print("<br><table width=100% border=0 cellspacing=0 cellpadding=10>");
print("<tr><td align=center><a href=modrules.php?act=newsect>Add New Rules Section</a></td></tr></table>\n");

begin_frame("Current Rules");

while ($arr=mysql_fetch_assoc($res))
{
begin_frame($arr[title]);
print("<form method=post action=modrules.php?act=edit&id=><table width=95% border=0 cellspacing=0 cellpadding=>");
print("<tr><td width=100%>");
print(format_comment($arr["text"]));
print("</td></tr><tr><td><input type=hidden value=$arr[id] name=id><input type=submit value='Edit'></td></tr></table></form>");
end_frame();
}

echo "<br><br>";

end_frame();

echo "<br><br>";

end_frame();

stdfoot();
}
?>