<?
//
// CCS theme done 30.11.05
//
require "backend/functions.php";
dbconn(false);
loggedinorreturn();
jmodonly();

stdhead("Multiple IPs");
require_once("backend/admin-functions.php");
adminmenu();


$res2 = mysql_query("SELECT count(ip) as count FROM users GROUP BY ip HAVING COUNT(ip)>1 AND sum(downloaded) > 0") or sqlerr(__FILE__, __LINE__);
while ($arr2 = mysql_fetch_assoc($res2))
   {
    $count = $count+1;    
   }
 
$perpage = 100;//number to show per page

list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] ."?" );

$res = mysql_query("SELECT  sum(uploaded) as uploaded, sum(downloaded) as downloaded, ip, sum(uploaded)/sum(downloaded) as ratio, count(ip) as count FROM users GROUP BY ip HAVING COUNT(ip)>1 AND sum(downloaded) > 0 ORDER BY ratio asc $limit") or sqlerr(__FILE__, __LINE__);

begin_frame("Duplicate IP Checker");

if (mysql_num_rows($res) == 0)
	print("<br><BR><center><b><font color=red>Nothing to show</font></b><BR></center>\n");
else
{
echo $pagertop;

print("<center><table border=1 cellspacing=0 cellpadding=2 class=table_table>\n");
print("<tr><td class=table_head align=left>IP</td><td class=table_head align=left>Combined Ratio</td><td class=table_head align=left>Count</td><td class=table_head align=left>Enabled</td><td class=table_head align=left>Disabled</td></tr>\n");
   while ($arr = mysql_fetch_assoc($res))
   {
     if($arr[ip]!="")
 {
$host = @gethostbyaddr($arr[ip]);
if(!(stristr($host, "aol")) && !(stristr($host, "cache"))&& !(stristr($host, "proxy")))
{ 
 $r = mysql_query("SELECT count(id) FROM users WHERE enabled = 'no' AND ip = '$arr[ip]' UNION SELECT count(id) FROM users WHERE enabled = 'yes' AND ip = '$arr[ip]'") or sqlerr();
$a = mysql_fetch_row($r);
$disabled = number_format(0 + $a[0]);
$a = mysql_fetch_row($r);
$enabled = number_format(0 + $a[0]);
 $nip = ip2long($arr[ip]);
       $auxres = mysql_query("SELECT COUNT(*) FROM bans WHERE $nip >= first AND $nip <= last") or sqlerr(__FILE__, __LINE__);
       $array = mysql_fetch_row($auxres);
      if ($array[0] == 0)
       $ipstr = "<a href='/iptest.php?ip=" . $arr[ip] . "'><font color=darkgreen><b>Not Banned</b></font></a>";
     else
       $ipstr = "<a href='/iptest.php?ip=" . $arr[ip] . "'><font color='#FF0000'><b>IP Banned</b></font></a>";
   if ($arr["downloaded"] > 0)
   {
     $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
     $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
   }
   else
     if ($arr["uploaded"] > 0)
       $ratio = "Inf.";
     else
       $ratio = "---";

print("<tr><td class=table_col1><a href=admin-search.php?ip=$arr[ip]>$arr[ip]</a> ($host) - $ipstr</td><td class=table_col2>$ratio<td class=table_col1>$arr[count]</td><td class=table_col2><font color=darkgreen><b>$enabled</b></font></td><td class=table_col1><font color=red><b>$disabled</b></font></td></tr>\n");}
}}
   print("</table></center><br>");
 }

echo $pagerbottom;

end_frame();

stdfoot();
?>