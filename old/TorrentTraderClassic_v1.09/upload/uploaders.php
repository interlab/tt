<?php
//
// CSS and language updated 30.11.05
//
require "backend/functions.php";
dbconn(true);

stdhead("Uploaders");
require_once("backend/admin-functions.php");
loggedinorreturn;

if ($CURUSER['class'] >= UC_JMODERATOR)

{
adminmenu();
$query = "SELECT id, username, added, uploaded, downloaded, donated, warned FROM users WHERE class = 1 ORDER BY username";
$result = mysql_query($query);
$num = mysql_num_rows($result); // how many uploaders
begin_frame("Uploaders Info Panel");
echo "<p>" . $num . " uploaders total.</p>";

$zerofix = $num - 1; // remove one row because mysql starts at zero

if ($num > 0)
{
echo "<table align=center class=table_table>";
echo "<tr>";
 echo "<td class=table_head>No.</td>";
 echo "<td class=table_head>" . USERNAME . "</td>";
 echo "<td class=table_head>" . UPLOADED . "</td>";
 echo "<td class=table_head>" . DOWNLOADED . "</td>";
 echo "<td class=table_head>" . RATIO . "</td>";
 echo "<td class=table_head>" . TORRENTS_POSTED . "</td>";
 echo "<td class=table_head>" . LAST_SEEDED . "</td>";
 echo "<td class=table_head>" . ACCOUNT_SEND_MSG . "</td>";
echo "</tr>";

for ($i = 0; $i <= $zerofix; $i++)
 {
 $id = mysql_result($result, $i, "id");
 $username = mysql_result($result, $i, "username");
 $added = mysql_result($result, $i, "added");
 $uploaded = mysql_result($result, $i, "uploaded");
 $downloaded = mysql_result($result, $i, "downloaded");
 $donated = mysql_result($result, $i, "donated");
 $warned = mysql_result($result, $i, "warned");
 
  // get uploader torrents activity
  $upperquery = "SELECT added FROM torrents WHERE owner = $id";
  $upperresult = mysql_query($upperquery);
 
  $torrentinfo = mysql_fetch_array($upperresult);
 
  $numtorrents = mysql_num_rows($upperresult);
   
  if ($downloaded > 0)
   {
   $ratio = $uploaded / $downloaded;
   $ratio = number_format($ratio, 3);
   $color = get_ratio_color($ratio);
   if ($color)
   $ratio = "<font color=$color>$ratio</font>";
   }
  else
   if ($uploaded > 0)
    $ratio = "Inf.";
   else
    $ratio = "---";
   
 // get donor
 if ($dated >= "1")
  $star = "<img src=images/star.gif>";
 else
  $star = "";
 
 // get warned
 if ($warned == "yes")
  $klicaj = "<img src=images/warned.gif>";
 else
  $klicaj = "";
 
 $counter = $i + 1;
 
 echo "<tr>";
  echo "<td align=center class=table_col1>$counter.</td>";
  echo "<td class=table_col2><a href=account-details.php?id=$id>$username</a> $star $klicaj</td>";
  echo "<td class=table_col1>" . mksize($uploaded). "</td>";
  echo "<td class=table_col2>" . mksize($downloaded) . "</td>";
  echo "<td class=table_col1>$ratio</td>";
  if ($numtorrents == 0) echo "<td class=table_col2><font color=red>$numtorrents torrents</font></td>";
  else echo "<td class=table_col2>$numtorrents torrents</td>";
  if ($numtorrents > 0)
   {
   $lastadded = mysql_result($upperresult, $numtorrents - 1, "added");
   echo "<td class=table_col1>" . get_elapsed_time(sql_timestamp_to_unix_timestamp($lastadded)) . " ago (" . gmdate("d. M Y",strtotime($lastadded)) . ")</td>";
   }
  else
   echo "<td class=table_col1>---</td>";
  echo "<td align=center class=table_col2><a href=account-inbox.php?receiver=$username><img src=images/button_pm.gif border=0></a></td>";

 echo "</tr>";

 
 }
echo "</table><br><br>";
end_frame();
}
end_frame();
}

else
echo "Not permitted staff Only.";

stdfoot();

