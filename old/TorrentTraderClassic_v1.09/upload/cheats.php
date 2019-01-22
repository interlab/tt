<?

require "backend/functions.php";
dbconn(true);

stdhead("BIG Uploaders");
loggedinorreturn;

jmodonly();

if ($daysago && $megabts){

$timeago = 84600 * $daysago; //last 7 days
$bytesover = 1048576 * $megabts; //over 500MB Upped
$timenow = "time() - $timeago"; 

$result = mysql_query("select * FROM users WHERE UNIX_TIMESTAMP(" . get_dt_num() . ") - UNIX_TIMESTAMP(added) < '$timeago' AND status='confirmed' AND uploaded > '$bytesover' ORDER BY uploaded DESC "); 
$num = mysql_num_rows($result); // how many uploaders

begin_frame("Big Uploaders");
echo "<p>" . $num . " Users with found over last ".$daysago." days with more than ".$megabts." MB (".$bytesover.") Bytes Uploaded.</p>";

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
 echo "<td class=table_head>AVG Daily Upload</td>";
 echo "<td class=table_head>" . ACCOUNT_SEND_MSG . "</td>";
 echo "<td class=table_head>Joined</td>";
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
// $joindate = "$added (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($added)) . " ago)";
  $joindate = "" . get_elapsed_time(sql_timestamp_to_unix_timestamp($added)) . " ago";
  //get uploader torrents activity
  $upperquery = "SELECT added FROM torrents WHERE owner = $id";
  $upperresult = mysql_query($upperquery);

  //$dayUpload   = $user["uploaded"];
 // $dayDownload = $user["downloaded"];

$seconds = mkprettytime(strtotime("now") - strtotime($added));
$days = explode("d ", $seconds);
if(sizeof($days) > 1) {
$dayUpload  = $uploaded / $days[0];
$dayDownload = $downloaded / $days[0];
}
 
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

  echo "<td class=table_col1>" . mksize($dayUpload) . "</td>";

  echo "<td align=center class=table_col2><a href=account-inbox.php?receiver=$username><img src=images/button_pm.gif border=0></a></td>";
  echo "<td class=table_col1>" . $joindate . "</td>";
 echo "</tr>";

 
 }
echo "</table><br><br>";
end_frame();
}

if ($num == 0)
{
end_frame();
}

}else{
begin_frame();?>
<form action='<?=$PHP_SELF?>' method='post'>
	Number of days joined: <input type='text' size='4' maxlength='4' name='daysago'> Days<br />
	MB Uploaded: <input type='text' size='6' maxlength='6' name='megabts'> MB<br />
	<input type='submit' value='   Submit   ' style='background:#eeeeee'>&nbsp;&nbsp;&nbsp;<input type='reset' value='  Reset  ' style='background:#eeeeee'>
	</form><?
end_frame();
}

stdfoot();

?>