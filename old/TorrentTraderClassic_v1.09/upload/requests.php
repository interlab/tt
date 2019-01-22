<?
//
// - Theme And Language Updated 25.Nov.05
//
ob_start("ob_gzhandler");
require "backend/functions.php";

dbconn();

loggedinorreturn();

stdhead("Requests Page");

begin_frame("" . MAKE_REQUEST . "");


print("<br>\n");

$where = "WHERE userid = " . $CURUSER["id"] . "";
$res2 = mysql_query("SELECT * FROM requests $where") or sqlerr();
$num2 = mysql_num_rows($res2);

?>



<table border=0 width=100% cellspacing=0 cellpadding=3>
<tr><td class=colhead align=left><? print("" . SEARCH . " " . TORRENT . ""); ?></td></tr>
<tr><td align=left><form method="get" action=torrents-search.php>
<input type="text" name="<? print("" . SEARCH . "\n"); ?>" size="40" value="<?= htmlspecialchars($searchstr) ?>" />
in
<select name="cat">
<option value="0">(all types)</option>
<?


$cats = genrelist();
$catdropdown = "";
foreach ($cats as $cat) {
   $catdropdown .= "<option value=\"" . $cat["id"] . "\"";
   if ($cat["id"] == (int)$_GET["cat"])
       $catdropdown .= " selected=\"selected\"";
   $catdropdown .= ">" . htmlspecialchars($cat["name"]) . "</option>\n";
}

$deadchkbox = "<input type=\"checkbox\" name=\"incldead\" value=\"1\"";
if ($_GET["incldead"])
   $deadchkbox .= " checked=\"checked\"";
$deadchkbox .= " /> " . INC_DEAD . "\n";

?>
<?= $catdropdown ?>
</select>
<?= $deadchkbox ?>
<input type="submit" value="<? print("" . SEARCH . "\n"); ?>"  />
</form>
</td></tr></table><BR><HR><BR>

<? print("<br>\n");

print("<form method=post action=takerequest.php><a name=add id=add></a>\n");
print("<CENTER><table border=0 width=600 cellspacing=0 cellpadding=3>\n");
print("<tr><td class=colhead align=center><B>" . MAKE_REQUEST . "</B></a></td><tr>\n");
print("<tr><td align=center><b>Title: </b><input type=text size=40 name=requesttitle>");
?>

<select name="category">
<option value="0">(Select a Category)</option>
<?

$res2 = mysql_query("SELECT id, name FROM categories  order by name");
$num = mysql_num_rows($res2);
$catdropdown2 = "";
for ($i = 0; $i < $num; ++$i)
   {
 $cats2 = mysql_fetch_assoc($res2);  
     $catdropdown2 .= "<option value=\"" . $cats2["id"] . "\"";
     $catdropdown2 .= ">" . htmlspecialchars($cats2["name"]) . "</option>\n";
   }

?>
<?= $catdropdown2 ?>
</select>

<? print("<br>\n");

print("<tr><td align=center>Additional Information (Optional)<br><textarea name=descr rows=7 cols=60></textarea>\n");
print("<tr><td align=center><input type=submit value='" . SUBMIT . "' style='height: 22px'>\n");
print("</form>\n");
print("</table></CENTER>\n");

end_frame();

stdfoot();
?>