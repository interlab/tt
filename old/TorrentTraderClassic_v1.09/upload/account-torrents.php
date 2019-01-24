<?
require_once("backend/functions.php");
dbconn(false);

loggedinorreturn();

//start table function
function maketable($res)
{
 $ret = "<table cellpadding=2 cellspacing=0 style='border-collapse: collapse' bordercolor=#646262 width=95% border=1>" .
   "<tr><td class=colhead>File Name</td><td class=colhead align=center>Size</td><td class=colhead align=center>Uploaded</td>\n" .
   "<td class=colhead align=center>Downloaded</td><td class=colhead align=center>Ratio</td></tr>\n";
 while ($arr = mysql_fetch_assoc($res))
 {
   $res2 = mysql_query("SELECT name,size FROM torrents WHERE id=$arr[torrent] ORDER BY name");
   $arr2 = mysql_fetch_assoc($res2);
   if ($arr["downloaded"] > 0)
   {
     $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 2);
     $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
   }
   else
     if ($arr["uploaded"] > 0)
       $ratio = "Inf.";
     else
       $ratio = "---";
       
   $ret .= "<tr><td><a href=torrents-details.php?id=$arr[torrent]&amp;hit=1><b>" . h($arr2[name]) . "</b></a></td><td align=center>" . mksize($arr2["size"]) . "</td><td align=center>" . mksize($arr["uploaded"]) . "</td><td align=center>" . mksize($arr["downloaded"]) . "</td><td align=center>$ratio</td></tr>\n";
 }
 $ret .= "</table>\n";
 return $ret;
}
// end table function


//start SQL Queries for data generation
$id = (int)$_GET["id"];

if (!is_valid_id($id))
 bark("Can't show details", "Bad ID.");

//get user details
$r = @mysql_query("SELECT * FROM users WHERE id=$id") or sqlerr();
$user = mysql_fetch_array($r) or  bark("Can't show details", "No user with ID $id.");

//get torrents
$r = mysql_query("SELECT * FROM torrents WHERE owner=$id ORDER BY name ASC") or sqlerr();
if (mysql_num_rows($r) > 0)
{
 $torrents = "<table cellpadding=2 cellspacing=0 style='border-collapse: collapse' bordercolor=#646262 width=95% border=1>\n" .
   "<tr><td class=colhead>File Name</td><td class=colhead>Seeders</td><td class=colhead>Leechers</td></tr>\n";
 while ($a = mysql_fetch_assoc($r))
 {
   $smallname =substr(h($a["name"]) , 0, 100);
   if ($smallname != h($a["name"])){
       $smallname .= '...';
   }
     $torrents .= "<tr><td><a href=torrents-details.php?id=" . $a["id"] . "&hit=1><b>" . $smallname . "</b></a></td>" .
       "<td align=center><font color=green>$a[seeders]</font></td><td align=center><font color=red>$a[leechers]</font></td></tr>\n";
 }
 $torrents .= "</table>";
}


//get leeching info
$res = mysql_query("SELECT torrent,uploaded,downloaded FROM peers WHERE ip='$user[ip]' AND seeder='no'");
if (mysql_num_rows($res) > 0)
 $leeching = maketable($res);

//get seeding info
$res = mysql_query("SELECT torrent,uploaded,downloaded FROM peers WHERE ip='$user[ip]' AND seeder='yes'");
if (mysql_num_rows($res) > 0)
 $seeding = maketable($res);


// ****** start page generation *******//

stdhead();

begin_frame("File Transfer Details For " . $user[username] . "");
echo "<br><br><CENTER><a href=account-details.php?id=" . $user["id"] . ">Return To Account Details</a></CENTER><BR>";


echo "<table border=0 width=80%>";

$completedls = mysql_query("SELECT torrent FROM downloaded WHERE user = ".$id." ORDER BY torrent ASC ");

if(mysql_num_rows($completedls) < 1) {
print("<tr valign=top><td><B>Downloaded Torrents: </B> <br></td><td align=left>This member has not downloaded any Torrents</td></tr>\n");
}else{
$finished = "<table cellpadding=2 cellspacing=0 style='border-collapse: collapse' bordercolor=#646262 width=95% border=1>\n" .
   "<tr><td class=colhead>Torrent Name</td><td class=colhead>Torrent Ratio</td></tr>\n";
$torrent = mysql_query("SELECT * FROM snatched WHERE userid = ".$id." AND finished  = 'yes' ORDER BY torrent ASC ");
while($tor = mysql_fetch_array($torrent)) {
if($tor[downloaded] == 0) {
$ratio = "Inf.";
}elseif($tor[uploaded] == 0) {
$ratio = "<font color=red>0.00</font>";
}else{
$ratio = number_format($tor["uploaded"] / $tor["downloaded"], 2);
     $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
}
$torrent2 = mysql_query("SELECT * FROM torrents WHERE id = ".$tor["torrent"]." ");
$tor2 = mysql_fetch_array($torrent2);
   $smallname =substr(h($tor2["name"]) , 0, 60);
   if ($smallname != h($tor2["name"])){
       $smallname .= '...';
   }
$finished .= "<tr><td><b><a href='torrents-details.php?id=".$tor["torrent"]."'>".$smallname."</a></b></td>" .
       "<td align=center>".$ratio."</td></tr>\n";

}

}
print("<tr>&nbsp;</tr><tr valign=top><td><B>Downloaded Torrents:</B> </td><td align=left>$finished</td></tr>\n");
echo "</table>";


if ($torrents){
print("<tr valign=top><td><B>Posted Torrents:</B> </td><td align=left>$torrents</td></tr>\n");
}else{
print("<tr valign=top><td><B>Posted Torrents:</B> </td><td align=left>No Torrents Have Been Posted By This User</td></tr>\n");
}

if (get_user_class() >= UC_JMODERATOR)
{
print("<tr valign=top><td><B>&nbsp;</B> <br></td><td align=left>&nbsp;</td></tr>\n");
print("<tr valign=top><td><B>&nbsp;</B> <br></td><td align=left><I><font color=green>Seeding / Leeching information is only viewable by staff.</font></I></td></tr>\n");
print("<tr valign=top><td><B>&nbsp;</B> <br></td><td align=left>&nbsp;</td></tr>\n");
if ($seeding){
 print("<tr valign=top><td><B>Currently Seeding:</B> <br></td><td align=left>$seeding</td></tr>\n");
}else{
 print("<tr valign=top><td><B>Currently Seeding:</B> <br></td><td align=left>No Torrents Are Currently Being Seeded By This User</td></tr>\n");
}

if ($leeching){
 print("<tr valign=top><td><B>Currently Leeching:</B> <br></td><td align=left>$leeching</td></tr>\n");
}else{
 print("<tr valign=top><td><B>Currently Leeching:</B> <br></td><td align=left>No Torrents Are Currently Being Leeched By This User</td></tr>\n");
}
}

echo "</table><br>";
end_frame();

stdfoot();
?>