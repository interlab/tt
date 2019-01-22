<?
//
// - Theme And Language Updated 26.Nov.05
//
ob_start();
require "backend/functions.php";
dbconn();
loggedinorreturn();


$search = trim($HTTP_GET_VARS['search']);
$class = $HTTP_GET_VARS['class'];
if ($class == '-' || !is_numeric($class))
$class = '';

if ($search != '' || $class)
{
$query = "username LIKE " . sqlesc("%$search%") . " AND status='confirmed'";
if ($search)
$q = "search=" . htmlspecialchars($search);
}
else
{
$letter = trim($_GET["letter"]);
if (strlen($letter) > 1)
die;

  if ($letter == "" || strpos("abcdefghijklmnopqrstuvwxyz", $letter) === false)
    $query = "status='confirmed'";
  else
      $query = "username LIKE '$letter%' AND status='confirmed'";
  $q = "letter=$letter";
}

if ($class!='')
{
$query .= " AND class=$class";
$q .= ($q ? "&amp;" : "") . "class=$class";
}

stdhead("" . USERS . "");
begin_frame("" . MEMBERS . "", center);
print("<br /><form method=get action=?>\n");
print("" . SEARCH . ": <input type=text size=30 name=search>\n");
print("<select name=class>\n");
print("<option value='-'>(any class)</option>\n");
for ($i = 0;;++$i)
{
	if ($c = get_user_class_name($i))
	  print("<option value=$i" . ($class && $class == $i ? " selected" : "") . ">$c</option>\n");
	else
	  break;
}
print("</select>\n");
print("<input type=submit value='". SEARCH . "'>\n");
print("</form>\n");

print("<p>\n");

print("<a href=extras-users.php><b>" . ALL . "</b></a> - \n");
for ($i = 97; $i < 123; ++$i)
{
	$l = chr($i);
	$L = chr($i - 32);
	if ($l == $letter)
    print("<b>$L</b>\n");
	else
    print("<a href=?letter=$l><b>$L</b></a>\n");
}

print("</p>\n");

$page = $_GET['page'];
$perpage = 100;

$res = mysql_query("SELECT COUNT(*) FROM users WHERE $query") or sqlerr();
$arr = mysql_fetch_row($res);
$pages = floor($arr[0] / $perpage);
if ($pages * $perpage < $arr[0])
  ++$pages;

if ($page < 1)
  $page = 1;
else
  if ($page > $pages)
    $page = $pages;

for ($i = 1; $i <= $pages; ++$i)
  if ($i == $page)
    $pagemenu .= "$i\n";
  else
    $pagemenu .= "<a href=?$q&page=$i>$i</a>\n";

if ($page == 1)
  $browsemenu .= "";
//  $browsemenu .= "[Prev]";
else
  $browsemenu .= "<a href=?$q&page=" . ($page - 1) . ">[Prev]</a>";

$browsemenu .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

if ($page == $pages)
  $browsemenu .= "";
//  $browsemenu .= "[Next]";
else
  $browsemenu .= "<a href=?$q&page=" . ($page + 1) . ">[Next]</a>";

$offset = ($page * $perpage) - $perpage;

$res = mysql_query("SELECT * FROM users WHERE $query ORDER BY username LIMIT $offset,$perpage") or sqlerr();
$num = mysql_num_rows($res);

begin_table();
print("<tr><td class=ttable_head align=left>" . USERNAME . "</td><td class=ttable_head>" . REGISTERED . "</td><td class=ttable_head>" . LAST_ACCESS . "</td><td class=ttable_head>" . RANK . "</td><td class=ttable_head>" . COUNTRY . "</td></tr>\n");
for ($i = 0; $i < $num; ++$i)
{
  $arr = mysql_fetch_assoc($res);
  if ($arr['country'] > 0)
  {
    $cres = mysql_query("SELECT name,flagpic FROM countries WHERE id=$arr[country]");
    if (mysql_num_rows($cres) == 1)
    {
      $carr = mysql_fetch_assoc($cres);
      $country = "<td align=\"center\" class=ttable_col1 style='padding: 0px' align='center'><img src=". $SITEURL ."/images/flag/$carr[flagpic] alt='$carr[name]' /></td>";
    }
  }
  else
    $country = "<td align=\"center\"  class=ttable_col1 style='padding: 0px' align='center'><img src=". $SITEURL ."/images/flag/unknown.gif alt=Unknown /></td>";
  if ($arr['added'] == '0000-00-00 00:00:00')
    $arr['added'] = '-';
  if ($arr['last_access'] == '0000-00-00 00:00:00')
    $arr['last_access'] = '-';
  print("<tr><td class=ttable_col1 align=left><a href=account-details.php?id=$arr[id]>" .($arr["class"] > 1 ? "" : "")."<b>$arr[username]</b></a>" .($arr["donated"] > 0 ? "<img src=$SITEURL/images/star.gif border=0 alt='Donated'>" : "")."</td>" .
  "<td align=\"center\" class=ttable_col2>$arr[added]</td><td align=\"center\" class=ttable_col1>$arr[last_access]</td>".
    "<td class=ttable_col2 align=center>" . get_user_class_name($arr["class"]) . "</td>$country</tr>\n");
}
end_table();

print("<p>$pagemenu<br />$browsemenu</p>");
end_frame();
stdfoot();
die;

?>