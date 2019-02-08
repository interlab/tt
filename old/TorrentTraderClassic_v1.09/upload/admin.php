<?php

ob_start();
require_once("backend/functions.php");

dbconn(false);
loggedinorreturn();

jmodonly();
stdhead("Staff CP");
require_once("backend/admin-functions.php");

$act = $_REQUEST['act'] ?? '';
$_POST['submit'] = $_POST['submit'] ?? '';
$do = $_POST['do'] ?? '';
$error_ac = '';
$_SERVER['php_self'] = $_SERVER['php_self'] ?? '';

if (empty($act))
{
    adminmenu(); 
    begin_frame("Reported Items To Be Dealt With");

// Start reports block
$type = $_GET["type"] = $_GET["type"] ?? '';
if ($type == "user")
    $where = " WHERE type = 'user'";
elseif ($type == "torrent")
    $where = " WHERE type = 'torrent'";
elseif ($type == "forum")
    $where = " WHERE type = 'forum'";
else
    $where = "";


$count = DB::fetchColumn('SELECT count(id) FROM reports ' . $where);
$perpage = 25;
list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] . "?type=" . $_GET["type"] . "&" );

print("<br><center><a href=reports-complete.php>View Completed Reports</a></center><br>");

echo $pagertop;

print("<table border=1 cellspacing=0 cellpadding=1 align=center width=95%>\n");
print("<tr><td class=table_head align=center>By</td><td class=table_head align=center>Reported</td><td class=table_head align=center>Type</td><td class=table_head align=center>Reason</td><td class=table_head align=center>Dealt With</td><td class=table_head align=center>Mark Dealt With</td>");

if (get_user_class() >= UC_MODERATOR)
    printf("<td class=table_head align=center>Delete</td>");

print("</tr>");
print("<form method=post action=takedelreport.php>");

$res = DB::executeQuery('
    SELECT reports.id, reports.dealtwith, reports.dealtby, reports.addedby, reports.votedfor, reports.votedfor_xtra,
        reports.reason, reports.type, users.username, reports.complete
    FROM reports
        INNER JOIN users on reports.addedby = users.id
    ' . $where . '
        AND complete = ?
    ORDER BY id desc
    ' . $limit, [0]);

while ($arr = $res->fetch()) {
    if ($arr['dealtwith']) {
        $arr3 = DB::fetchAssoc("SELECT username FROM users WHERE id = $arr[dealtby]");
        $dealtwith = "<font color=green><b>Yes - <a href=account-details.php?id=$arr[dealtby]><b>$arr3[username]</b></a></b></font>";
    } else
        $dealtwith = "<font color=red><b>No</b></font>";
    if ($arr['type'] == "user") {
        $type = "account-details";
        $name = DB::fetchColumn("SELECT username FROM users WHERE id = $arr[votedfor]");
    } elseif  ($arr['type'] == "forum") {
        $type = "forums";
        $subject = DB::fetchColumn("SELECT subject FROM forum_topics WHERE id = $arr[votedfor]");
    } elseif ($arr['type'] == "torrent") {
        $type = "torrents-details";
        $name = DB::fetchColumn("SELECT name FROM torrents WHERE id = $arr[votedfor]");
        if ($name == '') {
            $name = "<b>[Deleted]</b>";
        }
    }

    if ($arr['type'] == "forum") {
        print("<tr><td><a href=account-details.php?id=$arr[addedby]><b>$arr[username]</b></a></td>
            <td align=left><a href=$type.php?action=viewtopic&topicid=$arr[votedfor]&page=p#$arr[votedfor_xtra]><b>$subject</b></a></td>
            <td align=left>$arr[type]</td>
            <td align=left>$arr[reason]</td><td align=left>$dealtwith</td>
            <td align=center><input type=\"checkbox\" name=\"delreport[]\" value=\"" . $arr['id'] . "\" /></td></tr>\n");
    }
    else {
        print("<tr><td><a href=account-details.php?id=$arr[addedby]><b>$arr[username]</b></a></td>
            <td align=left><a href=$type.php?id=$arr[votedfor]><b>$name</b></a></td>
            <td align=left>$arr[type]</td>
            <td align=left>$arr[reason]</td>
            <td align=left>$dealtwith</td>
            <td align=center><input type=\"checkbox\" name=\"delreport[]\" value=\"" . $arr['id'] . "\" /></td>\n");
        if (get_user_class() >= UC_MODERATOR) {
            printf("<td><a href=admin-delreport.php?id=$arr[id]>Delete</a></td>");
        }
        print("</tr>");
    }
}

print("</table>\n");

print("<p align=right><input type=submit value=Confirm></p>");
print("</form>");

echo $pagerbottom;

print("<center><b>There is now no need to DELETE any reports, all dealtwith reports are now archived via the cleanup system.</b></center><br>");
end_frame();
// End Reports block
}


#======================================================================#
# Donations Code
#======================================================================#
if ($act == "donations") {
	if ($do == "update") {
		DB::executeUpdate('
            UPDATE site_settings SET requireddonations = ?, donations = ?, donatepage = ?',
            [$_POST['ed_requireddonations'], $_POST['ed_donations'], $_POST['ed_donatepage']
        ]);
        bark2("Success", "Donations Updated OK", 'Success');
    }
    $row = DB::fetchAssoc("SELECT * FROM site_settings");

	adminonly();
	adminmenu();
	begin_frame("Donation Management", 'center');
	?>
	<form action='admin.php' method='post'>
	<input type='hidden' name='act' value='donations'>
	<input type='hidden' name='do' value='update'>
	Monthly Required:<br />
	<input type='text' value='<?= $row['requireddonations'] ?>' size='5' maxlength='5' name='ed_requireddonations'><br />
	Donations:<br>
	<input type='text' value='<?= $row['donations'] ?>' size='5' maxlength='5' name='ed_donations'><br />
	Donate Page Contents:<br />
    <textarea name='ed_donatepage' cols="50" rows="8"><?= $row['donatepage'] ?></textarea><br>
	<input type='submit' value='   Save   ' style='background:#eeeeee'>&nbsp;&nbsp;&nbsp;
    <input type='reset' value='  Reset  ' style='background:#eeeeee'>
	</form>
	<?php 

    end_frame();
}

#======================================================================#
# Tracker Load
#======================================================================#
if($act == "trackerload")
{
	adminmenu();
begin_frame("Tracker Load");
?>
<table width=100% border=0 cellspacing=0 cellpadding=10><tr><td align=center>
<table class=interiortable border=0 width=402><tr><td style='padding: 0px; background-repeat: repeat-x'>
<?php  $percent = min(100, round(exec('ps ax | grep -c apache') / 256 * 30 ));
echo "<br>Our Tracker Load: ($percent %)(these stats are an approximation)<table class=interiortable border=0 width=400><tr><td style='padding: 0px; background-image: url(images/loadbarbg.gif); background-repeat: repeat-x'>";

   if ($percent <= 70) $pic = "images/loadbargreen.gif";
    elseif ($percent <= 90) $pic = "images/loadbaryellow.gif";
     else $pic = "images/loadbarred.gif";
          $width = $percent * 8;
echo "<img height=15 width=$width src=\"$pic\" alt='$percent%'></td></tr></table>";
echo "" . trim(exec('uptime')) . "<br>";


 $percent = min(100, round(exec('ps ax | grep -c apache') / 256 * 150));
echo "<br>Global Server Load (All websites on current host servers): ($percent %)<table class=interiortable border=0 width=400><tr><td style='padding: 0px; background-image: url(images/loadbarbg.gif); background-repeat: repeat-x'>";

   if ($percent <= 70) $pic = "images/loadbargreen.gif";
    elseif ($percent <= 90) $pic = "images/loadbaryellow.gif";
     else $pic = "images/loadbarred.gif";
          $width = $percent * 8;
echo "<img height=15 width=$width src=\"$pic\" alt='$percent%'></td></tr></table></td></tr></table></td></tr></table>";
end_frame();
}

#======================================================================#
# Donations Information
#======================================================================#
if($act == "userdonations")
{
	adminonly();
	adminmenu();
	begin_frame("View Donations", 'center');
	$res = mysql_query("SELECT * FROM users WHERE donated >'0' ORDER BY username") or sqlerr();
$num = mysql_num_rows($res);
print("<center><br><br><table border=1 width=95% cellspacing=0 cellpadding=1>\n");
print("<tr align=center><td class=table_head width=90>User Name</td>
 <td class=table_head width=70>Registered</td>
 <td class=table_head width=75>Last Access</td>  
 <td class=table_head width=75>User Class</td>
 <td class=table_head width=70>Downloaded</td>
 <td class=table_head width=70>Uploaded</td>
 <td class=table_head width=45>Ratio</td>
 <td class=table_head width=45>Donated</td>
 <td class=table_head width=225>Moderator Comments</td>
</tr>\n");
for ($i = 1; $i <= $num; $i++)
{
$arr = mysql_fetch_assoc($res);
if ($arr['added'] == '0000-00-00 00:00:00')
  $arr['added'] = '-';
if ($arr['last_access'] == '0000-00-00 00:00:00')
  $arr['last_access'] = '-';


if($arr["downloaded"] != 0){
$ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
} else {
$ratio="---";
}
$ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
  $uploaded = mksize($arr["uploaded"]);
  $downloaded = mksize($arr["downloaded"]);

$added = substr($arr['added'],0,10);
$last_access = substr($arr['last_access'],0,10);
$class=get_user_class_name($arr["class"]);

print("<tr><td align=left><a href=account-details.php?id=$arr[id]><b>$arr[username]</b></a>" .($arr["donated"] > 1 ? "<img src=/images/star.gif border=0 alt='Donor'>" : "")."</td>
  <td align=center>$added</td>
  <td align=center>$last_access</td>
  <td align=center>$class</td>
  <td align=center>$downloaded</td>
  <td align=center>$uploaded</td>
  <td align=center>$ratio</td>
  <td align=center>$arr[donated]</td>
  <td align=center>$arr[modcomment]</td></tr>\n");
}

print("</table>\n");
print("<p>$pagemenu<br>$browsemenu</p>");

end_frame();
}

#======================================================================#
# Peer Guardian Import 
#======================================================================#
if($act == "peerg")
{
	adminonly();
	adminmenu();
	begin_frame("Peer Guardian Importer", 'center');

	// change the following to your  .p2p location
	$f = fopen("hxxp://homepage.ntlworld.com/tim.leonard1/guarding.p2p", "r");
if (!$f)
  die("Cannot download guarding.p2p!");

mysql_query("DELETE FROM bans WHERE comment LIKE 'PeerGuardian: %'") or sqlerr(__FILE__, __LINE__);

$n = 0;
$o = 0;
$dt = sqlesc(get_date_time());
while (!feof($f))
{
  ++$o;
  $s = rtrim(fgets($f));
  $i = strrpos($s, ":");
  if (!$i) continue;
  $comment = sqlesc("PeerGuardian: " . substr($s, 0, $i));
  $s = substr($s, $i + 1);
  $i = strpos($s, "-");
  $first = ip2long(substr($s, 0, $i));
  $last = ip2long(substr($s, $i + 1));
  if ($first == -1 || $last == -1) continue;
  mysql_query("INSERT INTO bans (added, addedby, first, last, comment) VALUES($dt, $uid, $first, $last, $comment)") or sqlesc(__FILE__, __LINE__);
  ++$n;
}
$o -= $n;
print("$n ranges imported, $o line(s) was discarded.");

end_frame();
}

#======================================================================#
# Disabled Accounts
#======================================================================#
if($act == "disabledaccounts")
{
	adminmenu();
	begin_frame("Disabled Accounts", 'center');

    // todo
    $pagemenu = '';
    $browsemenu = '';

	$res = DB::query("SELECT * FROM users WHERE enabled = 'no' ORDER BY username");

    print("<center><br><br><table border=1 width=95% cellspacing=0 cellpadding=1>\n");
    print("<tr align=center><td class=table_head width=90>User Name</td>
        <td class=table_head width=70>Registered</td>
        <td class=table_head width=75>Last Access</td>  
        <td class=table_head width=75>User Class</td>
        <td class=table_head width=70>Downloaded</td>
        <td class=table_head width=70>Uploaded</td>
        <td class=table_head width=45>Ratio</td>
        <td class=table_head width=225>Moderator Comments</td>
        </tr>\n");

    while ($arr = $res->fetch()) {
        if ($arr['added'] == '0000-00-00 00:00:00')
            $arr['added'] = '-';
        if ($arr['last_access'] == '0000-00-00 00:00:00')
            $arr['last_access'] = '-';

        if ($arr["downloaded"] != 0) {
            $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
        } else {
            $ratio="---";
        }
        $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
        $uploaded = mksize($arr["uploaded"]);
        $downloaded = mksize($arr["downloaded"]);

        $added = substr($arr['added'],0,10);
        $last_access = substr($arr['last_access'], 0, 10);
        $class = get_user_class_name($arr["class"]);

        print("<tr><td align=left><a href=account-details.php?id=$arr[id]><b>$arr[username]</b></a>" .
            ($arr["donated"] > 1 ? "<img src=/images/star.gif border=0 alt='Donor'>" : "")."</td>
        <td align=center>$added</td>
        <td align=center>$last_access</td>
        <td align=center>$class</td>
        <td align=center>$downloaded</td>
        <td align=center>$uploaded</td>
        <td align=center>$ratio</td>
        <td align=center>$arr[modcomment]</td></tr>\n");
    }

    print("</table>\n");
    print("<p>$pagemenu<br>$browsemenu</p>");

    end_frame();
}

#======================================================================#
# Warned Accounts
#======================================================================#
if($act == "warneddaccounts")
{
	adminmenu();
	begin_frame("Warned Accounts", 'center');
	$res = mysql_query("SELECT * FROM users WHERE enabled='yes' AND warned='yes' ORDER BY username") or sqlerr();
$num = mysql_num_rows($res);
print("<center><br><br><table border=1 width=95% cellspacing=0 cellpadding=1>\n");
print("<tr align=center><td class=table_head width=90>User Name</td>
 <td class=table_head width=70>Registered</td>
 <td class=table_head width=75>Last Access</td>  
 <td class=table_head width=75>User Class</td>
 <td class=table_head width=70>Downloaded</td>
 <td class=table_head width=70>Uploaded</td>
 <td class=table_head width=45>Ratio</td>
 <td class=table_head width=225>Moderator Comments</td>
</tr>\n");
for ($i = 1; $i <= $num; $i++)
{
$arr = mysql_fetch_assoc($res);
if ($arr['added'] == '0000-00-00 00:00:00')
  $arr['added'] = '-';
if ($arr['last_access'] == '0000-00-00 00:00:00')
  $arr['last_access'] = '-';


if($arr["downloaded"] != 0){
$ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
} else {
$ratio="---";
}
$ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
  $uploaded = mksize($arr["uploaded"]);
  $downloaded = mksize($arr["downloaded"]);

$added = substr($arr['added'],0,10);
$last_access = substr($arr['last_access'],0,10);
$class=get_user_class_name($arr["class"]);

print("<tr><td align=left><a href=account-details.php?id=$arr[id]><b>$arr[username]</b></a>" .($arr["donated"] > 1 ? "<img src=/images/star.gif border=0 alt='Donor'>" : "")."</td>
  <td align=center>$added</td>
  <td align=center>$last_access</td>
  <td align=center>$class</td>
  <td align=center>$downloaded</td>
  <td align=center>$uploaded</td>
  <td align=center>$ratio</td>
  <td align=center>$arr[modcomment]</td></tr>\n");
}

print("</table>\n");
print("<p>$pagemenu<br>$browsemenu</p>");

end_frame();
}

#======================================================================#
# Database Administration
#======================================================================#
if ($act == "databaseadmin") {
    adminonly();
    adminmenu();

    $tablename = $_GET["tbname"] ?? '';
    $cmd = stripslashes($_POST['cmd']);
    if (!$tablename === '') {
        $cmd = "SELECT * FROM $tablename";
    }

	begin_frame("Database Administration");
	echo "<BR><center><B><a href=admin.php?act=database>Backup / Optimise Database</a></b></center><BR><BR>";
    echo "<i>WARNING: This area is for <u>advanced users only</u>! Do not send commands of which you do not know the meaning!
        <br><br>This is for performing manual database tasks.<br>Visit <a href=http://dev.mysql.com/doc/>http://dev.mysql.com/doc/</a>
        for syntax documentation.</i>";
    echo "<br><hr>";
    print("<form action='admin.php' method='post'>\n");
    print("<input type=hidden name='act' value='databaseadmin'>\n");
    print("<input type=hidden name='do' value='submitquery'>\n");
    print("<input type=hidden name='userid' value='$id'>\n");
    print("<table class=main border=1 align=center cellspacing=0 cellpadding=5>\n");
    print("<tr><td>MySQL Command:</td><td align=left><textarea cols=40 rows=6 name=cmd>$cmd</textarea></td></tr>\n");
    print("<tr><td colspan=2 align=center><input type=submit class=btn value='Run'></td></tr>\n");
    print("</table>\n");
    print("</form>\n");

    if ($do == "submitquery") {
        $validatecmd = mysql_query($cmd);
        echo "<br><br>";

        if ($validatecmd) {
            echo "<font color=green><b>Execution of MySQL Query successful!</b></font>";
        } else {
            echo "<font color=red><b>" . mysql_error() . "</b></font>";
        }
        echo "<br><br><a href=admin.php?act=databaseadmin>Show Database Tables</a>";
    }
    elseif ($do == "listfields") {
        echo "<br><br><a href=admin.php?act=databaseadmin>Show Database Tables</a><br><br>";
        echo "<table align=center cellpadding=0 cellspacing=0 style=border-collapse: collapse bordercolor=#D6D9DB width=100% border=1>";
        echo "<tr><td class=alt3 align=center><font size=1 face=Verdana color=white>Fields in table \"<b>".$tbname."</b>\"</td></tr></table>";
        echo "<table align=center cellpadding=0 cellspacing=0 style=border-collapse: collapse bordercolor=#D6D9DB width=100% border=1>";
        echo "<tr><td class=alt3 align=center><font size=1 face=Verdana color=white>Name</td><td class=alt3 align=center><font size=1
            face=Verdana color=white>Type</td><td class=alt3 align=center><font size=1 face=Verdana color=white>Flags</td></tr>";
        
        $reslistfields = mysql_query ("SELECT * FROM " . $tbname);
        $numfields = mysql_num_fields ($reslistfields);
        $rows   = mysql_num_rows ($reslistfields);
        $fieldcounter = 0;
        //$table = mysql_field_table ($reslistfields, $i);
        while ($fieldcounter < $numfields) {
            $type  = mysql_field_type  ($reslistfields, $fieldcounter);
            $name  = mysql_field_name  ($reslistfields, $fieldcounter);
            $flags = mysql_field_flags ($reslistfields, $fieldcounter);
            
            if(!$flags){
                $flags = "<i>none</i>";
            }
        echo "<tr><td>".$name."</td><td>".$type."</td><td>".$flags."</td></tr>";
        //echo $type." ".$name." ".$len." ".$flags."<BR>";
        $fieldcounter++;
        }
    
        echo "</table>";
    } else {
        // ----- TABLE DISPLAY
        echo "<table align=center cellpadding=0 cellspacing=0 style=border-collapse: collapse bordercolor=#D6D9DB width=100% border=1>";
        echo "<tr><td class=alt3 align=center><font size=1 face=Verdana color=white>Table Names in database \"<b>".$mysql_db."</b>\"</td></tr>";
        $res = DB::query('SHOW TABLES FROM ' . $mysql_db);
        while ($row = $res->fetch(\PDO::FETCH_NUM)) {
            echo '<tr><td><a href="admin.php?act=databaseadmin&do=listfields&tbname=' . $row[0] .'">' . $row[0] . '</a></td></tr>';
        }

        echo '</table>';
        // ---- END TABLE DISPLAY
    }
    
	end_frame();
}

#======================================================================#
# Database Management
#======================================================================#
if($act == "database")
{
//optimize
if($do == "opt")
{
mysql_query("OPTIMIZE TABLE `guests` , `peers` , `torrents` , `files` , `log` , `messages` , `forum_posts` ,`users` ; ");
bark2("Success", "Database optimized OK", Success);
}
//backup
if($do == "backup")
{
include("backup/backup.php");
}
modonly();
  adminmenu();
	begin_frame("Database Management");
	?>
	<b>Optimize Database: </b><a href='admin.php?act=database&do=opt'>CLICK HERE</a><br>
	<br>
    <b>Manual Backup Database: </b><a href='backup-database.php'>CLICK HERE</a><br>
	<i>(To set a automatic backup set a CRON task on backup-database.php)</i><BR>
	<br>
	<?php 
	end_frame();
	begin_frame("Backup History");
	?>
	<center><table width="500" cellpadding="1" cellspacing="0" border="2">
	<tr><b><td><b>DATE</b></TD><td><b>DAY</b></TD><td><b>FILENAME</b></TD></tr>
	<?php 
		//print the news titles, with links to the edit page 
		$getbackuphist = mysql_query("select * from dbbackup ORDER BY id DESC"); 
		while($backupr=mysql_fetch_array($getbackuphist)){ 
		extract($backupr); 
		  echo("<TR><TD>$added</TD><td>$day</td><td>$name</TD></TR>"); 
			} 
		echo("<br /><br /></TABLE></center>"); 
	end_frame();
}
#======================================================================#
#	Site Texts Edit
#======================================================================#
if($act == "sitetexts")
{
//disclaimer
if($do == "save_disclaimer")
{
	$fp = fopen("disclaimer.txt", "w");
	$css = stripslashes($css);
	$written = fwrite($fp,$css);
	fclose($fp);
	if($written) bark2("Success", "Disclaimer Updated OK", Success);
}

	adminmenu();
	begin_frame("Disclaimer Text Management", 'center');
	echo "<br><br>\n";
	echo "<form action='admin.php' method='post'>\n";
	echo "<input type='hidden' name='sid' value='$sid'>\n";
	echo "<input type='hidden' name='act' value='sitetexts'>\n";
	echo "<input type='hidden' name='do' value='save_disclaimer'>\n";
	echo "<textarea wrap='on' name='css' cols='100' rows='20' style='border:1px black solid;background:#eeeeee;font-family:verdana,arial; font-size: 12px; color:#000000;'>\n";
	include("disclaimer.txt");
	echo "</textarea>\n<p>\n";
	echo "<input style='background:#eeeeee' type='submit' value='   SAVE   '>\n";
	echo "<input style='background:#eeeeee' type='reset' value='  RESET   '>\n";
	echo "</form>\n";
	end_frame();
}
//news

#======================================================================#
#	Language Settings
#======================================================================#
if($act == "lang")
{
	//delete language
	if($do == "del_lang" && $lid != 1)
	{
		$dl = MYSQL_QUERY("DELETE FROM languages WHERE id = $lid");
		if($dl) autolink("admin.php?act=lang", "Language deleted ...");
		else die("<h2>mySQL-Error: Could not delete language-name (check connection & settings)\n</h2>\n</body>\n</html>");
	}

	//add language
if($do == "add_lang")
//add to db & create autolink
{
  $sql = "INSERT INTO languages (`uri`, `name`) VALUES ('$uri', '$name')";
  $ok = MYSQL_QUERY($sql);
  bark2("Success", "New Language Added", Success);
}

	//show lang's in <table>
	adminonly();
	adminmenu();
	begin_frame();
	?>
	<p>
	<font size='2'>Add a new language-name:</font>
	<form action='admin.php' method='post'>
	 <input type='hidden' name='act' value='lang'>
	<input type='hidden' name='do' value='add_lang'> 
	File Name: <input type='text' name='uri' size='30'>
	&nbsp;&nbsp;Language: <input type='text' name='name' size='30'>
	<input type='submit' value=' Add ' style='background:#eeeeee'>
	</form>
	<p>
	<?php 
	//get lang's from db
	$result = MYSQL_QUERY("SELECT * FROM languages ORDER BY id");
	//show them
	
	echo "<p>\n<font size='2'>Available language-names (sorted by id):</font>\n<p>\n";
	echo "<table width='200' border='1' cellspacing='0'>\n";
	echo "<tr bgcolor='#cecece'>\n<td align='center'><b>ID</b></td>\n<td align='center'><b>Name</b></td>\n<td align='center'><b>Delete?</b></td></tr>\n";
	while ($row = MYSQL_FETCH_ARRAY($result))
	{
		extract ($row);
		if($row[id] == 1) echo "<tr bgcolor='#ffffff' align='center'>\n<td>$row[id]</td>\n<td>$row[name]</td>\n<td align='center'>[ --- ]</td>\n</tr>\n";
		else echo "<tr bgcolor='#eeeeee' align='center'>\n<td>".$row[id]."</td>\n<td>".$row[name]."</td>\n<td align='center'>[ <a href='admin.php?act=lang&do=del_lang&lid=".$row[id]."' title='delete this entry'>del</a> ]</td>\n</tr>\n";
	}
	MYSQL_FREE_RESULT($result);
	echo "</table>\n";
	end_frame();
}
#======================================================================#
#	Banner Options
#======================================================================#
if($act == "banner" && $do == "")
{
	adminonly();
	adminmenu();
	begin_frame("Top Banner Ads", 'center');
	echo "Use the box below to edit the contents of&nbsp; banners.txt and sponsors.txt to control which banners are displayed on your site.<br />Each banner entry must be separated with a '~'. To increase the display rate of a banner enter its data multiple times.<br />To disable the banners, simply remove all data from both areas.\n";
	echo "<form action='admin.php' method='post'>\n";
	echo "<input type='hidden' name='sid' value='$sid'>\n";
	echo "<input type='hidden' name='act' value='baner'>\n";
	echo "<input type='hidden' name='do' value='save_banner'>\n";
	echo "<textarea name='css' cols='100' rows='20' style='border:1px black solid;background:#eeeeee;font-family:verdana,arial; font-size: 12px; color:#000000;'>\n";
	include("banners.txt");
	echo "</textarea>\n<p>\n";
	echo "<input style='background:#eeeeee' type='submit' value='   SAVE   '>\n";
	echo "<input style='background:#eeeeee' type='reset' value='  RESET   '>\n";
	echo "</form>\n";
	end_frame();

	begin_frame("Side Sponsor Adverts", 'center');
	echo "Use the box below to edit the contents of&nbsp; sponsors.txt\n";
	echo "<form action='admin.php' method='post'>\n";
	echo "<input type='hidden' name='sid' value='$sid'>\n";
	echo "<input type='hidden' name='act' value='baner'>\n";
	echo "<input type='hidden' name='do' value='save_sponsor'>\n";
	echo "<textarea name='cssa' cols='100' rows='10' style='border:1px black solid;background:#eeeeee;font-family:verdana,arial; font-size: 12px; color:#000000;'>\n";
	include("sponsors.txt");
	echo "</textarea>\n<p>\n";
	echo "<input style='background:#eeeeee' type='submit' value='   SAVE   '>\n";
	echo "<input style='background:#eeeeee' type='reset' value='  RESET   '>\n";
	echo "</form>\n";
	end_frame();
}

if($do == "save_banner")
{
	$fp = fopen("banners.txt", "w");
	$css = stripslashes($css);
	$written = fwrite($fp,$css);
	fclose($fp);
	if($written) bark2("Success", "Banners Updated OK", Success);

}
if($do == "save_sponsor")
{
	$fpa = fopen("sponsors.txt", "w");
	$cssa = stripslashes($cssa);
	$written = fwrite($fpa,$cssa);
	fclose($fpa);
	if($written) bark2("Success", "Sponsors Updated OK", Success);

}


#======================================================================#
#	Tracker (Settings) Settings
#======================================================================#
if($act == "settings")
	{
		adminonly();
		adminmenu();	// show menu
		//output
        begin_frame("Site Settings", 'center');
		// page submitted, update
		if ($do == 'save')
		{
		if ($CENSORWORDS_new == "ON1")
			$CENSORWORDS_temp = "true";
		else
		$CENSORWORDS_temp = "false";
        if ($WELCOMEPMON_new == "ON1")
			$WELCOMEPMON_temp = "true";
		else
			$WELCOMEPMON_temp = "false";
		if ($MEMBERSONLY_new == "ON1")
			$MEMBERSONLY_temp = "true";
		else
			$MEMBERSONLY_temp = "false";

		if ($MEMBERSONLY_WAIT_new == "ON1")
			$MEMBERSONLY_WAIT_temp = "true";
		else
			$MEMBERSONLY_WAIT_temp = "false";
            
		if ($RATIO_WARNINGON_new == "ON1")
			$RATIO_WARNINGON_temp = "true";
		else
			$RATIO_WARNINGON_temp = "false";

		if ($LOGGEDINONLY_new == "ON1")
			$LOGGEDINONLY_temp = "true";
		else
			$LOGGEDINONLY_temp = "false";

		if ($SITENOTICEON_new == "ON1")
			$SITENOTICEON_temp = "true";
		else
			$SITENOTICEON_temp = "false";

		if ($REMOVALSON_new == "ON1")
			$REMOVALSON_temp = "true";
		else
			$REMOVALSON_temp = "false";

		if ($NEWSON_new == "ON1")
			$NEWSON_temp = "true";
		else
			$NEWSON_temp = "false";

		if ($UPLOADERSONLY_new == "ON1")
			$UPLOADERSONLY_temp = "true";
		else
			$UPLOADERSONLY_temp = "false";

		if ($INVITEONLY_new == "ON1")
			$INVITEONLY_temp = "true";
		else
			$INVITEONLY_temp = "false";
		
		if ($ACONFIRM_new == "ON1")
			$ACONFIRM_temp = "true";
		else
			$ACONFIRM_temp = "false";

		if ($DONATEON_new == "ON1")
			$DONATEON_temp = "true";
		else
			$DONATEON_temp = "false";

		if ($DISCLAIMERON_new == "ON1")
			$DISCLAIMERON_temp = "true";
		else
			$DISCLAIMERON_temp = "false";

		if ($SHOUTBOX_new == "ON1")
			$SHOUTBOX_temp = "true";
		else
			$SHOUTBOX_temp = "false";

		if ($FORUMS_new == "ON1")
			$FORUMS_temp = "true";
		else
			$FORUMS_temp = "false";

		if ($DHT_new == "ON1")
			$DHT_temp = "true";
		else
			$DHT_temp = "false";
            
        if ($POLLON_new == "ON1")
			$POLLON_temp = "true";
		else
			$POLLON_temp = "false";

        if ($REQUESTSON_new == "ON1")
			$REQUESTSON_temp = "true";
		else
			$REQUESTSON_temp = "false";

		if ($IRCCHAT_new == "ON1")
			$IRCCHAT_temp = "true";
		else
			$IRCCHAT_temp = "false";

		if ($IRCANNOUNCE_new == "ON1")
			$IRCANNOUNCE_temp = "true";
		else
			$IRCANNOUNCE_temp = "false";

		if ($GLOBALBAN)
			$GLOBALBAN_temp = "true";
		else
			$GLOBALBAN_temp = "false";
                if (ini_get("magic_quotes_gpc")) $SITENOTICE_new = stripslashes($SITENOTICE_new);

			$config_settings_data = <<<EOD
<?php 

// MySQL Settings (please change these to reflect your MYSQL settings, all other settings can be changed via adminCP)
\$mysql_host = "$mysql_host_new";
\$mysql_user = "$mysql_user_new";
\$mysql_pass = "$mysql_pass_new";
\$mysql_db = "$mysql_db_new";

// Default Language / Theme Settings (These are currently set via the database, NOT THE ADMIN CP)
\$language = "$language";
\$theme = "$theme";

// Site Settings
\$SITENAME = "$SITENAME_new";
\$SITEEMAIL = "$SITEEMAIL_new";
\$SITEURL = "$SITEURL_new";
\$SITE_ONLINE = $SITE_ONLINE_new;
\$OFFLINEMSG = "$OFFLINEMSG_new";
\$UPLOADERSONLY = $UPLOADERSONLY_temp;
\$LOGGEDINONLY = $LOGGEDINONLY_temp;
\$INVITEONLY = $INVITEONLY_temp;
\$ACONFIRM = $ACONFIRM_temp;
\$WELCOMEPMON = $WELCOMEPMON_temp;
\$CENSORWORDS = $CENSORWORDS_temp;
\$MAXDISPLAYLENGTH = "$MAXDISPLAYLENGTH_new";
\$WELCOMEPMMSG = "$WELCOMEPMMSG_new";
\$DHT = $DHT_temp;
\$POLLON = $POLLON_temp;

//Setup Site Blocks
\$SITENOTICEON = $SITENOTICEON_temp;
\$REMOVALSON = $REMOVALSON_temp;
\$NEWSON = $NEWSON_temp;
\$DONATEON = $DONATEON_temp;
\$DISCLAIMERON = $DISCLAIMERON_temp;
\$SITENOTICE = <<<EOD\r\n$SITENOTICE_new\r\nEOD;
\$SHOUTBOX = $SHOUTBOX_temp;
\$FORUMS = $FORUMS_temp;
\$REQUESTSON = $REQUESTSON_temp;

//setup IRC Chat
\$IRCCHAT = $IRCCHAT_temp;
\$IRCCHANNEL = "$IRCCHANNEL_new";
\$IRCSERVER1 = "$IRCSERVER1_new";
\$IRCSERVER2 = "$IRCSERVER2_new";
\$IRCSERVER3 = "$IRCSERVER3_new";

//Setup IRC Announcer
\$IRCANNOUNCE = $IRCANNOUNCE_temp;
\$ANNOUNCEIP = "$ANNOUNCEIP_new";
\$ANNOUNCEPORT= "$ANNOUNCEPORT_new";

//WAIT TIME VARS
\$GIGSA= "$GIGSA_new";
\$RATIOA= "$RATIOA_new";
\$WAITA= "$WAITA_new";
\$GIGSB= "$GIGSB_new";
\$RATIOB= "$RATIOB_new";
\$WAITB= "$WAITB_new";
\$GIGSC= "$GIGSC_new";
\$RATIOC= "$RATIOC_new";
\$WAITC= "$WAITC_new";
\$GIGSD= "$GIGSD_new";
\$RATIOD= "$RATIOD_new";
\$WAITD= "$WAITD_new";

//RATIO WARNING VARS
\$RATIO_WARNINGON = "$RATIO_WARNINGON_temp";    //ratiowarn on/off
\$RATIOWARN_AMMOUNT = "$RATIOWARN_AMMOUNT_new"; //user warned if this ratio is held
\$RATIOWARN_TIME = "$RATIOWARN_TIME_new";   //ammount of time for user have have poor ratio before warning
\$RATIOWARN_BAN = "$RATIOWARN_BAN_new";     //ammount of time after warning to auto-ban user.

// Tracker Settings
\$torrent_dir = "$torrent_dir_new";
\$nfo_dir = "$nfo_dir_new";
\$image_dir = "$image_dir_new";
\$announce_urls = array();
\$announce_urls[] = "$announce_urls_new";
\$GLOBALBAN = $GLOBALBAN_temp;
\$MEMBERSONLY = $MEMBERSONLY_temp;
\$MEMBERSONLY_WAIT = $MEMBERSONLY_WAIT_temp;
\$RATIO_WARNINGON = $RATIO_WARNINGON_temp;
\$PEERLIMIT = "$PEERLIMIT_new";

// Advanced Settings for announce and cleanup
\$autoclean_interval = "$autoclean_interval_new";
\$max_torrent_size = "$max_torrent_size_new";
\$max_nfo_size = "$max_nfo_size_new";
\$max_image_size = "$max_image_size_new";
\$announce_interval = "$announce_interval_new";
\$signup_timeout = "$signup_timeout_new";
\$minvotes = "$minvotes_new";
\$maxsiteusers = "$maxsiteusers_new";
\$max_dead_torrent_time = "$max_dead_torrent_time_new";


?>
EOD;

			// create backup of config.php file first
			$old_config_file_read_handle = fopen("backend/config.php", "r");
			$old_config_file_write_handle = fopen("backend/oldconfig.php", "w");

			$old_config_file_contents = fread($old_config_file_read_handle, filesize("backend/config.php"));
			fwrite ($old_config_file_write_handle, $old_config_file_contents);

			fclose ($old_config_file_read_handle);
			fclose ($old_config_file_write_handle);

			// write onto current config file
			$new_config_file_handle = fopen("backend/config.php", "w");
			fwrite ($new_config_file_handle, $config_settings_data);
			fclose ($new_config_file_handle);
            //begin_frame("","center");
            print("<table border=0 cellspacing=0 cellpadding=5><td><center>");
    	    autolink("admin.php?act=settings", "Your Settings Were Updated");
            print("</center></td></tr></table>");
            //end_frame();

		}

		?>

		<form action='admin.php?act=settings&do=save' method='post'>
		<input type='hidden' name='sid' value='<?=$sid?>'>
		<input type='hidden' name='act' value='settings'>
		<input type='hidden' name='do'  value='save'>
		<div align="center">
		<table width='100%' cellspacing='3' cellpadding='3'>
		<tr>
		<td colspan="2"><b><font face="Verdana" size="1">
		Database Settings<br /></font><font size="1" face="Times New Roman">&#9492;
		</font></b><font size="1" face="Verdana">Only modify these settings if
		you have changed the location of your database.</font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">MYSQL Host:</font></td>
		<td align='left'><font size="1" face="Verdana"><input type='text' name='mysql_host_new' value='<?=$mysql_host?>' maxlength='50' size='50'></font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">MYSQL User:</font></td>
		<td align='left'>
		<font size="1" face="Verdana">
		<input type='text' name='mysql_user_new' value='<?=$mysql_user?>' maxlength='50' size='50'></font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">MYSQL Pass:</font></td>
		<td align='left'>
		<font size="1" face="Verdana">
		<input type='text' name='mysql_pass_new' value='<?=$mysql_pass?>' maxlength='50' size='50'></font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">MYSQL Database</font></td>
		<td align='left'><font size="1" face="Verdana">
		<input type='text' name='mysql_db_new' value='<?=$mysql_db?>' maxlength='50' size='50'></font></td>
		</tr>
		<tr>
		<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
		<td colspan="2"><b><font face="Verdana" size="1">
		File Storage Paths<br /></font><font size="1" face="Times New Roman">&#9492;
		</font></b><font size="1" face="Verdana">Must be CHMOD 777 and absolute paths. See <a href="phpinfo.php" target="_blank">[php info]</a> for more info.</font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">Path to directory
		where .torrents will be stored:</font></td>
		<td align='left'><font size="1" face="Verdana">
		<input type='text' name='torrent_dir_new' value='<?=$torrent_dir?>' size='50'></font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">Path to directory
		where NFO files will be stored:</font></td>
		<td align='left'><font size="1" face="Verdana">
		<input type='text' name='nfo_dir_new' value='<?=$nfo_dir?>' size='50'></font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">Path to directory
		where image files will be stored:</font></td>
		<td align='left'><font size="1" face="Verdana">
		<input type='text' name='image_dir_new' value='<?=$image_dir?>' size='50'></font></td>
		</tr>
		<tr>
		<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
		<td colspan="2"><b><font face="Verdana" size="1">
		Tracker Configuration<br /></font><font size="1" face="Times New Roman">&#9492;
		</font></b><font size="1" face="Verdana">Here you can configure your
		trackers main settings</font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">Site Name:</font></td>
		<td align='left'>
		<font size="1" face="Verdana">
		<input type='text' name='SITENAME_new' value='<?=$SITENAME?>' size='50'></font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">Tracker URL:</font></td>
		<td align='left'>
		<font size="1" face="Verdana">
		<input type='text' name='SITEURL_new' value='<?=$SITEURL?>' size='50'></font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">Announce Url:</font></td>
		<td align='left'><font size="1" face="Verdana">
		<input type='text' name='announce_urls_new' value='<?=$announce_urls[0]?>' size='50'></font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">Maximum Users Accounts:</font></td>
		<td align='left'><font size="1" face="Verdana">
		<input type='text' name='maxsiteusers_new' value='<?=$maxsiteusers?>' size='50'></font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">Maximum Peers:</font></td>
		<td align='left'><font size="1" face="Verdana">
		<input type='text' name='PEERLIMIT_new' value='<?=$PEERLIMIT?>' size='50'></font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">Email: (Signup emails etc will be sent from this address)</font></td>
		<td align='left'>
		<font size="1" face="Verdana">
		<input type='text' name='SITEEMAIL_new' value='<?=$SITEEMAIL?>' size='50'></font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">Tracker Status:</font></td>
		<td align='left'>
		<font size="1" face="Verdana">
		<select name='SITE_ONLINE_new'>
		<option value='true'  <?php  if($SITE_ONLINE == true)  echo "selected"; ?>>ONLINE
		<option value='false' <?php  if($SITE_ONLINE == false) echo "selected"; ?>>OFFLINE
		</select></font></td>
		</tr>
		<tr>
		<td valign="top"><font face="Verdana" size="1">Site Offline Message:<br><i> (HTML Allowed)</i></font></td>
		<td align='left'>
		<font size="1" face="Verdana">
		<textarea name='OFFLINEMSG_new' cols="38" rows="8"><?php echo $OFFLINEMSG; ?></textarea></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">Require Members To Register:</font></td>
		<td align='left'>
		<font face="Verdana"><b>
		<font size="1">YES</font></b><font size="1">
		<input style='border:0;background:#eeeeee' type='radio' name='MEMBERSONLY_new' value='ON1' <?php if($MEMBERSONLY == true)  echo "checked"; checked ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				   <b>NO</b>
		<input style='border:0;background:#eeeeee' type='radio' name='MEMBERSONLY_new' value='OFF1'<?php if($MEMBERSONLY == false) echo "checked"; ?>>
		</font></font> <font size="1" face="Verdana">&nbsp;
		</font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">Invite ONLY:<br><i> (Make it only possible to register via a invite, members only also needs to be ON)</i></font></td>
		<td align='left'>
		<font face="Verdana"><b>
		<font size="1">YES</font></b><font size="1">
		<input style='border:0;background:#eeeeee' type='radio' name='INVITEONLY_new' value='ON1' <?php if($INVITEONLY == true)  echo "checked"; checked ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				   <b>NO</b>
		<input style='border:0;background:#eeeeee' type='radio' name='INVITEONLY_new' value='OFF1'<?php if($INVITEONLY == false) echo "checked"; ?>>
		</font></font> <font size="1" face="Verdana">&nbsp;
		</font></td>
		</tr>
		 <tr>
 <td><font face="Verdana" size="1">Admin ONLY Confirm Registration:<br><i> (Make it only possible for an admin to confirm each new account)</i></font></td>
 <td align='left'>
 <font face="Verdana"><b>
 <font size="1">YES</font></b><font size="1">
 <input style='border:0;background:#eeeeee' type='radio' name='ACONFIRM_new' value='ON1' <?php if($ACONFIRM == true)  echo "checked"; checked ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <b>NO</b>
 <input style='border:0;background:#eeeeee' type='radio' name='ACONFIRM_new' value='OFF1'<?php if($ACONFIRM == false) echo "checked"; ?>>
 </font></font> <font size="1" face="Verdana">&nbsp;
 </font></td>
 </tr>
        <tr>
		<td><font face="Verdana" size="1">Send Welcome PM to New Users?</font></td>
		<td align='left'>
		<font face="Verdana"><b>
		<font size="1">YES</font></b><font size="1">
		<input style='border:0;background:#eeeeee' type='radio' name='WELCOMEPMON_new' value='ON1' <?php if($WELCOMEPMON == true)  echo "checked"; checked ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				   <b>NO</b>
		<input style='border:0;background:#eeeeee' type='radio' name='WELCOMEPMON_new' value='OFF1'<?php if($WELCOMEPMON == false) echo "checked"; ?>>
		</font></font> <font size="1" face="Verdana">&nbsp;
		</font></td>
		</tr>
        <tr>
		<td valign="top"><font face="Verdana" size="1">Welcome PM to New Users:</font></td>
		<td align='left'>
		<font size="1" face="Verdana">
		<textarea name='WELCOMEPMMSG_new' cols="38" rows="8"><?php echo $WELCOMEPMMSG; ?></textarea></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">Only Logged In Memebers Can View/Download Torrents:<br></font></td>
		<td align='left'>
		<font face="Verdana"><b>
		<font size="1">YES</font></b><font size="1">
		<input style='border:0;background:#eeeeee' type='radio' name='LOGGEDINONLY_new' value='ON1' <?php if($LOGGEDINONLY == true)  echo "checked"; checked ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				   <b>NO</b>
		<input style='border:0;background:#eeeeee' type='radio' name='LOGGEDINONLY_new' value='OFF1'<?php if($LOGGEDINONLY == false) echo "checked"; ?>>
		</font></font> <font size="1" face="Verdana">&nbsp;
		</font></td>
		</tr>
		<tr>
		 <td><font face="Verdana" size="1">Word Censor Enabled?<br.</font></td>
		 <td align='left'>
		 <font face="Verdana"><b>
		 <font size="1">YES</font></b><font size="1">
		 <input style='border:0;background:#eeeeee' type='radio' name='CENSORWORDS_new' value='ON1' <?php if($CENSORWORDS == true)  echo "checked"; checked ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  <b>NO</b>
		 <input style='border:0;background:#eeeeee' type='radio' name='CENSORWORDS_new' value='OFF1'<?php if($CENSORWORDS == false) echo "checked"; ?>>
		 </font></font> <font size="1" face="Verdana">&nbsp;
		 </font></td>
		 </tr>
		<tr>
		<td><font face="Verdana" size="1">Wait Times Enabled?<br><i>(See Below For Full Details)</i></font></td>
		<td align='left'>
		<font face="Verdana"><b>
		<font size="1">YES</font></b><font size="1">
		<input style='border:0;background:#eeeeee' type='radio' name='MEMBERSONLY_WAIT_new' value='ON1' <?php if($MEMBERSONLY_WAIT == true)  echo "checked"; checked ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				   <b>NO</b>
		<input style='border:0;background:#eeeeee' type='radio' name='MEMBERSONLY_WAIT_new' value='OFF1'<?php if($MEMBERSONLY_WAIT == false) echo "checked"; ?>>
		</font></font> <font size="1" face="Verdana">&nbsp;
		</font></td>
		</tr>

		<tr>
		<td><font face="Verdana" size="1" align="right">Wait Times<br></font></td>
		<td align='left'>
		<b>A) </b>RATIO: <<input type='text' name='RATIOA_new' value='<?=$RATIOA?>' maxlength='4' size='4'>GIGS: <<input type='text' name='GIGSA_new' value='<?=$GIGSA?>' maxlength='4' size='4'> WAIT: <input type='text' name='WAITA_new' value='<?=$WAITA?>' maxlength='2' size='3'>Hrs
		<BR><BR>
		<b>B) </b>RATIO: <<input type='text' name='RATIOB_new' value='<?=$RATIOB?>' maxlength='4' size='4'>GIGS: <<input type='text' name='GIGSB_new' value='<?=$GIGSB?>' maxlength='4' size='4'> WAIT: <input type='text' name='WAITB_new' value='<?=$WAITB?>' maxlength='2' size='3'>Hrs
		<BR><BR>
		<b>C) </b>RATIO: <<input type='text' name='RATIOC_new' value='<?=$RATIOC?>' maxlength='4' size='4'>GIGS: <<input type='text' name='GIGSC_new' value='<?=$GIGSC?>' maxlength='4' size='4'> WAIT: <input type='text' name='WAITC_new' value='<?=$WAITC?>' maxlength='2' size='3'>Hrs
		<BR><BR>
		<b>D) </b>RATIO: <<input type='text' name='RATIOD_new' value='<?=$RATIOD?>' maxlength='4' size='4'>GIGS: <<input type='text' name='GIGSD_new' value='<?=$GIGSD?>' maxlength='4' size='4'> WAIT: <input type='text' name='WAITD_new' value='<?=$WAITD?>' maxlength='2' size='3'>Hrs
		<BR><BR>
		</td>
		</tr>
        
        <tr>
		<td><font face="Verdana" size="1">Ratio Warning Enabled?<br><i>(See Below For Full Details)</i></font></td>
		<td align='left'>
		<font face="Verdana"><b>
		<font size="1">YES</font></b><font size="1">
		<input style='border:0;background:#eeeeee' type='radio' name='RATIO_WARNINGON_new' value='ON1' <?php if($RATIO_WARNINGON == true)  echo "checked"; checked ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				   <b>NO</b>
		<input style='border:0;background:#eeeeee' type='radio' name='RATIO_WARNINGON_new' value='OFF1'<?php if($RATIO_WARNINGON == false) echo "checked"; ?>>
		</font></font> <font size="1" face="Verdana">&nbsp;
		</font></td>
		</tr>
        
        <tr>
		<td><font face="Verdana" size="1" align="right">Warning Schedule<br></font></td>
		<td align='left'>
		Warn after RATIO is less than <input type='text' name='RATIOWARN_AMMOUNT_new' value='<?=$RATIOWARN_AMMOUNT?>' maxlength='4' size='4'> for <input type='text' name='RATIOWARN_TIME_new' value='<?=$RATIOWARN_TIME?>' maxlength='4' size='4'> days.
		<BR><BR>
        Ban after <input type='text' name='RATIOWARN_BAN_new' value='<?=$RATIOWARN_BAN?>' maxlength='4' size='4'> day(s) after warning ignored by user.
		<BR><BR>
		</td>
		</tr>

		<tr>
		<td><font face="Verdana" size="1">Auto Add DHT Flag?</font><br><i>(Automatic addition of the Private flag to all uploaded torrents, please not the seeder will need to re-download from the site.)</i></td>
		<td align='left'>
		<font face="Verdana"><b>
		<font size="1">YES</font></b><font size="1">
		<input style='border:0;background:#eeeeee' type='radio' name='DHT_new' value='ON1' <?php if($DHT == true)  echo "checked"; checked ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				   <b>NO</b>
		<input style='border:0;background:#eeeeee' type='radio' name='DHT_new' value='OFF1'<?php if($DHT == false) echo "checked"; ?>>
		</font></font> <font size="1" face="Verdana">&nbsp;
		</font></td>
		</tr>
        
        <tr>
		<td><font face="Verdana" size="1">Turn Site Poll On?</font><br><i>Create a poll at <a href=makepoll.php>makepoll.php</a></i></td>
		<td align='left'>
		<font face="Verdana"><b>
		<font size="1">YES</font></b><font size="1">
		<input style='border:0;background:#eeeeee' type='radio' name='POLLON_new' value='ON1' <?php if($POLLON == true)  echo "checked"; checked ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				   <b>NO</b>
		<input style='border:0;background:#eeeeee' type='radio' name='POLLON_new' value='OFF1'<?php if($POLLON == false) echo "checked"; ?>>
		</font></font> <font size="1" face="Verdana">&nbsp;
		</font></td>
		</tr>

		<tr>
		<td><font face="Verdana" size="1">Forums Enabled?</font></td>
		<td align='left'>
		<font face="Verdana"><b>
		<font size="1">YES</font></b><font size="1">
		<input style='border:0;background:#eeeeee' type='radio' name='FORUMS_new' value='ON1' <?php if($FORUMS == true)  echo "checked"; checked ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				   <b>NO</b>
		<input style='border:0;background:#eeeeee' type='radio' name='FORUMS_new' value='OFF1'<?php if($FORUMS == false) echo "checked"; ?>>
		</font></font> <font size="1" face="Verdana">&nbsp;
		</font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">Restrict Uploads To Uploader Class?</font></td>
		<td align='left'>
		<font face="Verdana"><b>
		<font size="1">YES</font></b><font size="1">
		<input style='border:0;background:#eeeeee' type='radio' name='UPLOADERSONLY_new' value='ON1' <?php if($UPLOADERSONLY == true)  echo "checked"; checked ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				   <b>NO</b>
		<input style='border:0;background:#eeeeee' type='radio' name='UPLOADERSONLY_new' value='OFF1'<?php if($UPLOADERSONLY == false) echo "checked"; ?>>
		</font></font> <font size="1" face="Verdana">&nbsp;
		</font></td>
		</tr>
		

		<tr>
		<td><font face="Verdana" size="1">IRC Web Chat Enabled?</font></td>
		<td align='left'>
		<font face="Verdana"><b>
		<font size="1">YES</font></b><font size="1">
		<input style='border:0;background:#eeeeee' type='radio' name='IRCCHAT_new' value='ON1' <?php if($IRCCHAT == true)  echo "checked"; checked ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				   <b>NO</b>
		<input style='border:0;background:#eeeeee' type='radio' name='IRCCHAT_new' value='OFF1'<?php if($IRCCHAT == false) echo "checked"; ?>>
		</font></font> <font size="1" face="Verdana">&nbsp;
		</font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">IRC Channel To Join:</font></td>
		<td align='left'>
		<font size="1" face="Verdana">
		<input type='text' name='IRCCHANNEL_new' value='<?=$IRCCHANNEL?>' maxlength='400' size='50'></font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">IRC Main Server:</font></td>
		<td align='left'>
		<font size="1" face="Verdana">
		<input type='text' name='IRCSERVER1_new' value='<?=$IRCSERVER1?>' maxlength='400' size='50'></font></td>
		</tr>
		<tr>
		<tr>
		<td><font face="Verdana" size="1">IRC Alt Server 2:</font></td>
		<td align='left'>
		<font size="1" face="Verdana">
		<input type='text' name='IRCSERVER2_new' value='<?=$IRCSERVER2?>' maxlength='400' size='50'></font></td>
		</tr>
		<tr>
		<tr>
		<td><font face="Verdana" size="1">IRC Alt Server 3:</font></td>
		<td align='left'>
		<font size="1" face="Verdana">
		<input type='text' name='IRCSERVER3_new' value='<?=$IRCSERVER3?>' maxlength='400' size='50'></font></td>
		</tr>
		<tr>
		<tr>
		<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
		<td colspan="2"><b><font face="Verdana" size="1">
		Blocks Management<br /></font><font size="1" face="Times New Roman">&#9492;
		</font></b><font size="1" face="Verdana">Here you can configure the blocks on the site</font></td>
		</tr>

		<tr>
		<td><font face="Verdana" size="1">Torrent Name Max Length Before Cut-Off: </font><br><i>(if name is higher it will be shortend with ... added)</i><font color=red>REQUIRED</font> </td>
		<td align='left'><font size="1" face="Verdana">
		<input type='text' name='MAXDISPLAYLENGTH_new' value='<?=$MAXDISPLAYLENGTH?>' maxlength='3' size='5'></font></td>
		</tr>

		<tr>
		<td><font face="Verdana" size="1">Welcome / Notice Block Enabled?</font></td>
		<td align='left'>
		<font face="Verdana"><b>
		<font size="1">YES</font></b><font size="1">
		<input style='border:0;background:#eeeeee' type='radio' name='SITENOTICEON_new' value='ON1' <?php if($SITENOTICEON == true)  echo "checked"; checked ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				   <b>NO</b>
		<input style='border:0;background:#eeeeee' type='radio' name='SITENOTICEON_new' value='OFF1'<?php if($SITENOTICEON == false) echo "checked"; ?>>
		</font></font> <font size="1" face="Verdana">&nbsp;
		</font></td>
		</tr>
		<tr>
		<td valign="top"><font face="Verdana" size="1">Welcome / Notic Text:<br><i>(html allowed)</i> </font></td>
		<td align='left'>
		<font size="1" face="Verdana">
		<textarea name='SITENOTICE_new' cols="38" rows="8"><?php echo $SITENOTICE; ?></textarea></font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">Shoutbox Enabled?</font></td>
		<td align='left'>
		<font face="Verdana"><b>
		<font size="1">YES</font></b><font size="1">
		<input style='border:0;background:#eeeeee' type='radio' name='SHOUTBOX_new' value='ON1' <?php if($SHOUTBOX == true)  echo "checked"; checked ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				   <b>NO</b>
		<input style='border:0;background:#eeeeee' type='radio' name='SHOUTBOX_new' value='OFF1'<?php if($SHOUTBOX == false) echo "checked"; ?>>
		</font></font> <font size="1" face="Verdana">&nbsp;
		</font></td>
		</tr>

		<tr>
		<td><font face="Verdana" size="1">Removals / Copyrights Block Enabled?</font></td>
		<td align='left'>
		<font face="Verdana"><b>
		<font size="1">YES</font></b><font size="1">
		<input style='border:0;background:#eeeeee' type='radio' name='REMOVALSON_new' value='ON1' <?php if($REMOVALSON == true)  echo "checked"; checked ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				   <b>NO</b>
		<input style='border:0;background:#eeeeee' type='radio' name='REMOVALSON_new' value='OFF1'<?php if($REMOVALSON == false) echo "checked"; ?>>
		</font></font> <font size="1" face="Verdana">&nbsp;
		</font></td>
		</tr>

		<tr>
		<td><font face="Verdana" size="1">Site News Block Enabled?</font></td>
		<td align='left'>
		<font face="Verdana"><b>
		<font size="1">YES</font></b><font size="1">
		<input style='border:0;background:#eeeeee' type='radio' name='NEWSON_new' value='ON1' <?php if($NEWSON == true)  echo "checked"; checked ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				   <b>NO</b>
		<input style='border:0;background:#eeeeee' type='radio' name='NEWSON_new' value='OFF1'<?php if($NEWSON == false) echo "checked"; ?>>
		</font></font> <font size="1" face="Verdana">&nbsp;
		</font></td>
		</tr>

		<tr>
		<td><font face="Verdana" size="1">Donate Block Enabled?</font></td>
		<td align='left'>
		<font face="Verdana"><b>
		<font size="1">YES</font></b><font size="1">
		<input style='border:0;background:#eeeeee' type='radio' name='DONATEON_new' value='ON1' <?php if($DONATEON == true)  echo "checked"; checked ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				   <b>NO</b>
		<input style='border:0;background:#eeeeee' type='radio' name='DONATEON_new' value='OFF1'<?php if($DONATEON == false) echo "checked"; ?>>
		</font></font> <font size="1" face="Verdana">&nbsp;
		</font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">Disclaimer Block Enabled?</font></td>
		<td align='left'>
		<font face="Verdana"><b>
		<font size="1">YES</font></b><font size="1">
		<input style='border:0;background:#eeeeee' type='radio' name='DISCLAIMERON_new' value='ON1' <?php if($DISCLAIMERON == true)  echo "checked"; checked ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				   <b>NO</b>
		<input style='border:0;background:#eeeeee' type='radio' name='DISCLAIMERON_new' value='OFF1'<?php if($DISCLAIMERON == false) echo "checked"; ?>>
		</font></font> <font size="1" face="Verdana">&nbsp;
		</font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">Request Area On?</font></td>
		<td align='left'>
		<font face="Verdana"><b>
		<font size="1">YES</font></b><font size="1">
		<input style='border:0;background:#eeeeee' type='radio' name='REQUESTSON_new' value='ON1' <?php if($REQUESTSON == true)  echo "checked"; checked ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				   <b>NO</b>
		<input style='border:0;background:#eeeeee' type='radio' name='REQUESTSON_new' value='OFF1'<?php if($REQUESTSON == false) echo "checked"; ?>>
		</font></font> <font size="1" face="Verdana">&nbsp;
		</font></td>
		</tr>

		<tr>
		<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
		<td colspan="2"><b><font face="Verdana" size="1">
		IRC Announcer Settings<br /></font><font size="1" face="Times New Roman">&#9492;
		</font></b><font face="Verdana" size="1">This is where you can adjust the settings for the IRC announcer.  This announces all torrents uploaded and new forum topics.  Click <a href=admin.php?act=ircannounce>HERE</a> instructions on how to setup.</font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">IRC Torrent/Forum Announcer Enabled?</font></td>
		<td align='left'>
		<font face="Verdana"><b>
		<font size="1">YES</font></b><font size="1">
		<input style='border:0;background:#eeeeee' type='radio' name='IRCANNOUNCE_new' value='ON1' <?php if($IRCANNOUNCE == true)  echo "checked"; checked ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				   <b>NO</b>
		<input style='border:0;background:#eeeeee' type='radio' name='IRCANNOUNCE_new' value='OFF1'<?php if($IRCANNOUNCE == false) echo "checked"; ?>>
		</font></font> <font size="1" face="Verdana">&nbsp;
		</font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">IP Address Of Target PC To Announce On: </font></td>
		<td align='left'>
		<font size="1" face="Verdana">
		<input type='text' name='ANNOUNCEIP_new' value='<?=$ANNOUNCEIP?>' maxlength='400' size='50'></font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">PORT Of Targer PC To Announce On: </font></td>
		<td align='left'>
		<font size="1" face="Verdana">
		<input type='text' name='ANNOUNCEPORT_new' value='<?=$ANNOUNCEPORT?>' maxlength='400' size='50'></font></td>
		</tr>
		<tr>
		<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
		<td colspan="2"><b><font face="Verdana" size="1">
		Advanced Settings<br /></font><font size="1" face="Times New Roman">&#9492;
		</font></b><font face="Verdana" size="1">These settings are for advanced
		users only. If you do not understand what they do, do
		<font color="#CC0000">NOT </font>modify them.</font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">Maximum Torrent Size
		(in bytes)</font></td>
		<td align='left'><font size="1" face="Verdana">
		<input type='text' name='max_torrent_size_new' value='<?=$max_torrent_size?>' maxlength='50' size='50'></font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">Maximum NFO Size (in
		bytes)</font></td>
		<td align='left'><font size="1" face="Verdana">
		<input type='text' name='max_nfo_size_new' value='<?=$max_nfo_size?>' maxlength='50' size='50'></font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">Maximum Image (in
		bytes)</font></td>
		<td align='left'><font size="1" face="Verdana">
		<input type='text' name='max_image_size_new' value='<?=$max_image_size?>' maxlength='50' size='50'></font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">Torrent Auto-Clean
		Interval (in seconds)</font></td>
		<td align='left'><font size="1" face="Verdana">
		<input type='text' name='autoclean_interval_new' value='<?=$autoclean_interval?>' maxlength='50' size='50'></font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">Torrent Announce
		Interval (in seconds)</font></td>
		<td align='left'><font size="1" face="Verdana">
		<input type='text' name='announce_interval_new' value='<?=$announce_interval?>' maxlength='50' size='50'></font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">Torrent Minimum
		Votes</font></td>
		<td align='left'><font size="1" face="Verdana">
		<input type='text' name='minvotes_new' value='<?=$minvotes?>' maxlength='50' size='50'></font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">Inactivity Timeout (in seconds)</font></td>
		<td align='left'><font size="1" face="Verdana">
		<input type='text' name='signup_timeout_new' value='<?=$signup_timeout?>' maxlength='50' size='50'></font></td>
		</tr>
		<tr>
		<td><font face="Verdana" size="1">Maximum Torrent Dead
		Time (in seconds)</font></td>
		<td align='left'><font size="1" face="Verdana">
		<input type='text' name='max_dead_torrent_time_new' value='<?=$max_dead_torrent_time?>' maxlength='50' size='50'></font></td>
		</tr>
		<tr>
		<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
		<td align='left'></td><td align='left'>
			<font size="1" face="Verdana">
			<input type='submit' value='Update Settings'>&nbsp;
			<input type='reset' value='Reset'></font></td>
		</tr>

		<?php // print errors if appeared
		if($error_fp != "")
		{
		echo "<tr>\n";
		echo "<td colspan='2' align='center' style='border:3px red solid; background:#eeeeee'><b>COULD NOT SAVE SETTINGS:</b><br />$error_fp</td>\n";
		echo "</tr>\n";
		}
		?>

		</table></div>


		<?php 
		end_frame();
		print("</form>");
	}

#======================================================================#
#	Tracker Log
#======================================================================#
if($act == "view_log")
	{
		adminmenu();	// show menu
		//output

	//delete language
	if($do == "del_log" && $arr[id] != 1)
	{
		$dl = MYSQL_QUERY("DELETE FROM log WHERE id = $arr[id]");
		if($dl) autolink("admin.php?act=view_log", "Entry deleted ...");
		else die("<h2>mySQL-Error: Could not delete language-name (check connection & settings)\n</h2>\n</body>\n</html>");
	}

// delete items older than a week - Should be a variable in db and should be changeable in AdminCP
$secs = 24 * 60 * 60;
mysql_query("DELETE FROM log WHERE " . gmtime() . " - UNIX_TIMESTAMP(added) > $secs") or sqlerr(__FILE__, __LINE__);
$res = mysql_query("SELECT added, txt, id FROM log ORDER BY added DESC") or sqlerr(__FILE__, __LINE__);

if (mysql_num_rows($res) == 0){
	begin_frame("Admin Log");
		print("<b><CENTER>The log is empty.</CENTER></b><br /><BR><CENTER>Items older than 24 hours are Automatically removed.</CENTER>\n");
	end_frame();

}else{

begin_frame("Admin Log", justify);
  begin_table();
  print("<tr><td class=alt3 align=left><font size=1 face=Verdana color=white>Date</td><td class=alt3 align=left><font size=1 face=Verdana color=white>Time</td><td class=alt3 align=left><font size=1 face=Verdana color=white>Event</td><!--<td class=alt3 align=left><font size=1 face=Verdana color=white>Delete Entry</td>--></tr>\n");
  while ($arr = mysql_fetch_assoc($res))
  {
    $date = substr($arr['added'], 0, strpos($arr['added'], " "));
    $time = substr($arr['added'], strpos($arr['added'], " ") + 1);
    print("<tr><td class=alt1>$date</td><td class=alt2>$time</td><td class=alt1 align=left>$arr[txt]</td><!--<td class=alt2><a href='admin.php?act=view_log&do=del_log&lid=$arr[id]' title='delete this entry'>delete</a></td>--></tr>\n");
  }
  end_table();
}
//end_frame();

	}

#======================================================================#
# IRC ANNOUNE CONFIG
#======================================================================#
if($act == "ircannounce")
{
adminonly();
		adminmenu();	// show menu
		//output
        begin_frame("IRC Announce Config", left);
        
        if ($IRCANNOUNCE){
            echo "IRC Announce is <i>enabled</i> for $SITENAME.<br>";
            echo "IRC Announce IP: $ANNOUNCEIP<br>";
            echo "IRC Announce Port: $ANNOUNCEPORT";
        }else{
            echo "IRC Annonce is <i>disabled</i> for $SITENAME. <a href=$SITEURL/admin.php?act=settings>Click here</a> to to turn on IRC Announce.";
        }
        echo "<br><br>";
        echo "IRC Server: $IRCSERVER1<br>";
        echo "IRC Channel: $IRCCHANNEL<br>";
        echo "<br><br>";
        echo "These settings can be changed via <a href=$SITEURL/admin.php?act=settings>Site Settings</a> in the <a href=$SITEURL/admin.php?act=settings>Admin CP</a>.";
        end_frame();
        
        begin_frame("mIRC Client Script", left);
            echo "The following code is required on the computer the announce messages will be sent from. Save as \"tbs_ia.mrc\".<br>";
            //classes/tbs_ia.mrc <== location of mirc script
            begin_block("Contents of TBS_IA.mrc");                
                $lines = file('backend/tbs_ia.mrc');
                foreach ($lines as $line_num => $line) {
                    echo $line . "<br />\n";
                }
                
            end_block();
            
            echo "<br<br>";
            echo "Or you can <a href=$SITEURL/backend/tbs_ia.mrc>click here</a> to <a href=$SITEURL/backend/tbs_ia.mrc>download</a> the file without copy/pasting it.<br>";
            echo "<br><br><br>";
            echo "<b><u>Instructions for installation</u></b>: <br>
                1. Start-up and run mIRC.<br>
                2. Load the script in mIRC.<br>
                &nbsp;&nbsp;• Tools -> Script Editor and select Remote<br>
                &nbsp;&nbsp;• File -> Load and select tbs_ia.mrc It will give a warning. Answer yes and it will ask you chan and port.<br>
                &nbsp;&nbsp;• File -> Save & Exit and connect to server and join the channel.<br>
                3. Restart mIRC (VERY IMPORTANT!)";
                echo "<br><br>";
                
                echo "Now, check your server screen on IRC once its loaded. If you see \"port xxxx is not free! Listener not started!\" then you have to try another port
                or check your firewall settings, etc.<br>";
                echo "Remember, the port must be OPEN on your local machine.<br>";
        end_frame();
}
    
    
#======================================================================#
#        News
#======================================================================#
if ($act === "news") {
    require_once TT_CONTR_DIR . '/admin/admin-action-news.php';
}

#======================================================================#
#	Bans
#======================================================================#
if($act == "bans")
	{
		adminmenu();	// show menu
		//output
$remove = $HTTP_GET_VARS['remove'];

if (is_valid_id($remove))
{
  mysql_query("DELETE FROM bans WHERE id=$remove") or sqlerr();
  write_log("Ban $remove was removed by $CURUSER[id] ($CURUSER[username])");
}

if ($do == 'add')
{
	$first = trim($HTTP_POST_VARS["first"]);
	$last = trim($HTTP_POST_VARS["last"]);
	$comment = trim($HTTP_POST_VARS["comment"]);
	if (!$first || !$last || !$comment)
		stderr("Error", "Missing form data.");
	$first = ip2long($first);
	$last = ip2long($last);
	if ($first == -1 || $last == -1)
		stderr("Error", "Bad IP address.");
	$comment = sqlesc($comment);
	$added = sqlesc(get_date_time());
	mysql_query("INSERT INTO bans (added, addedby, first, last, comment) VALUES($added, $CURUSER[id], $first, $last, $comment)") or sqlerr(__FILE__, __LINE__);
	bark2("Success", "The ban has been added!", Success);
	die;
}

begin_frame("Blocklist", justify);
?>
<p align="justify">This page allows you to prevent individual users or groups of users from accessing your tracker by placing a block on thier IP or IP range. If you wish to temporarily disable an account, but still wish a user to be able to view your tracker,
you can use the 'Disable Account' option which is found in the user's profile page.</p>
<?php 
end_frame();

begin_frame("Blocklist", 'center');

$chk = mysql_query("SELECT * FROM bans") or sqlerr();
if (mysql_num_rows($chk) == 0)
  print("<b>No Bans Found</b><br />\n");
else
{
  print("<table border=1 cellspacing=0 cellpadding=5>\n");
  print("<tr><td class=table_head>Added</td><td class=table_head align=left>First IP</td><td class=table_head align=left>Last IP</td>".
    "<td class=table_head align=left>By</td><td class=table_head align=left>Comment</td><td class=table_head>Remove</td></tr>\n");

  $page = $HTTP_GET_VARS['page'];
  $perpage = 50;
  $cnt = mysql_query("SELECT COUNT(*) FROM bans") or sqlerr();
  $arr = mysql_fetch_row($cnt);
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
  	$pagemenu .= "<b>$i</b>\n";
     else
  	$pagemenu .= "<a href=admin.php?act=bans&page=$i><b>$i</b></a>\n";
  if ($page == 1)
  	$browsemenu .= "<b>&lt;&lt; Prev</b>";
  else
  	$browsemenu .= "<a href=admin.php?act=bans&$q&page=" . ($page - 1) . "><b>&lt;&lt; Prev</b></a>";
  $browsemenu .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
  if ($page == $pages)
  	$browsemenu .= "<b>Next &gt;&gt;</b>";
  else
  	$browsemenu .= "<a href=admin.php?act=bans&$q&page=" . ($page + 1) . "><b>Next &gt;&gt;</b></a>";
  $offset = ($page * $perpage) - $perpage;
  print("<p>$browsemenu<br /><br />$pagemenu</p>");
  $res = mysql_query("SELECT * FROM bans ORDER BY added LIMIT $offset,$perpage") or sqlerr();
  $num = mysql_num_rows($res);
  for ($i = 0; $i < $num; ++$i)
  {
   while ($arr = mysql_fetch_assoc($res))
   {
  	$r2 = mysql_query("SELECT username FROM users WHERE id=$arr[addedby]") or sqlerr();
  	$a2 = mysql_fetch_assoc($r2);
	$arr["first"] = long2ip($arr["first"]);
	$arr["last"] = long2ip($arr["last"]);
 	  print("<tr><td>$arr[added]</td><td align=left>$arr[first]</td><td align=left>$arr[last]</td><td align=left><a href=account-details.php?id=$arr[addedby]>$a2[username]".
 	    "</a></td><td align=left>$arr[comment]</td><td><a href=admin.php?act=bans&remove=$arr[id]>Remove</a></td></tr>\n");
   }
  }
  print("</table>\n");
  print("<p><b>Note</b> - bans expires after 90 days</p>\n");
}
end_frame();

if (get_user_class() >= UC_JMODERATOR)
{
	begin_frame("Add Ban", 'center');
	print("<table border=1 cellspacing=0 cellpadding=5>\n");
	print("<form method=post action=admin.php?act=bans&do=add>\n");
	print("<tr><td class=rowhead>First IP</td><td><input type=text name=first size=40></td>\n");
	print("<tr><td class=rowhead>Last IP</td><td><input type=text name=last size=40></td>\n");
	print("<tr><td class=rowhead>Comment</td><td><input type=text name=comment size=40></td>\n");
	print("<tr><td colspan=2><input type=submit value='Okay' class=btn></td></tr>\n");
	print("</form>\n</table>\n");
	end_frame();
}

	print("</form>");
	}

#======================================================================#
#	User Settings
#======================================================================#
if ($act == "users") {

$search = trim($search);
$class = $class;
if ($class == '-' || !is_valid_id($class))
 $class = '';

if ($search != '' || $class)
{
 $query = "username LIKE " . sqlesc("%$search%") . " AND status='confirmed'";
 $q = 'search=' . h($search);
}
else
{
$letter = trim($letter);
 if (strlen($letter) > 1)
   die;

 if ($letter == "" || strpos("abcdefghijklmnopqrstuvwxyz", $letter) === false)
   $query = "status='confirmed'";
 else
  $query = "username LIKE '$letter%' AND status='confirmed'";
 $q = "letter=$letter";
}

if ($class)
{
 $query .= " AND class=$class";
 $q .= '&class=$class';
}

stdhead("Users");
adminmenu();
begin_frame($txt['MEMBERS'], 'center');
print("<center><a href='admin.php?act=confirmreg'>Manual Confirm User Registration</a></center><br>\n");
print("<center><a href='cheats.php'>Check For Possible Cheaters</a></center><br>\n");
print("<br /><form method=get action=?>\n");
print("<input type=hidden name=act value=users>\n");
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
print("<input type=submit value='Okay'>\n");
print("</form>\n");

print("<p>\n");

print("<a href=admin.php?act=users><b>ALL</b></a> - \n");
for ($i = 97; $i < 123; ++$i)
{
$l = chr($i);
$L = chr($i - 32);
if ($l == $letter)
   print("<b>$L</b>\n");
else
   print("<a href=?letter=$l&act=users><b>$L</b></a>\n");
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
$pagemenu .= "<center>";
for ($i = 1; $i <= $pages; ++$i)
 if ($i == $page)
   $pagemenu .= "$i\n";
 else
   $pagemenu .= "<a href=?$q&page=$i&act=users>$i</a>\n";

if ($page == 1)
 $browsemenu .= "";
//  $browsemenu .= "[Prev]";
else
 $browsemenu .= "<a href=?$q&page=" . ($page - 1) . "&act=users>[Prev]</a>";

$browsemenu .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

if ($page == $pages)
 $browsemenu .= "";
//  $browsemenu .= "[Next]";
else
 $browsemenu .= "<a href=?$q&page=" . ($page + 1) . "&act=users>[Next]</a>";

$browsemenu .= "</center>";

$offset = ($page * $perpage) - $perpage;

$res = mysql_query("SELECT * FROM users WHERE $query ORDER BY username LIMIT $offset,$perpage") or sqlerr();
$num = mysql_num_rows($res);

begin_table();
print("<tr><td align=\"center\"  class=alt3 align=left><font size=1 face=Verdana color=white>" . USERNAME . "</td><td align=\"center\"  class=alt3 align=left><font size=1 face=Verdana color=white>Delete</td><td align=\"center\"  class=alt3><font size=1 face=Verdana color=white>" . REGISTERED . "</td><td align=\"center\"  class=alt3><font size=1 face=Verdana color=white>" . LAST_ACCESS . "</td><td align=\"center\"  class=alt3 align=left><font size=1 face=Verdana color=white>" . RANK . "</td><td align=\"center\"  class=alt3><font size=1 face=Verdana color=white>" . COUNTRY . "</td></tr>\n");
for ($i = 0; $i < $num; ++$i)
{
 $arr = mysql_fetch_assoc($res);
 if ($arr['country'] > 0)
 {
   $cres = mysql_query("SELECT name,flagpic FROM countries WHERE id=$arr[country]");
   if (mysql_num_rows($cres) == 1)
   {
     $carr = mysql_fetch_assoc($cres);
     $country = "<td align=\"center\"  class=alt1 style='padding: 0px' align='center'><img src=". $SITEURL ."/images/flag/$carr[flagpic] alt='$carr[name]' /></td>";
   }
 }
 else
   $country = "<td align=\"center\"  class=alt1 style='padding: 0px' align='center'><img src=". $SITEURL ."/images/flag/unknown.gif alt=Unknown /></td>";
 if ($arr['added'] == '0000-00-00 00:00:00')
   $arr['added'] = '-';
 if ($arr['last_access'] == '0000-00-00 00:00:00')
   $arr['last_access'] = '-';
 print("<tr><td align=\"center\"  class=alt1 align=left><a href=account-details.php?id=$arr[id]>" .($arr["class"] > 1 ? "<font color=#A83838>" : "")."<b>$arr[username]</b></font></a>" .($arr["donated"] > 0 ? "<img src=$SITEURL/images/star.gif border=0 alt='Donated $$arr[donated]'>" : "")."</td>" .
 
"<td align=\"center\"  class=alt1 align=left><a href=admin.php?act=deluser&id=$arr[id]>Delete User</a></td>".

"<td align=\"center\"  class=alt2>$arr[added]</td><td align=\"center\"  class=alt1>$arr[last_access]</td>".
   "<td align=\"center\"  class=alt2 align=left>" . get_user_class_name($arr["class"]) . "</td>$country</tr>\n");
}

end_table();
end_frame();

print("<p>$pagemenu<br />$browsemenu</p>");
   
}

if($act == "deluser")
{
 $id = (int) $id;
 $res = mysql_query("DELETE FROM users WHERE id = ".$id." AND class < '1'");
 
 begin_frame("Delete User", 'center');
 if (mysql_affected_rows() > 0) {
 print("<b>User Nr: ".$id." deleted </b>");
 }else{
 stderr("User doesn't exist or is superior to you");
 }
 print("<br><br><a href='admin.php?act=users'> Go Back </a>");
 end_frame();
}
#======================================================================#
#	Manual Conf Reg
#======================================================================#
if($act == "confirmreg")
{
adminmenu(); 
begin_frame("Info On This List", justify);
?>
<p align="justify">This page shows all users that have not clicked the ACTIVATION link in the signup email, they cannot access the site until they have clicked this link.  You should only manually confirm a user if they request it (via email, irc or other method), where they have lost or not received the email.  All PENDING users will be cleaned from the system every so often.</p>
<?php 
end_frame();
begin_frame("Manual Registration Confirm", 'center');
begin_table();
$perpage = 100;
print("<tr><td align=\"center\"  class=alt3 align=left><font size=1 face=Verdana color=white>Username</td><td align=\"center\"  class=alt3><font size=1 face=Verdana color=white>Email Address</td><td align=\"center\"  class=alt3><font size=1 face=Verdana color=white>Date Registered</td><td align=\"center\"  class=alt3 align=left><font size=1 face=Verdana color=white>IP</td><td align=\"center\"  class=alt3><font size=1 face=Verdana color=white>Status</td></tr>\n");

$resww = "SELECT * FROM users WHERE status='pending' ORDER BY username";
$reqww = mysql_query($resww);
while ($row = mysql_fetch_array($reqww))
	{
	 extract ($row);
  echo "<tr><td>$row[username]</td><td>$row[email]</td><td>$row[added]</td><td>$row[ip]</td><td><a href='admin.php?act=editreg&id=$row[id]'>$row[status]</a></td></tr>\n";

	}
end_table();
end_frame();
}
if ($do == "save_editreg")
// SAVE THEME EDIT FUNCTION
	{
		mysql_query(" UPDATE users SET status='$ed_status' WHERE id=$id");
echo "<br><br><center><b>Updated Completed</b></center>";
}

if($act == "editreg" && $id != "")
// EDIT USER REG FORM
{
	$qq = MYSQL_QUERY("SELECT * FROM users WHERE id = $id");
	$ee = MYSQL_FETCH_ARRAY($qq);
	adminmenu();
	begin_frame();
	?>

	<form action='admin.php' method='post'>
	<input type='hidden' name='sid' value='<?=$sid?>'>
	<input type='hidden' name='id' value='<?=$id?>'>
	<input type='hidden' name='do' value='save_editreg'>
	Name: <?=$ee[username]?><br />
	Surrent Status: <?=$ee[status]?><br>
	<select name='ed_status'>
		<option value='pending' <?php if($status == "pending") echo "selected"; ?>>pending
		<option value='confirmed' <?php if($status == "confirmed") echo "selected"; ?>>confirmed
		</select>
	<!--<input type='text' value='<?=$ee[status]?>' size='30' maxlength='30' name='ed_status'><br />-->
	<input type='submit' value='   Save   ' style='background:#eeeeee'>&nbsp;&nbsp;&nbsp;<input type='reset' value='  Reset  ' style='background:#eeeeee'>
	</form>
	<?php 
		end_frame();
}
#======================================================================#
#	Torrent Management
#======================================================================#
if($act == "torrents")
{
	adminmenu(); 
begin_frame("TORRENT MANAGEMENT", 'center');
	?>
<table align=center cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#D6D9DB" width="100%" border="1">
<tr>
<td class=alt3 align=center><font size=1 face=Verdana color=white>Name</td>
<td class=alt3 align=center><font size=1 face=Verdana color=white>Visible</td>
<td class=alt3 align=center><font size=1 face=Verdana color=white>Banned</td>
<td class=alt3 align=center><font size=1 face=Verdana color=white>Seeders</td>
<td class=alt3 align=center><font size=1 face=Verdana color=white>Leechers</td>
<td class=alt3 align=center><font size=1 face=Verdana color=white>External?</td>
<td class=alt3 align=center><font size=1 face=Verdana color=white>Edit?</td>
</tr>
<?php 
$rqq = "SELECT * FROM torrents ORDER BY name";
$resqq = mysql_query($rqq);
 while ($row = mysql_fetch_array($resqq))
{
 extract ($row);
   echo "<tr><td>" . $row["name"] . "</td><td>$row[visible]</td><td>$row[banned]</td><td>$row[seeders]</td><td>$row[leechers]</td><td>$row[external]</td><td><a href=\"torrents-edit.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&amp;id=" . $row["id"] . "\"><font size=1 face=Verdana>EDIT</a></td></tr>\n";
}

	echo "</table>\n";
	end_frame();
}

#======================================================================#
#	Banned Torrents List
#======================================================================#
if($act == "bannedtorrents")
{
	adminmenu(); 
begin_frame("BANNED TORRENT MANAGEMENT", 'center');
	?>
<table align=center cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#D6D9DB" width="100%" border="1">
<tr>
<td class=alt3 align=center><font size=1 face=Verdana color=white>Name</td>
<td class=alt3 align=center><font size=1 face=Verdana color=white>Visible</td>
<td class=alt3 align=center><font size=1 face=Verdana color=white>Banned</td>
<td class=alt3 align=center><font size=1 face=Verdana color=white>Seeders</td>
<td class=alt3 align=center><font size=1 face=Verdana color=white>Leechers</td>
<td class=alt3 align=center><font size=1 face=Verdana color=white>External?</td>
<td class=alt3 align=center><font size=1 face=Verdana color=white>Edit?</td>
</tr>
<?php 
$rqqw = "SELECT * FROM torrents WHERE banned='yes' ORDER BY name";
$resqqw = mysql_query($rqqw);
 while ($row = mysql_fetch_array($resqqw))
{
 extract ($row);
   echo "<tr><td>" . $row["name"] . "</td><td>$row[visible]</td><td>$row[banned]</td><td>$row[seeders]</td><td>$row[leechers]</td><td>$row[external]</td><td><a href=\"torrents-edit.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&amp;id=" . $row["id"] . "\"><font size=1 face=Verdana>EDIT</a></td></tr>\n";
}

	echo "</table>\n";
	end_frame();
}
#======================================================================#
#	Message Spy
#======================================================================#
if($act == "msgspy")
{
	adminonly();
adminmenu(); 
begin_frame("Messages Spy", 'center');
//////////PAGER////////////
$res2 = mysql_query("SELECT COUNT(*) FROM messages $where");
        $row = mysql_fetch_array($res2);
        $count = $row[0];
$perpage = 5000;
    list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] ."&act=msgspy?" );
echo $pagertop;
//////////END PAGER///////////
$res = mysql_query("SELECT * FROM messages ORDER BY id DESC $limit") or sqlerr(__FILE__, __LINE__);
  print("<table border=1 cellspacing=0 cellpadding=3>\n");
  ///////////////////////////////////////
?>
<form method="post" action="<?=$GLOBALS['SITEURL']?>/take-deletepm.php">
<?php 
///////////////////////////////////////
  print("<tr><td class=table_head align=left>Sender</td><td class=table_head align=left>Receiver</td><td class=table_head align=left>Text</td><td class=table_head align=left>Date</td><td class=table_head>Delete</td></tr>\n");
  while ($arr = mysql_fetch_assoc($res))
  {
    $res2 = mysql_query("SELECT username FROM users WHERE id=" . $arr["receiver"]) or sqlerr();
    $arr2 = mysql_fetch_assoc($res2);
    $receiver = "<a href=account-details.php?id=" . $arr["receiver"] . "><b>" . $arr2["username"] . "</b></a>";
    $res3 = mysql_query("SELECT username FROM users WHERE id=" . $arr["sender"]) or sqlerr();
    $arr3 = mysql_fetch_assoc($res3);
    $sender = "<a href=account-details.php?id=" . $arr["sender"] . "><b>" . $arr3["username"] . "</b></a>";
             if( $arr["sender"] == 0 )
             $sender = "<font color=red><b>System</b></font>";
    $msg = format_comment($arr["msg"]);
  $added = format_comment($arr["added"]);
  print("<tr><td align=left class=table_col1>$sender</td><td align=left class=table_col2>$receiver</td><td align=left class=table_col1>$msg</td><td align=left class=table_col2>$added</td>");
/////////////////////////////////////////////////////////////////////////////////
  if ($_GET[check] == "yes") {
    echo("</td><TD class=table_col1><INPUT type=\"checkbox\" checked name=\"delmp[]\" value=\"" . $arr['id'] . "\"></TD>\n</TR>\n");
   }
   else {
    echo("</td><TD class=table_col1><INPUT type=\"checkbox\" name=\"delmp[]\" value=\"" . $arr['id'] . "\"></TD>\n</TR>\n");
   }

  /////////////////////////////////////////////////////////////////////////////////
}
print("</table>");
?><BR>
<input type="submit" value="Delete!" />
</form>
<!------------------------------------------------->
<A href="admin.php?act=msgspy&page=<?php echo $_GET[page];?>action=<?php echo $_GET[action]; ?>&box=<?php echo $_GET[box]; ?>&check=yes">Select All</A>
<!------------------------------------------------->
<?php 
print($pagerbottom);

end_frame();
}
#======================================================================#
#	Manual Added Banned Torrents List
#======================================================================#
if($act == "bannedtorrentsmanual")
{
	adminmenu(); 
begin_frame("BANNED TORRENT MANUAL ADDED LIST", 'center');
	?>
AHA not yet matey...

<?php 
	end_frame();
}
#======================================================================#
#	Theme Settings
#======================================================================#
if ($act == "style") {
	adminonly();
	adminmenu();
	begin_frame("Themes Management", 'center');
	?>
	<b>Add A Theme: </b><a href='admin.php?act=add_theme'>CLICK HERE</a><br><br>
	<b>Delete A Theme: </b><a href='admin.php?act=del_theme'>CLICK HERE</a><br><br>
	<h5>Current Themes:</h5>
	Click a theme to edit<br><br>
	<table width='60%'>
	<tr>
	<TD><b>Theme Name</b></td><TD><b>Folder Name</b></td><TD><b>ID</b></td></tr>
	<?php 
	// LIST THEME
	$querya = DB::query("SELECT * FROM stylesheets ORDER BY name");
	$allthemes = 0;
    while ($row = $querya->fetch()) {
        echo "<tr>
            <td><font size='2'><b><a href='admin.php?do=edtheme&id=$row[id]'>$row[name]</b></a></font></td>
            <td>$row[uri]</td><td>$row[id]</td></tr>\n";
    }

    if (! $allthemes) {
        echo "<h4>None</h4>\n";
    }

	echo "</table>\n";
	end_frame();
}

// ADD THEME TO DATABASE FUNCTION
if ($do == "add_this_theme") {
	adminmenu();
    $new_theme_name = $_POST['new_theme_name'] ?? '';
	$new_uri = $_POST['new_uri'] ?? '';
    $error_ac == "";
	if ($new_theme_name == "")
        $error_ac .= "<br><br><li>Theme Name was empty\n";
	if ($new_uri == "")
        $error_ac .= "<br><br><li>Folder Name was empty\n";

	if ($error_ac == "") {
		DB::insert('stylesheets', ['name' => $new_theme_name, 'uri' => $new_uri]);
        echo "<br><br><center><b>Theme Added OK</b></center>";
	} else {
        echo "ERROR! Please fill out all fields!<p>:: <a href='javascript:history.back()'>back</a>\n";
    }
}

// ADD THEME FORM
if ($act == "add_theme" || $error_ac != "") {
	adminmenu();
	begin_frame();
    $new_theme_name = $_POST['new_theme_name'] ?? '';
	$new_uri = $_POST['new_uri'] ?? '';
	?>
	<p>
	<table align='center' width='80%' bgcolor='#cecece' cellspacing='2' cellpadding='2' style='border: 1px solid black'>
	<form action='admin.php' method='post'>
	<input type='hidden' name='sid' value='<?=$sid?>'>
	<input type='hidden' name='act' value='sql'>
	<input type='hidden' name='do' value='add_this_theme'>
	<tr>
	<td>Name of the new Theme:</td>
	<td align='right'><input type='text' name='new_theme_name' size='30' maxlength='30' value='<?= $new_theme_name ?>'></td>
	</tr>
	<tr>
	<td>Folder Name (case SenSiTive):</td>
	<td align='right'><input type='text' name='new_uri' size='30' maxlength='30' value='<?= $new_uri ?>'></td>
	</tr>
	<tr>
	<td colspan='2' align='center'>
	<input type='submit' value='Add new theme'>
	<input type='reset' value='Reset'>
	</td>
	</tr>
	<?php 
	if($error_ac != "") echo "<tr><td colspan='2' align='center' style='background:#eeeeee;border:2px red solid'><b>COULD NOT ADD NEW THEME:</b><br />$error_ac</tr></td>\n";
	?>
	</table>
		<br>Please note: All themes must be uploaded to the /themes/ folder.  Please make sure all folder names are EXACT.
	<?php 
		end_frame();
}

if($do == "save_edtheme")
// SAVE THEME EDIT FUNCTION
{
	if($ed_name != "" && $ed_uri != "")
	{
		mysql_query(" UPDATE stylesheets SET name='$ed_name' ,uri='$ed_uri' WHERE id=$id");
bark2("Success", "Theme Updated", Success);
	} else {bark2("ERROR", "Please fill out all fields", Success); }
}

if($do == "edtheme" && $id != "")
// EDIT THEME FORM
{
	begin_frame();
	$q = MYSQL_QUERY("SELECT * FROM stylesheets WHERE id = $id");
	$r = MYSQL_FETCH_ARRAY($q);

	?>
	<form action='admin.php' method='post'>
	<input type='hidden' name='sid' value='<?=$sid?>'>
	<input type='hidden' name='id' value='<?=$id?>'>
	<input type='hidden' name='do' value='save_edtheme'>
	Name:<br />
	<input type='text' value='<?=$r[name]?>' size='30' maxlength='30' name='ed_name'><br />
	Folder Name (case SenSiTive):<br>
	<input type='text' value='<?=$r[uri]?>' size='30' maxlength='30' name='ed_uri'><br />
	<input type='submit' value='   Save   ' style='background:#eeeeee'>&nbsp;&nbsp;&nbsp;<input type='reset' value='  Reset  ' style='background:#eeeeee'>
	</form>
	<br>Please note: All themes must be uploaded to the /themes/ folder.  Please make sure all folder names are EXACT.
	<?php 
		end_frame();
}

if($do == "del_this_theme")
//DELETE THEME FROM DATABASE FUNCTION
{
	$error_ac == "";
	if($ed_id == "") $error_ac .= "<br><br><li>Theme ID was empty\n";

	if($error_ac == "")
	{
		mysql_query(" DELETE FROM stylesheets WHERE id = $ed_id");
bark2("Success", "Theme Deleted OK", Success);
	} else {bark2("ERROR", "Please fill out all fields", Success); }
}

if($act == "del_theme" || $error_ac != "")
// DELETE THEME FORM
{
	adminmenu();
	begin_frame();
	?>
	<p>
	<form action='admin.php' method='post'>
	<input type='hidden' name='sid' value='<?=$sid?>'>
	<input type='hidden' name='id' value='<?=$id?>'>
	<input type='hidden' name='do' value='del_this_theme'>
	Enter Theme ID to Delete:<br />
	<input type='text' value='' size='30' maxlength='30' name='ed_id'><br />
	<input type='submit' value='   Delete   ' style='background:#eeeeee'>&nbsp;&nbsp;&nbsp;<input type='reset' value='  Reset  ' style='background:#eeeeee'>
	</form><br>
	<b>NOTE: DELETING A THEME DOES NOT REMOVE THE THEME FROM THE SERVER, IT ONLY REMOVES IT FROM BEING SELECTABLE</b>
	<?php 
		end_frame();
}

#======================================================================#
# Forum Settings
# Last Edited: TorrentialStorm 01/2/07 @ 14:27 GMT
#======================================================================#

if($do == "add_this_forum")
//add to db & create autolink
{
$error_ac == "";
if($new_forum_name == "") $error_ac .= "<li>Forum-name was empty\n";
if($new_desc == "") $error_ac .= "<li>Forum-description was empty\n";
if($new_forum_sort == "") $error_ac .= "<li>Forum sort order was empty\n";
if($new_forum_cat == "") $error_ac .= "<li>Forum category was empty\n";

if($error_ac == "")
{
  $sql = "INSERT INTO forum_forums (`name`, `description`, `sort`, `category`, `minclassread`, `minclasswrite`) VALUES ('$new_forum_name', '$new_desc', '$new_forum_sort', '$new_forum_cat', '$minclassread', '$minclasswrite')";
  $ok = MYSQL_QUERY($sql);
  if($ok) autolink("admin.php?act=forum", "Thank you, new forum added to db ...");
  else echo "<h4>Could not save to DB - check your connection & settings!</h4>";
}
}

if($do == "add_this_forumcat")
//add to db & create autolink
{
$error_ac == "";
if($new_forumcat_name == "") $error_ac .= "<li>Forum cat name was empty\n";
if($new_forumcat_sort == "") $error_ac .= "<li>Forum cat sort order was empty\n";

if($error_ac == "")
{
  $sql = "INSERT INTO forumcats (`name`, `sort`) VALUES ('$new_forumcat_name', '$new_forumcat_sort')";
  $ok = MYSQL_QUERY($sql);
  if($ok) autolink("admin.php?act=forum", "Thank you, new forum cat added to db ...");
  else echo "<h4>Could not save to DB - check your connection & settings!</h4>";
}
}

//save edited data
if($do == "save_edit")
{
        mysql_query("UPDATE forum_forums SET sort = '$changed_sort', name = '$changed_forum', description = '$changed_forum_desc', category = '$changed_forum_cat', minclassread='$minclassread', minclasswrite='$minclasswrite' WHERE id='$id'");
echo "<br><br><center><b>Updated Completed</b></center>";

}

//save edited data cat
if($do == "save_editcat")
{
        mysql_query("UPDATE forumcats SET sort = '$changed_sortcat', name = '$changed_forumcat' WHERE id='$id'");
echo "<br><br><center><b>Updated Completed</b></center>";

}

//finally delete forum
if($do == "delete_forum")
{
if($delcat != "")
{
  $sql2 = "DELETE FROM forum_forums WHERE id = '$id'";
        $ok2 = MYSQL_QUERY($sql2);
  if($ok2) autolink("admin.php?act=forum", "forum deleted ...");
}}

//finally delete forumcat
if($do == "delete_forumcat")
{
if($delcat != "")
{
  $sql2 = "DELETE FROM forumcats WHERE id = '$id'";
        $ok2 = MYSQL_QUERY($sql2);
  if($ok2) autolink("admin.php?act=forum", "forum cat deleted ...");
}}

if($act == "forum" || $error_ac != "")
{
//form to add forum
adminonly();
adminmenu();
begin_frame("Forums Management");
$query = MYSQL_QUERY("SELECT * FROM forumcats ORDER BY sort, name");
$allcat = MYSQL_NUM_ROWS($query);
while($row =MYSQL_FETCH_ARRAY($query)) {
$forumcat[] = $row;
}
MYSQL_FREE_RESULT($query);
?>
<p>
<table align='center' width='80%' bgcolor='#cecece' cellspacing='2' cellpadding='2' style='border: 1px solid black'>
<form action='admin.php' method='post'>
<input type='hidden' name='sid' value='<?=$sid?>'>
<input type='hidden' name='act' value='sql'>
<input type='hidden' name='do' value='add_this_forum'>
<tr>
<td>Name of the new Forum:</td>
<td align='right'><input type='text' name='new_forum_name' size='30' maxlength='30'  value='<?=$new_forum_name?>'></td>
</tr>
<tr>
<td>Forum Sort Order:</td>
<td align='right'><input type='text' name='new_forum_sort' size='10' maxlength='10'  value='<?=$new_forum_sort?>'></td>
</tr>
<tr>
<td>Description of the new Forum:</td>
<td align='right'><textarea cols='50' rows='5' name='new_desc'><?=$new_desc?></textarea></td>
</tr>
<tr>
<td>Forum Category:</td>
<td align='right'><select name='new_forum_cat'>
<?php 
foreach ($forumcat as $row)
echo "<option value={$row['id']}>{$row['name']}</option>";
?>
</select>
</tr>
<tr><td>Mininum Class Needed to Read:</td>
<td align='right'><select name='minclassread'>
<option value='<?=UC_USER?>' <?=$r[minclassread]==UC_USER?'selected':''?>>User</option>
<option value='<?=UC_UPLOADER?> <?=$r[minclassread]==UC_UPLOADER?'selected':''?>'>Uploader</option>
<option value='<?=UC_VIP?>' <?=$r[minclassread]==UC_VIP?'selected':''?>>VIP</option>
<option value='<?=UC_JMODERATOR?>' <?=$r[minclassread]==UC_JMODERATOR?'selected':''?>>Moderator</option>
<option value='<?=UC_MODERATOR?>' <?=$r[minclassread]==UC_MODERATOR?'selected':''?>>Super Moderator</option>
<option value='<?=UC_ADMINISTRATOR?>' <?=$r[minclassread]==UC_ADMINISTRATOR?'selected':''?>>Administrator</option>
</select></td></tr>
<tr><td>Mininum Class Needed to Post:</td>
<td align='right'><select name='minclasswrite'>
<option value='<?=UC_USER?>' <?=$r[minclasswrite]==UC_USER?'selected':''?>>User</option>
<option value='<?=UC_UPLOADER?>' <?=$r[minclasswrite]==UC_UPLOADER?'selected':''?>>Uploader</option>
<option value='<?=UC_VIP?>' <?=$r[minclasswrite]==UC_VIP?'selected':''?>>VIP</option>
<option value='<?=UC_JMODERATOR?>' <?=$r[minclasswrite]==UC_JMODERATOR?'selected':''?>>Moderator</option>
<option value='<?=UC_MODERATOR?>' <?=$r[minclasswrite]==UC_MODERATOR?'selected':''?>>Super Moderator</option>
<option value='<?=UC_ADMINISTRATOR?> <?=$r[minclasswrite]==UC_ADMINISTRATOR?'selected':''?>'>Administrator</option>
</select></td></tr>
    
    <tr>
<td colspan='2' align='center'>
<input type='submit' value='Add new forum'>
<input type='reset' value='Reset'>
</td>
</tr>
<?php 
if($error_ac != "") echo "<tr><td colspan='2' align='center' style='background:#eeeeee;border:2px red solid'><b>COULD  NOT ADD NEW forum:</b><br />$error_ac</tr></td>\n";
?>
</table>
</form>
<p>
<table align='center' width='80%' bgcolor='#cecece' cellspacing='2' cellpadding='2' style='border: 1px solid black'>
<h5>Current Forums:</h5>
<?php 
// get forum from db
echo "<tr><td width='60'><font size='2'><b>ID</b></td><td width='120'>NAME</td><td  width='250'>DESC</td><td width='45'>SORT</td><td width='45'>CATEGORY</td><td width='18'>EDIT</td><td width='18'>DEL</td></font>\n";
$query = MYSQL_QUERY("SELECT * FROM forum_forums ORDER BY sort, name");
$allforums = MYSQL_NUM_ROWS($query);
if($allforums == 0) {
  echo "<h4>None</h4>\n";
} else {
  while($row =MYSQL_FETCH_ARRAY($query))
  {
foreach ($forumcat as $cat) if ($cat['id'] == $row['category']) $category = $cat['name'];
  echo "<tr><td width='60'><font size='2'><b>ID($row[id])</b></td><td width='120'> $row[name]</td><td  width='250'>$row[description]</td><td width='45'>$row[sort]</td><td width='45'>$category</td></font>\n";
            echo "<td width='18'><a href='admin.php?do=edit_forum&id=$row[id]'>[Edit]</a></td>\n";
            echo "<td width='18'><a href='admin.php?do=del_forum&id=$row[id]'><img src='images/delete.gif' alt='Delete  Category' width='17' height='17' border='0'></a></td></tr>\n";
  }
MYSQL_FREE_RESULT($query);
} //endif
echo "</table>\n";
?>
<BR><table align='center' width='80%' bgcolor='#cecece' cellspacing='2' cellpadding='2' style='border: 1px solid black'>
<h5>Current Forum Categories:</h5>
<?php 
// get forum from db
echo "<tr><td width='60'><font size='2'><b>ID</b></td><td width='120'>NAME</td><td  width='18'>SORT</td><td width='18'>EDIT</td><td width='18'>DEL</td></font>\n";

if($allcat == 0) {
  echo "<h4>None set</h4>\n";
} else {
  foreach ($forumcat as $row)
  {
  echo "<tr><td width='60'><font size='2'><b>ID($row[id])</b></td><td width='120'> $row[name]</td><td width='18'>$row[sort]</td>\n";
            echo "<td width='18'><a href='admin.php?do=edit_forumcat&id=$row[id]'>[Edit]</a></td>\n";
            echo "<td width='18'><a href='admin.php?do=del_forumcat&id=$row[id]'><img src='images/delete.gif' alt='Delete  Category' width='17' height='17' border='0'></a></td></tr>\n";
  }
} //endif
echo "</table>\n";
?>
<BR><table align='center' width='80%' bgcolor='#cecece' cellspacing='2' cellpadding='2' style='border: 1px solid black'>
<form action='admin.php' method='post'>
<input type='hidden' name='sid' value='<?=$sid?>'>
<input type='hidden' name='act' value='sql'>
<input type='hidden' name='do' value='add_this_forumcat'>
<tr>
<td>Name of the new Category:</td>
<td align='right'><input type='text' name='new_forumcat_name' size='30' maxlength='30'  value='<?=$new_forumcat_name?>'></td>
</tr>
<tr>
<td>Category Sort Order:</td>
<td align='right'><input type='text' name='new_forumcat_sort' size='10' maxlength='10'  value='<?=$new_forumcat_sort?>'></td>
</tr>
    
    <tr>
<td colspan='2' align='center'>
<input type='submit' value='Add new category'>
<input type='reset' value='Reset'>
</td>
</tr>
</table>
</form>
<?php 
        end_frame();
}
//edit forum
if($do == "edit_forum")
{
    begin_frame("Edit Forum");
$q = MYSQL_QUERY("SELECT * FROM forum_forums WHERE id = '$id'");
$r = MYSQL_FETCH_ARRAY($q);
MYSQL_FREE_RESULT($q);
?>
      <table align='center' width='80%' bgcolor='#cecece' cellspacing='2' cellpadding='2' style='border: 1px solid black'>

  <form action="admin.php" method="post">
  <input type="hidden" name="do" value="save_edit">
  <input type="hidden" name="id" value="<?=$id?>">
      <tr><td>New Name for Forum:</td>
      <td align='right'><input type="text" name="changed_forum" class="option" size="35" value="<?=$r[name]?>"></td></tr>
      <tr><td>New Sort Order:</td>
      <td align='right'><input type="text" name="changed_sort" class="option" size="35" value="<?=$r[sort]?>"></td></tr>
      <tr><td>Description:</td>
      <td align='right'><textarea cols='50' rows='5' name='changed_forum_desc'><?=$r[description]?></textarea></td></tr>
      <tr><td>New Category:</td>
      <td align='right'><select name='changed_forum_cat'>
<?php 
$query = MYSQL_QUERY("SELECT * FROM forumcats ORDER BY sort, name");
while ($row=mysql_fetch_array($query))
echo "<option value={$row['id']}>{$row['name']}</option>";
MYSQL_FREE_RESULT($query);
?>
</select></td></tr>
<tr><td>Mininum Class Needed to Read:</td>
<td align='right'><select name='minclassread'>
<option value='<?=UC_USER?>' <?=$r[minclassread]==UC_USER?'selected':''?>>User</option>
<option value='<?=UC_UPLOADER?> <?=$r[minclassread]==UC_UPLOADER?'selected':''?>'>Uploader</option>
<option value='<?=UC_VIP?>' <?=$r[minclassread]==UC_VIP?'selected':''?>>VIP</option>
<option value='<?=UC_JMODERATOR?>' <?=$r[minclassread]==UC_JMODERATOR?'selected':''?>>Moderator</option>
<option value='<?=UC_MODERATOR?>' <?=$r[minclassread]==UC_MODERATOR?'selected':''?>>Super Moderator</option>
<option value='<?=UC_ADMINISTRATOR?>' <?=$r[minclassread]==UC_ADMINISTRATOR?'selected':''?>>Administrator</option>
</select></td></tr>
<tr><td>Mininum Class Needed to Post:</td>
<td align='right'><select name='minclasswrite'>
<option value='<?=UC_USER?>' selected>User</option>
<option value='<?=UC_UPLOADER?>'>Uploader</option>
<option value='<?=UC_VIP?>'>VIP</option>
<option value='<?=UC_JMODERATOR?>'>Moderator</option>
<option value='<?=UC_MODERATOR?>'>Super Moderator</option>
<option value='<?=UC_ADMINISTRATOR?>'>Administrator</option>
</select></td></tr>
      <tr><td><input type="submit" class="button" value="Change"></td></tr>
      </form>
      </table>
<?php 
    end_frame();
}

//del Forum
if($do == "del_forum")
{
    begin_frame("Confirm");
$t = MYSQL_QUERY("SELECT * FROM forum_forums WHERE id = '$id'");
$v = MYSQL_FETCH_ARRAY($t);
?>
  <form action="admin.php" method="post">
  <input type="hidden" name="do" value="delete_forum">
  <input type="hidden" name="id" value="<?=$id?>">
      Really delete the Forum <?="<b>$v[name] with ID$v[id] ???</b>"?>
      <input type="submit" name="delcat" class="button" value="Delete">
      </form>
<?php 
          end_frame();
}

      //del Forum
if($do == "del_forumcat")
{
    begin_frame("Confirm");
$t = MYSQL_QUERY("SELECT * FROM forumcats WHERE id = '$id'");
$v = MYSQL_FETCH_ARRAY($t);
?>
  <form action="admin.php" method="post">
  <input type="hidden" name="do" value="delete_forumcat">
  <input type="hidden" name="id" value="<?=$id?>">
      Really delete the Forum category<?="<b>$v[name] with ID$v[id] ???</b> All Sub Forums will now be invisible"?>
      <input type="submit" name="delcat" class="button" value="Delete">
      </form>
<?php 
          end_frame();
}

//edit forum
if($do == "edit_forumcat")
{
    begin_frame("Edit Category");
$q = MYSQL_QUERY("SELECT * FROM forumcats WHERE id = '$id'");
$r = MYSQL_FETCH_ARRAY($q);
?>
      <table align='center' width='80%' bgcolor='#cecece' cellspacing='2' cellpadding='2' style='border: 1px solid black'>
  <form action="admin.php" method="post">
  <input type="hidden" name="do" value="save_editcat">
  <input type="hidden" name="id" value="<?=$id?>">
      <tr><td>New Name for Category:</td></tr>
      <tr><td><input type="text" name="changed_forumcat" class="option" size="35" value="<?=$r[name]?>"></td></tr>
      <tr><td>New Sort Order:</td></tr>
      <tr><td><input type="text" name="changed_sortcat" class="option" size="35" value="<?=$r[sort]?>"></td></tr>
      <input type="submit" class="button" value="Change"></td></tr>
      </form>
      </table>
<?php 
    end_frame();
}

#======================================================================#
# Word Censor Filter
#======================================================================#
if($act == "censor") {
modonly();
adminmenu();
//Output
if ($_POST['submit'] == 'Add Censor'){
$query = "INSERT INTO censor (word, censor) VALUES ('" . $_POST['word'] . "','" . $_POST['censor'] . "');";
             mysql_query($query);
             }
if ($_POST['action'] == 'Delete Censor'){
  $aquery = "DELETE FROM censor WHERE word = '" . $_POST['censor'] . "' LIMIT 1";
  mysql_query($aquery);
  }

begin_frame("Edit Censored Words", 'center');  
/*------------------
|HTML form for Word Censor
------------------*/
?>
<div align="center">
<table width='100%' cellspacing='3' cellpadding='3'>
<tr>
<td bgcolor='#eeeeee' colspan="2"><b><font face="Verdana" size="1">
Client Agent Ban Settings<br /></font><font size="1" face="Times New Roman">
</font></b><font size="1" face="Verdana">These settings control the Censored Words.<font></td>
</tr>
<form id="Add Censor" name="Add Censor" method="POST" action="./admin.php?act=censor">
<tr>
<td bgcolor='#eeeeee'><font face="Verdana" size="1">Add Word Censor:  <input type="text" name="word" id="word" size="50" maxlength="255" value=""></font></td></tr>
<tr><td bgcolor='#eeeeee'><font face="Verdana" size="1">Censor Word With:  <input type="text" name="censor" id="censor" size="50" maxlength="255" value=""></font></td></tr>
<tr><td bgcolor='#eeeeee' align='left'>
<font size="1" face="Verdana"><input type="submit" name="submit" value="Add Censor"></font></td>
</tr>
</form>

<form id="Delete Censor" name="Delete Censor" method="POST" action="./admin.php?act=censor">
<tr>
<td bgcolor='#eeeeee'><font face="Verdana" size="1">Remove Censor For: <select name="censor">
<?php
/*-------------
|Get the words currently censored
-------------*/
$select = "SELECT word FROM censor ORDER BY word";
$sres = mysql_query($select);
while ($srow = mysql_fetch_array($sres)) {
    echo "<option>" . $srow[0] . "</option>\n";
}
echo'</select></font></td></tr><tr><td bgcolor="#eeeeee" align="left">
<font size="1" face="Verdana"><input type="submit" name="action" value="Delete Censor"></font></td>
</tr></form></table><br>';
end_frame();
}
// End forum Censored Words

#======================================================================#
# Ratio Warn System - Watched Users
#======================================================================#
if($act == "rws-watched")
{
    $resrws = mysql_query("SELECT * FROM ratiowarn WHERE warned='no'");
	$reqrws = mysql_fetch_assoc($resrws);
	adminmenu();
	begin_frame("Ratio Warn System - Watched Users", 'center');
    if ($reqrws < 1){
        echo "There are no users currently being watched for poor ratios.";
    }else{
        $res_rws = mysql_query("SELECT *, TO_DAYS(NOW()) - TO_DAYS(ratiodate) as difference FROM ratiowarn WHERE warned='no'");
        $num = mysql_num_rows($res_rws);
        begin_table();
        print("<tr><td class=alt3 align=left><font size=1 face=Verdana color=white>User</td><td class=alt3 align=left><font size=1 face=Verdana color=white>Ratio</td><td class=alt3 align=left><font size=1 face=Verdana color=white>Warned</td><td class=alt3 align=left><font size=1 face=Verdana color=white>Time Until Warning</td></tr>\n");
        for ($i = 0; $i < $num; ++$i)
        {
            $arr = mysql_fetch_array($res_rws);
            $userid = $arr['userid'];
            $userr = mysql_query("SELECT username, uploaded, downloaded FROM users WHERE id='$userid'");
            $userq = mysql_fetch_assoc($userr);
            $user = $userq['username'];
			if ($userq["downloaded"] > 0) {
				$ratio = number_format($userq["uploaded"] / $userq["downloaded"], 2);
			}else{
				$ratio = "---";
			}
            
            $timeleft = ($arr['difference'] - $RATIOWARN_TIME)/-1;
            print("<tr><td class=alt1><a href=account-details.php?id=$userid>$user</a></td><td class=alt2>$ratio</td><td class=alt1 align=left>no</td><td class=alt2>$timeleft days</td></tr>\n");
        }
        end_table();
    }//display users with poor ratios

    end_frame();
}   //end rws-watched

#======================================================================#
# Ratio Warn System - Warned Users
#======================================================================#
if($act == "rws-warned")
{
    $resrws = mysql_query("SELECT * FROM ratiowarn WHERE warned='yes'");
	$reqrws = mysql_fetch_assoc($resrws);
	adminmenu();
	begin_frame("Ratio Warn System - Warned Users", 'center');
    if ($reqrws < 1){
        echo "No users have been warned for maintaining poor ratios.";
    }else{
        $res_rws = mysql_query("SELECT *, TO_DAYS(NOW()) - TO_DAYS(warntime) as difference FROM ratiowarn WHERE warned='yes'");
        $num = mysql_num_rows($res_rws);
        begin_table();
        print("<tr><td class=alt3 align=left><font size=1 face=Verdana color=white>User</td><td class=alt3 align=left><font size=1 face=Verdana color=white>Ratio</td><td class=alt3 align=left><font size=1 face=Verdana color=white>Warned</td><td class=alt3 align=left><font size=1 face=Verdana color=white>Banned</td><td class=alt3 align=left><font size=1 face=Verdana color=white>Time Until Ban</td></tr>\n");
        for ($i = 0; $i < $num; ++$i)
        {
            $arr = mysql_fetch_array($res_rws);
            $userid = $arr['userid'];
            $userr = mysql_query("SELECT username, uploaded, downloaded FROM users WHERE id='$userid'");
            $userq = mysql_fetch_assoc($userr);
            $user = $userq['username'];
			if ($userq["downloaded"] > 0) {
				$ratio = number_format($userq["uploaded"] / $userq["downloaded"], 2);
			}else{
				$ratio = "---";
			}
            $banned = $arr['banned'];
            
            if($banned == 'no'){
                $timeleft = ($arr['difference'] - $RATIOWARN_BAN)/-1;
            }else{
                $timeleft = "null";
            }
            print("<tr><td class=alt1><a href=account-details.php?id=$userid>$user</a></td><td class=alt2>$ratio</td><td class=alt1 align=left>yes</td><td class=alt1 align=left>$banned</td><td class=alt2>$timeleft days</td></tr>\n");
        }
        end_table();
    }//display warned users

    end_frame();
}   //end rws-warned
//end here
stdfoot();
hit_end();

