<?php

require_once __DIR__ . '/../../backend/functions.php';

dbconn(false);
loggedinorreturn();

jmodonly();
stdhead("Staff CP");
require_once '../../backend/admin-functions.php';

$act = $_REQUEST['act'] ?? '';
$_POST['submit'] = $_POST['submit'] ?? '';
// $do = $_POST['do'] ?? '';
$do = $_REQUEST['do'] ?? '';
$error_ac = '';
$_SERVER['php_self'] = $_SERVER['php_self'] ?? '';

$pagemenu = '';
$browsemenu = '';

if (empty($act)) {
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

echo '<br><center><a href="admin-reports-complete.php">View Completed Reports</a></center><br>';

echo $pagertop;

print("<table border=1 cellspacing=0 cellpadding=1 align=center width=95%>\n");
print("<tr><td class=table_head align=center>By</td><td class=table_head align=center>Reported</td><td class=table_head align=center>Type</td><td class=table_head align=center>Reason</td><td class=table_head align=center>Dealt With</td><td class=table_head align=center>Mark Dealt With</td>");

if (get_user_class() >= UC_MODERATOR)
    printf("<td class=table_head align=center>Delete</td>");

print("</tr>");
echo '<form method="post" action="report.php">';

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

    if ($arr['type'] === "forum") {
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
    Monthly Required:<br>
    <input type='text' value='<?= $row['requireddonations'] ?>' size='5' maxlength='5' name='ed_requireddonations'><br>
    Donations:<br>
    <input type='text' value='<?= $row['donations'] ?>' size='5' maxlength='5' name='ed_donations'><br>
    Donate Page Contents:<br>
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
if ($act === 'userdonations') {
    adminonly();
    adminmenu();
    begin_frame("View Donations", 'center');
    $res = DB::query('SELECT * FROM users WHERE donated > 0 ORDER BY username');

    print("<center><br><br><table border=1 width=95% cellspacing=0 cellpadding=1>
        <tr align=center><td class=table_head width=90>User Name</td>
        <td class=table_head width=70>Registered</td>
        <td class=table_head width=75>Last Access</td>  
        <td class=table_head width=75>User Class</td>
        <td class=table_head width=70>Downloaded</td>
        <td class=table_head width=70>Uploaded</td>
        <td class=table_head width=45>Ratio</td>
        <td class=table_head width=45>Donated</td>
        <td class=table_head width=225>Moderator Comments</td>
        </tr>\n"
    );
    while ($arr = $res->fetch()) {
        if ($arr['added'] === '0000-00-00 00:00:00')
            $arr['added'] = '-';
        if ($arr['last_access'] === '0000-00-00 00:00:00')
            $arr['last_access'] = '-';


        if ($arr["downloaded"] != 0) {
            $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
        } else {
            $ratio = "---";
        }
        $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
        $uploaded = mksize($arr["uploaded"]);
        $downloaded = mksize($arr["downloaded"]);

        $added = substr($arr['added'], 0, 10);
        $last_access = substr($arr['last_access'], 0, 10);
        $class = get_user_class_name($arr["class"]);

        print("<tr><td align=left><a href=account-details.php?id=$arr[id]><b>$arr[username]</b></a>"
            .($arr["donated"] > 1 ? "<img src=/images/star.gif border=0 alt='Donor'>" : "")."</td>
            <td align=center>$added</td>
            <td align=center>$last_access</td>
            <td align=center>$class</td>
            <td align=center>$downloaded</td>
            <td align=center>$uploaded</td>
            <td align=center>$ratio</td>
            <td align=center>$arr[donated]</td>
            <td align=center>$arr[modcomment]</td></tr>\n"
        );
    }

    print("</table>
    <p>$pagemenu<br>$browsemenu</p>");

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

    print("<center><br><br>
        <table border=1 width=95% cellspacing=0 cellpadding=1>
        <tr align=center><td class=table_head width=90>User Name</td>
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
if ($act == "warneddaccounts") {
    adminmenu();
    begin_frame("Warned Accounts", 'center');
    // todo: limit?
    $res = DB::query("SELECT * FROM users WHERE enabled = 'yes' AND warned = 'yes' ORDER BY username");

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
        $last_access = substr($arr['last_access'],0,10);
        $class = get_user_class_name($arr["class"]);

        print("<tr><td align=left><a href=account-details.php?id=$arr[id]><b>$arr[username]</b></a>"
            .($arr["donated"] > 1 ? "<img src=/images/star.gif border=0 alt='Donor'>" : "")."</td>
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
if($act === "database") {
    // optimize
    if($do === "opt") {
        DB::query("OPTIMIZE TABLE `guests`, `peers`, `torrents`, `files`, `log`, `messages`, `forum_posts`, `users`;");
        bark2("Success", "Database optimized OK");
    }
    // backup
    if ($do === "backup") {
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
        // print the news titles, with links to the edit page 
        $res = DB::query('select * from dbbackup ORDER BY id DESC'); 
        while ($backupr = $res->fetch()) { 
            extract($backupr); 
            echo("<TR><TD>$added</TD><td>$day</td><td>$name</TD></TR>"); 
        } 
        echo("<br><br></TABLE></center>"); 
        end_frame();
}

#======================================================================#
#    Site Texts Edit
#======================================================================#
if ($act === 'sitetexts') {
    // disclaimer
    if ($do === 'save_disclaimer') {
        $fp = fopen('disclaimer.txt', 'w');
        $written = fwrite($fp, $_POST['descr']);
        fclose($fp);
        if ($written) {
            bark2('Success', 'Disclaimer Updated OK');
        } else {
            bark2('Error', 'Disclaimer not updated');
        }
    }

    adminmenu();
    begin_frame('Disclaimer Text Management', 'center');
    echo "<br><br>
        <form action='admin.php' method='post'>
        <input type='hidden' name='sid' value='$sid'>
        <input type='hidden' name='act' value='sitetexts'>
        <input type='hidden' name='do' value='save_disclaimer'>
        <textarea wrap='on' name='descr' cols='100' rows='20'
        style='border:1px black solid; background:#eeeeee; font-family:verdana,arial; font-size: 12px; color:#000000;'>\n";
    include("disclaimer.txt");
    echo "</textarea>\n<p>
        <input style='background:#eeeeee' type='submit' value='   SAVE   '>
        <input style='background:#eeeeee' type='reset' value='  RESET   '>
        </form>\n";
    end_frame();
}
//news

#======================================================================#
#    Language Settings
#======================================================================#
if ($act === 'lang')
{
    // delete language
    $lid = (int) ($_POST['lid'] ?? 0);
    if ($do === 'del_lang' && $lid !== 1) {
        $dl = DB::executeUpdate('DELETE FROM languages WHERE id = ' . $lid);
        if ($dl)
            autolink('admin.php?act=lang', 'Language deleted ...');
        else
            die('<h2>mySQL-Error: Could not delete language-name (check connection & settings)</h2></body></html>');
    }

// add language
if ($do === 'add_lang')
// add to db & create autolink
{
    $uri = $_POST['uri'] ?? '';
    $name = $_POST['name'] ?? '';
    if (empty($uri) && empty($name)) {
        bark2('Error', 'Empty field uri or name');
    }
    $ok = DB::executeUpdate('INSERT INTO languages (`uri`, `name`) VALUES (?, ?)',
        [$uri, $name]
    );
    bark2('Success', 'New Language Added');
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
    // get lang's from db
    $res = DB::query("SELECT * FROM languages ORDER BY id");
    // show them

    echo "<p>\n<font size='2'>Available language-names (sorted by id):</font>\n<p>
        <table width='200' border='1' cellspacing='0'>
        <tr bgcolor='#cecece'>\n<td align='center'><b>ID</b></td>
        <td align='center'><b>Name</b></td>\n<td align='center'><b>Delete?</b></td></tr>\n";

    while ($row = $res->fetch()) {
        if ($row['id'] == 1) {
            echo "
            <tr bgcolor='#ffffff' align='center'>
            <td>$row[id]</td><td>$row[name]</td>
            <td align='center'>[ --- ]</td></tr>";
        } else {
            echo "
        <tr bgcolor='#eeeeee' align='center'>\n<td>".$row['id']."</td>
        <td>".$row['name']."</td>\n
        <td align='center'>[ <a href='admin.php?act=lang&do=del_lang&lid=".$row['id']."' title='delete this entry'>del</a> ]</td>\n</tr>\n";
        }
    }

    echo "</table>\n";
    end_frame();
}

#======================================================================#
#    Banner Options
#======================================================================#
if($act == "banner" && $do == "")
{
    adminonly();
    adminmenu();
    begin_frame("Top Banner Ads", 'center');
    echo "Use the box below to edit the contents of&nbsp; banners.txt and sponsors.txt to control which banners are displayed on your site.
    <br>Each banner entry must be separated with a '~'. To increase the display rate of a banner enter its data multiple times.
    <br>To disable the banners, simply remove all data from both areas.\n";
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

if($do == "save_banner") {
    $fp = fopen("banners.txt", "w");
    $css = stripslashes($css);
    $written = fwrite($fp,$css);
    fclose($fp);
    if($written) bark2("Success", "Banners Updated OK");

}
if ($do == "save_sponsor") {
    $fpa = fopen("sponsors.txt", "w");
    $cssa = stripslashes($cssa);
    $written = fwrite($fpa,$cssa);
    fclose($fpa);
    if ($written) {
        bark2("Success", "Sponsors Updated OK");
    } else {
        bark2("Error", "Sponsors Not Updated");
    }
}


#======================================================================#
#    Tracker (Settings) Settings
#======================================================================#
if ($act === 'settings') {
    require_once TT_CONTR_DIR . '/admin/admin-action-settings.php';
}

#======================================================================#
#    Tracker Log
#======================================================================#
if ($act === "view_log") {
    adminmenu();    // show menu
    // output

    /*
    if ($do === "del_log" && $arr['id'] != 1) {
        $dl = DB::query("DELETE FROM log WHERE id = $arr[id]");
        if ($dl)
            autolink("admin.php?act=view_log", "Entry deleted ...");
        else
            die("<h2>mySQL-Error: Could not delete language-name (check connection & settings)</h2></body></html>");
    }
    */

    // delete items older than a week - Should be a variable in db and should be changeable in AdminCP
    $secs = 14 * 24 * 60 * 60;
    DB::query("DELETE FROM log WHERE " . gmtime() . " - UNIX_TIMESTAMP(added) > $secs");
    $res = DB::fetchAll("SELECT added, txt, id FROM log ORDER BY added DESC");

    if (! $res) {
        begin_frame("Admin Log");
        print("<b><CENTER>The log is empty.</CENTER></b>
            <br><BR><CENTER>Items older than 14 days are Automatically removed.</CENTER>\n");
        end_frame();
    } else {
        begin_frame("Admin Log", 'justify');
        begin_table();
        print("<tr><td class=alt3 align=left><font size=1 face=Verdana color=white>Date</td>
            <td class=alt3 align=left><font size=1 face=Verdana color=white>Time</td>
            <td class=alt3 align=left><font size=1 face=Verdana color=white>Event</td>
            <!--<td class=alt3 align=left><font size=1 face=Verdana color=white>Delete Entry</td>--></tr>\n");
        foreach ($res as $arr) {
            $date = substr($arr['added'], 0, strpos($arr['added'], " "));
            $time = substr($arr['added'], strpos($arr['added'], " ") + 1);
            print("<tr><td class=alt1>$date</td>
                <td class=alt2>$time</td>
                <td class=alt1 align=left>$arr[txt]</td>
                <!--<td class=alt2><a href='admin.php?act=view_log&do=del_log&lid=$arr[id]' title='delete this entry'>delete</a></td>--></tr>\n");
        }
        end_table();
    }
    // end_frame();
}

#======================================================================#
# IRC ANNOUNE CONFIG
#======================================================================#
if($act === 'ircannounce') {
    adminonly();
    adminmenu();
    // output
    begin_frame('IRC Announce Config', 'left');
    echo 'IRC Annonce is <i>removed</i> from this version';
    end_frame();
}
    
    
#======================================================================#
#        News
#======================================================================#
if ($act === 'news') {
    require_once TT_CONTR_DIR . '/admin/admin-action-news.php';
}

#======================================================================#
#    Bans
#======================================================================#
if($act === 'bans')
    {
        adminmenu();    // show menu
        //output
$remove = $HTTP_GET_VARS['remove'];

if (is_valid_id($remove))
{
  mysql_query("DELETE FROM bans WHERE id=$remove") or sqlerr();
  write_log("Ban $remove was removed by $CURUSER[id] ($CURUSER[username])");
}

if ($do === 'add')
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
    bark2("Success", "The ban has been added!");
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
  print("<b>No Bans Found</b><br>\n");
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
  print("<p>$browsemenu<br><br>$pagemenu</p>");
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
#    User Settings
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
print("<br><form method=get action=?>\n");
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
for ($i = 0; $i < $num; ++$i) {
    $arr = mysql_fetch_assoc($res);
    if ($arr['country'] > 0) {
        $cres = mysql_query("SELECT name,flagpic FROM countries WHERE id=$arr[country]");
        if (mysql_num_rows($cres) == 1) {
            $carr = mysql_fetch_assoc($cres);
            $country = "<td align=\"center\"  class=alt1 style='padding: 0px' align='center'><img src="
                . $SITEURL ."/images/flag/$carr[flagpic] alt='$carr[name]' /></td>";
        }
    } else {
        $country = "<td align=\"center\"  class=alt1 style='padding: 0px' align='center'><img src="
            . $SITEURL ."/images/flag/unknown.gif alt=Unknown /></td>";
    }
    if ($arr['added'] == '0000-00-00 00:00:00')
        $arr['added'] = '-';
    if ($arr['last_access'] == '0000-00-00 00:00:00')
        $arr['last_access'] = '-';
    print("<tr><td align=\"center\"  class=alt1 align=left><a href=account-details.php?id=$arr[id]>"
        .($arr["class"] > 1 ? "<font color=#A83838>" : "")."<b>$arr[username]</b></font></a>"
        .($arr["donated"] > 0 ? "<img src=$SITEURL/images/star.gif border=0 alt='Donated $$arr[donated]'>" : "")."</td>"
        ."<td align=\"center\"  class=alt1 align=left><a href=admin.php?act=deluser&id=$arr[id]>Delete User</a></td>"
        ."<td align=\"center\"  class=alt2>$arr[added]</td><td align=\"center\"  class=alt1>$arr[last_access]</td>"
        ."<td align=\"center\"  class=alt2 align=left>" . get_user_class_name($arr["class"]) . "</td>$country</tr>\n");
}

end_table();
end_frame();

print("<p>$pagemenu<br>$browsemenu</p>");
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
#    Manual Conf Reg
#======================================================================#
if ($act == "confirmreg") {
    adminmenu(); 
    begin_frame("Info On This List", 'justify');
    ?>
    <p align="justify">This page shows all users that have not clicked the ACTIVATION link in the signup email, 
    they cannot access the site until they have clicked this link.  You should only manually confirm a user if 
    they request it (via email, irc or other method), where they have lost or not received the email. 
    All PENDING users will be cleaned from the system every so often.</p>
    <?php 
    end_frame();
    begin_frame("Manual Registration Confirm", 'center');
    begin_table();
    $perpage = 100;
    print("<tr><td align=\"center\"  class=alt3 align=left><font size=1 face=Verdana color=white>Username</td>
        <td align=\"center\" class=alt3><font size=1 face=Verdana color=white>Email Address</td>
        <td align=\"center\" class=alt3><font size=1 face=Verdana color=white>Date Registered</td>
        <td align=\"center\" class=alt3 align=left><font size=1 face=Verdana color=white>IP</td>
        <td align=\"center\" class=alt3><font size=1 face=Verdana color=white>Status</td></tr>\n");

    $res = DB::query("SELECT * FROM users WHERE status = 'pending' ORDER BY username");
    while ($row = $res->fetch()) {
        echo "<tr><td>$row[username]</td>
            <td>$row[email]</td>
            <td>$row[added]</td>
            <td>$row[ip]</td>
            <td><a href='admin.php?act=editreg&id=$row[id]'>$row[status]</a></td></tr>\n";
    }
    end_table();
    end_frame();
}

// SAVE THEME EDIT FUNCTION
if ($do == "save_editreg") {
    mysql_query("UPDATE users SET status = '$ed_status' WHERE id = $id");
    echo "<br><br><center><b>Updated Completed</b></center>";
}

// EDIT USER REG FORM
if ($act == "editreg" && $id != "") {
    $ee = DB::fetchAssoc("SELECT * FROM users WHERE id = $id");
    adminmenu();
    begin_frame();
    ?>

    <form action='admin.php' method='post'>
    <input type='hidden' name='sid' value='<?= $sid ?>'>
    <input type='hidden' name='id' value='<?= $id ?>'>
    <input type='hidden' name='do' value='save_editreg'>
    Name: <?= $ee['username'] ?><br>
    Surrent Status: <?=$ee['status'] ?><br>
    <select name='ed_status'>
        <option value='pending' <?= $status == "pending" ? "selected" : '' ?>>pending
        <option value='confirmed' <?= $status == "confirmed" ? "selected" : '' ?>>confirmed
        </select>
    <input type='submit' value='   Save   ' style='background:#eeeeee'>&nbsp;&nbsp;&nbsp;<input type='reset' value='  Reset  ' style='background:#eeeeee'>
    </form>
    <?php 
    end_frame();
}


// Torrent Management
if ($act === 'torrents') {
    adminmenu(); 
    begin_frame('TORRENT MANAGEMENT', 'center');

    $count = DB::fetchColumn('SELECT COUNT(*) FROM torrents LIMIT 1');
    if ($count) {
        $perpage = 250;
        [$pagertop, $pagerbottom, $limit] = pager($perpage, $count, $_SERVER['PHP_SELF'] . '&act=torrents?');

        echo $pagertop;

        ?>
        <table align=center cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#D6D9DB" width="100%" border="1">
        <tr>
        <td class=alt3 align=center><font size=1 face=Verdana color=white>Name</td>
        <td class=alt3 align=center><font size=1 face=Verdana color=white>Visible</td>
        <td class=alt3 align=center><font size=1 face=Verdana color=white>Banned</td>
        <td class=alt3 align=center><font size=1 face=Verdana color=white>Seeders</td>
        <td class=alt3 align=center><font size=1 face=Verdana color=white>Leechers</td>
        <td class=alt3 align=center><font size=1 face=Verdana color=white>Edit?</td>
        </tr>
        <?php

        $res = DB::query('SELECT * FROM torrents ORDER BY name ' . $limit);
        while ($row = $res->fetch()) {
            echo '<tr><td>' . $row['name'] . '</td>
            <td>' . $row['visible'] . '</td>
            <td>' . $row['banned'] . '</td>
            <td>' . $row['seeders'] . '</td>
            <td>' . $row['leechers'] . '</td>
            <td><a href="torrents-edit.php?returnto=' . urlencode($_SERVER['REQUEST_URI']) . '&id=' . $row['id']
                . '"><font size=1 face=Verdana>EDIT</a></td></tr>';
        }

        echo '</table>';
        echo $pagerbottom;
    }

    end_frame();
}


// Banned Torrents List
if ($act === 'bannedtorrents') {
    adminmenu(); 
    begin_frame('BANNED TORRENT MANAGEMENT', 'center');

    $count = DB::fetchColumn('SELECT COUNT(*) FROM torrents WHERE banned = \'yes\' LIMIT 1');
    if ($count) {
        $perpage = 250;
        [$pagertop, $pagerbottom, $limit] = pager($perpage, $count, $_SERVER['PHP_SELF'] . '&act=bannedtorrents?');

        echo $pagertop;

        ?>
        <table align=center cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#D6D9DB" width="100%" border="1">
        <tr>
        <td class=alt3 align=center><font size=1 face=Verdana color=white>Name</td>
        <td class=alt3 align=center><font size=1 face=Verdana color=white>Visible</td>
        <td class=alt3 align=center><font size=1 face=Verdana color=white>Banned</td>
        <td class=alt3 align=center><font size=1 face=Verdana color=white>Seeders</td>
        <td class=alt3 align=center><font size=1 face=Verdana color=white>Leechers</td>
        <td class=alt3 align=center><font size=1 face=Verdana color=white>Edit?</td>
        </tr>
        <?php

        $res = DB::query('SELECT * FROM torrents WHERE banned = \'yes\' ORDER BY name ' . $limit);
        while ($row = $res->fetch()) {
            echo '<tr><td>' . $row['name'] . '</td>
            <td>' . $row['visible'] . '</td>
            <td>' . $row['banned'] . '</td>
            <td>' . $row['seeders'] . '</td>
            <td>' . $row['leechers'] . '</td>
            <td><a href="torrents-edit.php?returnto=' . urlencode($_SERVER['REQUEST_URI']) . '&id=' . $row['id']
                . '"><font size=1 face=Verdana>EDIT</a></td></tr>';
        }

        echo '</table>';
        echo $pagerbottom;
    }

    end_frame();
}


// Message Spy
if ($act === 'msgspy') {
    adminonly();

    $url = $GLOBALS['SITEURL'] . '/admin.php?act=msgspy';

    // POST
    if (isset($_POST['delpm'])) {
        $numDone = 0;
        if (isset($_POST['delpm'])) {
            $_POST['delpm'] = array_map('intval', $_POST['delpm']);
            $do = 'DELETE FROM messages WHERE id IN (' . implode(', ', $_POST['delpm']) . ')';
            $numDone = DB::executeUpdate($do);
        }

        stdhead();
        begin_frame('Done');
        echo '<br><B><center>Deleted '.$numDone.' messages.<BR><BR><a href="'.$url.'">Back To Staff CP</a></center></b>';
        end_frame();
        stdfoot();
        die('');
    }

    // GET
    adminmenu();
    begin_frame("Messages Spy", 'center');
    // PAGER
    $count = DB::fetchColumn('SELECT COUNT(*) FROM messages');
    $perpage = 250;
    [$pagertop, $pagerbottom, $limit] = pager($perpage, $count, $url);
    echo $pagertop;

    $res = DB::query('
        SELECT m.*, u.username AS recusername, u2.username AS senusername
        FROM messages AS m
            LEFT JOIN users AS u ON u.id = m.receiver
            LEFT JOIN users AS u2 ON u2.id = m.sender
        ORDER BY id DESC
        ' . $limit);
    echo '<table border=1 cellspacing=0 cellpadding=3>';

    ?>
    <form method="post" action="<?= $url ?>" id="tt-msg-deleted">
        <tr><td class=table_head align=left>Sender</td>
        <td class=table_head align=left>Receiver</td>
        <td class=table_head align=left>Text</td>
        <td class=table_head align=left>Date</td>
        <td class=table_head>Delete</td></tr>
    <?php
    while ($arr = $res->fetch()) {
        $receiver = "<a href=account-details.php?id=" . $arr["receiver"] . "><b>" . $arr["recusername"] . "</b></a>";
        $sender = "<a href=account-details.php?id=" . $arr["sender"] . "><b>" . $arr["senusername"] . "</b></a>";
        if (! $arr["sender"])
            $sender = "<font color=red><b>System</b></font>";
        $msg = format_comment($arr["msg"]);
        $added = format_comment($arr["added"]);
        print("<tr><td align=left class=table_col1>$sender</td>
        <td align=left class=table_col2>$receiver</td>
        <td align=left class=table_col1>$msg</td>
        <td align=left class=table_col2>$added</td>");

        echo '<td class=table_col1><input type="checkbox" name="delpm[]" value="' . $arr['id'] . '"></td></tr>';
    }
    echo '</table>';
    ?><BR>
    <input type="submit" value="Delete!" />
    </form>

    <script>
    function tt_del_msg()
    {
        var checkboxes = document.getElementsByName('delpm[]');
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = !checkboxes[i].checked;
        }
    }
    </script>
    <a href="#" onclick="tt_del_msg(); return false;">Select All</a>

    <?php 
    echo $pagerbottom;

    end_frame();
}


#======================================================================#
#    Manual Added Banned Torrents List
#======================================================================#
if ($act == "bannedtorrentsmanual") {
    adminmenu(); 
    begin_frame("BANNED TORRENT MANUAL ADDED LIST", 'center');
    ?>
AHA not yet matey...

<?php 
    end_frame();
}

#======================================================================#
#    Theme Settings
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
    $error_ac = "";
    if ($new_theme_name == "")
        $error_ac .= "<br><br>Theme Name was empty";
    if ($new_uri == "")
        $error_ac .= "<br><br>Folder Name was empty";

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
    if ($error_ac != "") {
        echo "<tr><td colspan='2' align='center' style='background:#eeeeee;border:2px red solid'><b>COULD NOT ADD NEW THEME:</b>
            <br>$error_ac</tr></td>\n";
    }
    ?>
    </table>
        <br>Please note: All themes must be uploaded to the /themes/ folder.  Please make sure all folder names are EXACT.
    <?php 
        end_frame();
}

if($do == "save_edtheme")
// SAVE THEME EDIT FUNCTION
{
    if ($ed_name != "" && $ed_uri != "") {
        mysql_query(" UPDATE stylesheets SET name='$ed_name' ,uri='$ed_uri' WHERE id=$id");
        bark2("Success", "Theme Updated");
    } else {
        bark2("ERROR", "Please fill out all fields");
    }
}

if($do == "edtheme" && $id != "")
// EDIT THEME FORM
{
    begin_frame();
    $q = MYSQL_QUERY("SELECT * FROM stylesheets WHERE id = $id");
    $r = MYSQL_FETCH_ARRAY($q);

    ?>
    <form action='admin.php' method='post'>
    <input type='hidden' name='sid' value='<?= $sid ?>'>
    <input type='hidden' name='id' value='<?= $id ?>'>
    <input type='hidden' name='do' value='save_edtheme'>
    Name:<br>
    <input type='text' value='<?=$r['name']?>' size='30' maxlength='30' name='ed_name'><br>
    Folder Name (case SenSiTive):<br>
    <input type='text' value='<?=$r['uri']?>' size='30' maxlength='30' name='ed_uri'><br>
    <input type='submit' value='   Save   ' style='background:#eeeeee'>&nbsp;&nbsp;&nbsp;<input type='reset' value='  Reset  ' style='background:#eeeeee'>
    </form>
    <br>Please note: All themes must be uploaded to the /themes/ folder.  Please make sure all folder names are EXACT.
    <?php 
        end_frame();
}

if ($do == "del_this_theme")
//DELETE THEME FROM DATABASE FUNCTION
{
    $error_ac = "";
    if ($ed_id == "")
        $error_ac .= "<br><br>Theme ID was empty\n";

    if ($error_ac == "") {
        DB::query("DELETE FROM stylesheets WHERE id = $ed_id");
        bark2("Success", "Theme Deleted OK");
    } else {
        bark2("ERROR", "Please fill out all fields");
    }
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
    Enter Theme ID to Delete:<br>
    <input type='text' value='' size='30' maxlength='30' name='ed_id'><br>
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

if ($do == "add_this_forum") {
    $error_ac = "";
    $new_forum_name = $_POST['new_forum_name'] ?? '';
    if ($new_forum_name == "")
        $error_ac .= "<li>Forum-name was empty\n";

    $new_desc = $_POST['new_desc'] ?? '';
    if ($new_desc == "")
        $error_ac .= "<li>Forum-description was empty\n";

    $new_forum_sort = $_POST['new_forum_sort'] ?? '';
    if ($new_forum_sort == "")
        $error_ac .= "<li>Forum sort order was empty\n";

    $new_forum_cat = $_POST['new_forum_cat'] ?? '';
    if ($new_forum_cat == "")
        $error_ac .= "<li>Forum category was empty\n";

    $minclassread = $_POST['minclassread'] ?? '';
    $minclasswrite = $_POST['minclasswrite'] ?? '';

    if ($error_ac == "") {
        $ok = DB::executeUpdate('INSERT INTO forum_forums
            (`name`, `description`, `sort`, `category`, `minclassread`, `minclasswrite`)
            VALUES (?, ?, ?, ?, ?, ?)',
            [$new_forum_name, $new_desc, $new_forum_sort, $new_forum_cat, $minclassread, $minclasswrite]
        );
        if ($ok) {
            autolink("admin.php?act=forum", "Thank you, new forum added to db ...");
        } else {
            echo "<h4>Could not save to DB - check your connection & settings!</h4>";
        }
    }
}

if ($do == "add_this_forumcat") {
    $error_ac = "";
    $new_forumcat_name = $_POST['new_forumcat_name'] ?? '';
    $new_forumcat_sort = $_POST['new_forumcat_sort'] ?? '';
    if ($new_forumcat_name == "")
        $error_ac .= "<li>Forum cat name was empty\n";
    if ($new_forumcat_sort == "")
        $error_ac .= "<li>Forum cat sort order was empty\n";

    if ($error_ac == "") {
        $ok = DB::executeUpdate('INSERT INTO forumcats (`name`, `sort`) VALUES (?, ?)',
            [$new_forumcat_name, $new_forumcat_sort]
        );
        if ($ok)
            autolink("admin.php?act=forum", "Thank you, new forum cat added to db ...");
        else
            echo "<h4>Could not save to DB - check your connection & settings!</h4>";
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
if ($do == "delete_forum") {
    if ($delcat != "") {
        $sql2 = "DELETE FROM forum_forums WHERE id = '$id'";
        $ok2 = DB::executeUpdate($sql2);
        if ($ok2)
            autolink("admin.php?act=forum", "forum deleted ...");
    }
}

// finally delete forumcat
if ($do == 'delete_forumcat') {
    if ($delcat != '') {
        $sql2 = 'DELETE FROM forumcats WHERE id = '.$id;
        $ok2 = DB::executeUpdate($sql2);
        if ($ok2)
            autolink('admin.php?act=forum', 'forum cat deleted ...');
    }
}

if ($act == 'forum' || $error_ac != '') {
    // form to add forum
    adminonly();
    adminmenu();
    begin_frame('Forums Management');
    $res = DB::query('SELECT * FROM forumcats ORDER BY sort, name');
    $allcat = 0;
    while($row = $res->fetch()) {
        $allcat += 1;
        $forumcat[] = $row;
    }

?>
<p>
<table align='center' width='80%' bgcolor='#cecece' cellspacing='2' cellpadding='2' style='border: 1px solid black'>
<form action='admin.php' method='post'>
<input type='hidden' name='sid' value='<?= $sid ?>'>
<input type='hidden' name='act' value='sql'>
<input type='hidden' name='do' value='add_this_forum'>
<tr>
<td>Name of the new Forum:</td>
<td align='right'><input type='text' name='new_forum_name' size='30' maxlength='30'  value='<?= $_POST['new_forum_name'] ?? '' ?>'></td>
</tr>
<tr>
<td>Forum Sort Order:</td>
<td align='right'><input type='text' name='new_forum_sort' size='10' maxlength='10'  value='<?= $_POST['new_forum_sort'] ?? '' ?>'></td>
</tr>
<tr>
<td>Description of the new Forum:</td>
<td align='right'><textarea cols='50' rows='5' name='new_desc'><?= $_POST['new_desc'] ?? '' ?></textarea></td>
</tr>
<tr>
<td>Forum Category:</td>
<td align='right'><select name='new_forum_cat'>
<?php 
foreach ($forumcat as $row) {
    echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
}
?>
</select>
</tr>
<tr><td>Mininum Class Needed to Read:</td>
<td align='right'><select name='minclassread'>
<option value='<?=UC_USER?>'>User</option>
<option value='<?=UC_UPLOADER?>'>Uploader</option>
<option value='<?=UC_VIP?>'>VIP</option>
<option value='<?=UC_JMODERATOR?>'>Moderator</option>
<option value='<?=UC_MODERATOR?>'>Super Moderator</option>
<option value='<?=UC_ADMINISTRATOR?>'>Administrator</option>
</select></td></tr>
<tr><td>Mininum Class Needed to Post:</td>
<td align='right'><select name='minclasswrite'>
<option value='<?=UC_USER?>'>User</option>
<option value='<?=UC_UPLOADER?>'>Uploader</option>
<option value='<?=UC_VIP?>'>VIP</option>
<option value='<?=UC_JMODERATOR?>'>Moderator</option>
<option value='<?=UC_MODERATOR?>'>Super Moderator</option>
<option value='<?=UC_ADMINISTRATOR?>'>Administrator</option>
</select></td></tr>
    
    <tr>
<td colspan='2' align='center'>
<input type='submit' value='Add new forum'>
<input type='reset' value='Reset'>
</td>
</tr>
<?php 
if ($error_ac != "") {
    echo "<tr><td colspan='2' align='center' style='background:#eeeeee;border:2px red solid'><b>COULD  NOT ADD NEW forum:</b>
        <br>$error_ac</tr></td>\n";
}
?>
</table>
</form>
<p>
<table align='center' width='80%' bgcolor='#cecece' cellspacing='2' cellpadding='2' style='border: 1px solid black'>
<h5>Current Forums:</h5>
<?php 
// get forum from db
echo "<tr><td width='60'><font size='2'><b>ID</b></td>
    <td width='120'>NAME</td>
    <td  width='250'>DESC</td>
    <td width='45'>SORT</td>
    <td width='45'>CATEGORY</td>
    <td width='18'>EDIT</td>
    <td width='18'>DEL</td></font>\n";

$res = DB::fetchAll("SELECT * FROM forum_forums ORDER BY sort, name");

if (! $res) {
    echo "<h4>None</h4>\n";
} else {
    foreach ($res as $row) {
        foreach ($forumcat as $cat)
            if ($cat['id'] == $row['category'])
                $category = $cat['name'];

        echo "<tr><td width='60'><font size='2'><b>ID($row[id])</b></td>
            <td width='120'> $row[name]</td>
            <td  width='250'>$row[description]</td>
            <td width='45'>$row[sort]</td>
            <td width='45'>$category</td></font>
            <td width='18'><a href='admin.php?do=edit_forum&id=$row[id]'>[Edit]</a></td>
            <td width='18'><a href='admin.php?do=del_forum&id=$row[id]'>
            <img src='images/delete.gif' alt='Delete  Category' width='17' height='17' border='0'></a>
            </td></tr>\n";
    }
} //endif
echo "</table>\n";

?>
<BR><table align='center' width='80%' bgcolor='#cecece' cellspacing='2' cellpadding='2' style='border: 1px solid black'>
<h5>Current Forum Categories:</h5>
<?php 
// get forum from db
echo "<tr><td width='60'><font size='2'><b>ID</b></td>
    <td width='120'>NAME</td><td  width='18'>SORT</td>
    <td width='18'>EDIT</td><td width='18'>DEL</td></font>\n";

if($allcat == 0) {
    echo "<h4>None set</h4>\n";
} else {
    foreach ($forumcat as $row) {
        echo "<tr><td width='60'><font size='2'><b>ID($row[id])</b></td>
            <td width='120'> $row[name]</td>
            <td width='18'>$row[sort]</td>
            <td width='18'><a href='admin.php?do=edit_forumcat&id=$row[id]'>[Edit]</a></td>
            <td width='18'><a href='admin.php?do=del_forumcat&id=$row[id]'>
                <img src='images/delete.gif' alt='Delete  Category' width='17' height='17' border='0'></a>
            </td></tr>\n";
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
<td align='right'><input type='text' name='new_forumcat_name' size='30' maxlength='30'  value='<?= $_POST['new_forumcat_name'] ?? '' ?>'></td>
</tr>
<tr>
<td>Category Sort Order:</td>
<td align='right'><input type='text' name='new_forumcat_sort' size='10' maxlength='10'  value='<?= $_POST['new_forumcat_sort'] ?? '' ?>'></td>
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
if ($do == "edit_forum") {
    begin_frame("Edit Forum");
    $id = (int) ($_GET['id'] ?? 0);
    $r = DB::fetchAssoc('SELECT * FROM forum_forums WHERE id = '.$id);
?>
    <table align='center' width='80%' bgcolor='#cecece' cellspacing='2' cellpadding='2' style='border: 1px solid black'>
    <form action="admin.php" method="post">
    <input type="hidden" name="do" value="save_edit">
    <input type="hidden" name="id" value="<?=$id?>">
    <tr><td>New Name for Forum:</td>
    <td align='right'><input type="text" name="changed_forum" class="option" size="35" value="<?=$r['name']?>"></td></tr>
    <tr><td>New Sort Order:</td>
    <td align='right'><input type="text" name="changed_sort" class="option" size="35" value="<?=$r['sort']?>"></td></tr>
    <tr><td>Description:</td>
    <td align='right'><textarea cols='50' rows='5' name='changed_forum_desc'><?=$r['description']?></textarea></td></tr>
    <tr><td>New Category:</td>
    <td align='right'><select name='changed_forum_cat'>
<?php 
$query = DB::query("SELECT * FROM forumcats ORDER BY sort, name");
while ($row = $query->fetch()) {
    echo "<option value={$row['id']}>{$row['name']}</option>";
}
?>
</select></td></tr>
<tr><td>Mininum Class Needed to Read:</td>
<td align='right'><select name='minclassread'>
<option value='<?=UC_USER?>' <?=$r['minclassread']==UC_USER?'selected':''?>>User</option>
<option value='<?=UC_UPLOADER?> <?=$r['minclassread']==UC_UPLOADER?'selected':''?>'>Uploader</option>
<option value='<?=UC_VIP?>' <?=$r['minclassread']==UC_VIP?'selected':''?>>VIP</option>
<option value='<?=UC_JMODERATOR?>' <?=$r['minclassread']==UC_JMODERATOR?'selected':''?>>Moderator</option>
<option value='<?=UC_MODERATOR?>' <?=$r['minclassread']==UC_MODERATOR?'selected':''?>>Super Moderator</option>
<option value='<?=UC_ADMINISTRATOR?>' <?=$r['minclassread']==UC_ADMINISTRATOR?'selected':''?>>Administrator</option>
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
if ($do == "del_forum") {
    begin_frame("Confirm");
    $id = (int) ($_GET['id'] ?? 0);
    $v = DB::fetchAssoc('SELECT * FROM forum_forums WHERE id = '.$id);
?>
    <form action="admin.php" method="post">
    <input type="hidden" name="do" value="delete_forum">
    <input type="hidden" name="id" value="<?= $id ?>">
    Really delete the Forum <?="<b>$v[name] with ID$v[id] ???</b>"?>
    <input type="submit" name="delcat" class="button" value="Delete">
    </form>
<?php 
    end_frame();
}

// del Forum
if ($do == "del_forumcat") {
    begin_frame("Confirm");
    $id = (int) ($_GET['id'] ?? 0);
    $v = DB::fetchAssoc('SELECT * FROM forumcats WHERE id = '.$id);
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

// edit forum
if ($do == "edit_forumcat") {
    begin_frame("Edit Category");
    $id = (int) ($_GET['id'] ?? 0);
    $r = DB::fetchAssoc('SELECT * FROM forumcats WHERE id = '.$id);
?>
    <table align='center' width='80%' bgcolor='#cecece' cellspacing='2' cellpadding='2' style='border: 1px solid black'>
    <form action="admin.php" method="post">
    <input type="hidden" name="do" value="save_editcat">
    <input type="hidden" name="id" value="<?=$id?>">
    <tr><td>New Name for Category:</td></tr>
    <tr><td><input type="text" name="changed_forumcat" class="option" size="35" value="<?=$r['name']?>"></td></tr>
    <tr><td>New Sort Order:</td></tr>
    <tr><td><input type="text" name="changed_sortcat" class="option" size="35" value="<?=$r['sort']?>"></td></tr>
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
Client Agent Ban Settings<br></font><font size="1" face="Times New Roman">
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
$res = DB::query($select);
while ($srow = $res->fetch()) {
    echo "<option>" . $srow['word'] . "</option>\n";
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
if ($act == "rws-watched") {
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

