<?
require "backend/functions.php";
dbconn(false);
loggedinorreturn();
stdhead();
begin_frame("News Archive");
?>
<?
$query = 'SELECT archive FROM news_options';
$resu = mysql_query($query) or die(mysql_error());
while ($row = mysql_fetch_array($resu)){
if ($row['archive'] == 'on'){
$query = 'SELECT id, title, user, date, text FROM news ORDER BY date DESC';
$resu = mysql_query($query) or die(mysql_error());
while ($row = mysql_fetch_array($resu)){
begin_frame("" . $row['title'] . "");
print("<I>Posted By " . $row['user'] . "</i> On " . $row['date'] . "\n");
echo'<BR>' . stripslashes($row['text']) . '';
end_frame();
 }
 }
 else
 {
         begin_frame("Error");
         print("News Archiving Has Been Disabled!");
         end_frame();
         }
         }

 end_frame();
 stdfoot();
?>