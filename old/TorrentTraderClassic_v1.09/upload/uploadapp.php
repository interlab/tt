<?php 

require_once("backend/functions.php");
dbconn(false);
loggedinorreturn();
stdhead("Upload Application");
require_once("backend/admin-functions.php");

?>
<STYLE>
.popup
{
CURSOR: help;
TEXT-DECORATION: none
}
</STYLE><?php 

if ($_POST["form"]=="") {
 if($CURUSER["class"]<UC_MODERATOR)
   $CURUSER["uploadpos"] == 'no'?0:2;
else
  $form=10;
} else
$form=$_POST["form"];
if($form==0) {
 $res=mysql_query("SELECT * FROM uploadapp WHERE userid=".$CURUSER["id"]) or sqlerr(__FILE__, __LINE__);
 if(mysql_num_rows($res)) {
  $row=mysql_fetch_array($res);
  $form=4;
 }
}

$debug=0;
$upreq = 1;
$upreqn = $upreq * 1073741824;


if($debug) {
begin_frame("Debug Box");
print("<table>");
 print("<form action=\"uploadapp.php\" method=\"post\" enctype=\"multipart/form-data\" name=\"debug\" id=\"uploadapp\">");
tr("User Class","&nbsp;&nbsp;".get_user_class_name($CURUSER["class"]),1);
tr("Variables",
 "form = " . $form. "<br>".
 "user = " . $_POST["user"]. "<br>".
 "groupacct = " . $_POST["groupacct"]. "<br>".
 "grouname = " . unesc($_POST["groupname"]). "<br>".
 "groupdes = " . unesc($_POST["groupdes"]). "<br>".
 "joined = " . unesc($_POST["joined"]). "<br>".
 "ratio = " . unesc($_POST["ratio"]). "<br>".
 "upk =". unesc($_POST["upk"]). "<br>".
 "rbseed = ".unesc($_POST["rbseed"]).
  "<br>rbrelease = ".unesc($_POST["rbrelease"]).
  "<br>rbstime = ".unesc($_POST["rbstime"]) . "<br>".
 "plans =". unesc($_POST["plans"]). "<br>".
 "comment =". unesc($_POST["comment"]). "<br>".
 "",1);
 tr("View forms","<input type=\"radio\" name=\"form\" value=\"0\" ". ($form==0?"checked":""). ">Upload App".
 "<input name=\"form\" type=\"radio\" value=\"3\" ".($form==3?"checked":"").">Moderator+ Page".
 "<input type=\"submit\" name=\"SubmitD\" value=\"Change Forms\">",1);
 print("</table> </form>");

end_frame();
}
if($form>=10 && $CURUSER["class"]<UC_MODERATOR) {
begin_frame("Invalid Request");
end_frame();
} else if($form==0) {
begin_frame("Uploaders Application");
?>
<Center><BR><BR><b>Please use the application form below to apply for the right to UPLOAD torrents to this tracker<br>once you have submitted please await our staff vote.<br><BR>You will receive a PM once the voting has completed.<BR><BR></b></center>
 <form action="uploadapp.php" method="post" enctype="multipart/form-data" name="uploadapp" id="uploadapp">
 <center><table border=1 style='border-collapse: collapse' bordercolor=#646262 cellpadding=4>
<?php 

if ($CURUSER["downloaded"] > 0)
$ratio = $CURUSER['uploaded'] / $CURUSER['downloaded'];
else if ($CURUSER["uploaded"] > 0)
$ratio = 1;
else
$ratio = 0;

tr("User","&nbsp;&nbsp;<input name=\"user\" type=\"hidden\" value=\"". $CURUSER['id']."\">".$CURUSER['username'],1);
//tr("Is this a Group Account?","<input type=\"radio\" name=\"groupacct\" value=\"1\">Yes".
// "<input name=\groupacct\" type=\"radio\" value=\"0\" checked>No",1);
//tr("","<h2><center>For Group Applications only</center></h2>",1);
//tr("Group Name","<input name=\"groupname\" type=\"text\" id=\"groupname\" size=\"50\" maxlength=\"50\">",1);
//tr("Group ID<br>(3 char designator)","<input name=\"groupdes\" type=\"text\" id=\"groupdes\" size=\"7\" maxlength=\"3\">",1);
//tr("","<h2><center>For All Applicants</center></h2>",1);
tr("Joined Date","&nbsp;&nbsp;<input name=\"joined\" type=\"hidden\" value=\"".$CURUSER['added']."\">".$CURUSER['added'],1);
tr("My Ratio is at or above 1.0","&nbsp;&nbsp;<input name=\"ratio\" type=\"hidden\" value=\"".($ratio>=1?"ok":"not ok")."\">".($ratio>=1?"Yes":"No"),1);
$upreqm=$CURUSER['uploaded']>=$upreqn;
tr("I meet or exceed ". $upreq ." gb uploaded transfer","&nbsp;&nbsp;<input name=\"upk\" type=\"hidden\" value=\"".($upreqm?"yes":"no")."\">".($upreqm?"Yes":"No"),1);
tr("Content I plan on uploading<br>(not restricted to)","<textarea name=\"plans\" cols=\"50\" rows=\"2\" wrap=\"VIRTUAL\"></textarea>",1);
tr("Why I should be given upload access","<textarea name=\"comment\" cols=\"50\" rows=\"4\" wrap=\"VIRTUAL\"></textarea>",1);
?>
</table></center>
<p>I know how to seed (including the creation of torrent files) torrents?<br>
<input type="radio" name="rbseed" value="1">
 Yes<br>
 <input name="rbseed" type="radio" value="0" checked>
 No</p>        
<p>I understand that I am not allowed to upload banned releases, or other group releases that are officially not allowed on this tracker.<br>
<input type="radio" name="rbrelease" value="1">
 Yes<br>
 <input name="rbrelease" type="radio" value="0" checked>
 No</p>
<p>I understand that I am to seed torrents for at least 24 hours, or at least two other leechers have become seeders.<br>
<input type="radio" name="rbstime" value="1">
Yes<br>
<input name="rbstime" type="radio" value="0" checked>
No</p>
<br>
<input name="form" type="hidden" value="1">
<center><input type="submit" name="Submit" value="Send Application"><center>
</form><br><BR>
<?php 
end_frame();

} else if ($form==1) {

begin_frame("Uploaders Application Request");
$qry="INSERT INTO uploadapp (userid,applied,grpacct,grpname,grpdes,content,comment,seeding,othergrps,seedtime) ".
 "VALUES (". $_POST["user"].", ".
  implode(",",array_map("sqlesc", array(
    get_date_time(),
   $_POST["groupacct"],
   $_POST["groupname"],
   $_POST["groupdes"],
   $_POST["plans"],
   $_POST["comment"],
    $_POST["rbseed"],
   $_POST["rbrelease"],
   $_POST["rbstime"]))).")";
$ret=mysql_query($qry);
if (!$ret) {
  if (mysql_errno() == 1062)
   print("Application already on file<br>");
  else
  print("mysql puked: ".mysql_error());
} else
 print("<center><h2>Your application has been successfully sent to the review board</h2><br><BR>You can check back on your voting progress at any time by following this link again.<br></center>");
 

end_frame();
} else if($form==2) {
begin_frame("Uploaders Application Request");
print("<h2>You already have upload capabilities</h2>");
end_frame();
} else if($form==4) {
begin_frame("Your Application Request");
  $votesyes=$votesno==0;
   if($row["votes"]!="") {
    $votes=explode(" ",$row["votes"]);
    for($i=0;$i<count($votes);$i++)
    {
     $votei=explode(":",$votes[$i]);
     $votei[1]?$votesyes++:$votesno++;
    }
   }
   print("Upload Application Is Still Under Review:<br><BR><b>Current Voting status:</b> Yes = ".$votesyes." &nbsp;&nbsp; No = ".$votesno);
   print("<br>The Polls are ".($row["active"]=="0"?"Closed":"Open").".");
end_frame();
} else if($form>=10) {
begin_frame("Uploaders Voting Booth");
adminmenu();
if($form==11) {
 $res=mysql_query("SELECT * FROM uploadapp WHERE id=".$_POST["pollid"]) or sqlerr(__FILE__, __LINE__);
 $row=mysql_fetch_array($res);
  $votesyes=$votesno=$voted=0;
   if($row["votes"]!="") {
    $votes=explode(" ",$row["votes"]);
    for($i=0;$i<count($votes);$i++)
    {
     $votei=explode(":",$votes[$i]);
     if($CURUSER["id"]==$votei[0]) $voted++;
     $votei[1]?$votesyes++:$votesno++;
    }
   }
 if($_POST["ballet"] && $voted==0) {
  $votes=($row["votes"]!=""?$row["votes"]." ":"").implode(":",array($CURUSER["id"],$_POST["ballet"]=="Yes"?1:0));
  mysql_query("UPDATE uploadapp SET votes='".$votes."' WHERE id=".$_POST["pollid"]);
  print("Vote for ".$_POST["pollid"]." recieved (".$_POST["ballet"].")<br>");
 } else if($_POST["closepoll"]) {
   print("Request to close Polling for ".$_POST["pollid"]." recieved<br>");
   if(count($votes)<5) {
     print("Request denied, requires 5 votes to close");
   } else {
    mysql_query("UPDATE uploadapp SET active='0' WHERE id=".$_POST["pollid"]);
    $tvotes=$votesyes+$votesno;
    $votea=$votesyes>$votesno;
    $modcomment = gmdate("Y-m-d") . " - Upload Application: ".($votea?"Accepted":"Denied")." (Yes = ".$votesyes." No = ".$votesno." (".
     number_format((($votea?$votesyes:$votesno)/$tvotes)*100,3)."%)";
    print($modcomment."<br>");
    if($votea) {
     $mq="UPDATE users SET uploadpos='yes',class='".UC_UPLOADER."',modcomment=CONCAT(modcomment,".sqlesc($modcomment."\n").") WHERE id=".$row["userid"];
     mysql_query($mq);
     print("Updating User Records...<br>");
    $dt = sqlesc(get_date_time());
    $msg = sqlesc("Congrats, You have been accepted as a new Uploader!.\n");
    mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, ".$row["userid"].", $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__);
    } else {
     $mq="UPDATE users SET modcomment=CONCAT(modcomment,".sqlesc($modcomment."\n").") WHERE id=".$row["userid"];
     mysql_query($mq);
    $dt = sqlesc(get_date_time());
    $msg = sqlesc("sorry, You have been denied as a new Uploader.\n");
    mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, ".$row["userid"].", $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__);
    }
  }
 } else if($_POST["removepoll"]) {
  mysql_query("DELETE FROM uploadapp where id=".$_POST["pollid"]);
  print("Poll ".$_POST["pollid"]." removed from database");
 } else if($_POST["addcomment"]) {
  print ("Comment: ".sqlesc($_POST["newcomments"])."<br>");
   if(($_POST["newcomments"])) {
    $un=sqlesc($CURUSER["username"].": ".$_POST["newcomments"]."\n");
   $mq="UPDATE uploadapp SET modcomments=CONCAT(modcomments,".$un.") WHERE id=".$_POST["pollid"];
   mysql_query($mq);
   print("Added coment to poll ".$_POST["pollid"]);
  }
 }
   
}
print("<h1>Recent Uploader Applications</h1>");
$res=mysql_query("SELECT * FROM uploadapp ORDER BY applied DESC") or sqlerr(__FILE__, __LINE__);
if(!mysql_num_rows($res)) print("<h1>None");
else {
?>
 <table border=1 style='border-collapse: collapse' bordercolor=#646262>
 <tr><td>Poll #</td>
 <td>Username</td>
 <td><span title=" Application Date &amp; Join Date " class="popup">App date</span></td>
 <td><span title=" Group Affiliated Application " class="popup">Group</span></td>
 <td><span title=" Purposed Content to deliver" class="popup">Content</span></td>
 <td><span title=" Comments Left to us ops to sway us to vote yes" class="popup">Comments</span></td>
 <td><span title=" Does User Pass Ratio Requirement?" class="popup">Ratio</span></td>
 <td><span title=" Does User Pass Upload Transmission Requirement?" class="popup"><?=$upreq?>gb+</span></td>
 <td><span title=" Does User know how to seed torrents?" class="popup">Seeding</span></td>
 <td><span title=" Does User acknowledge other groups right to only upload their titles?" class="popup">Groups</span></td>
 <td><span title=" Does User acknowledge minimal seeding times?" class=popup">Seeder<br>Time</span></td>
 <td>Voting Poll</td>
 </tr>
<?php 
 while($row=mysql_fetch_array($res))
 {
   $resu=mysql_query("SELECT * FROM users where id = ".$row["userid"])  or sqlerr(__FILE__, __LINE__);
   $rowu=mysql_fetch_array($resu);
   $voted=$tvotes=$votesyes=$votesno=0;
   if($row["votes"]!="") {
    $votes=explode(" ",$row["votes"]);
    for($i=0;$i<count($votes);$i++)
    {
     $votei=explode(":",$votes[$i]);
     if($CURUSER["id"]==$votei[0]) $voted++;
     $votei[1]?$votesyes++:$votesno++;
     $tvotes++;
    }
   }
 if ($rowu["downloaded"] > 0)
  $ratio = $rowu['uploaded'] / $rowu['downloaded'];
 else if ($rowu["uploaded"] > 0)
  $ratio = 1;
 else
  $ratio = 0;
?>    
 <tr>
   <form action="uploadapp.php" method="post" enctype="multipart/form-data" name="poll<?=$row["id"]?>" id="uploadapp">
   <input name="form" type="hidden" value="11">
 <input name="pollid" type="hidden" value="<?=$row["id"]?>">
 <td><?=$row["id"]?></td>
 <td><a href=account-details.php?id=<?=$row["userid"]?>><?=$rowu["username"]?></a></td>
 <td><?=$row["applied"]?></td>
 <td <?=($row["grpacct"]?"bgcolor=\"#FFFF00\">(".unesc($row["grpdes"]).")&nbsp;".unesc($row["grpname"]):">N/A")?></td>
 <td><?=$row["content"]?></td>
 <td><?=$row["comment"]?></td>
 <td bgcolor="<?=($ratio>=1?"#00FF00":"#FF0000")?>"></td>
 <td bgcolor="<?=($rowu["uploaded"]>=$upreqn?"#00FF00":"#FF0000")?>"></td>
 <td bgcolor="<?=($row["seeding"]?"#00FF00":"#FF0000")?>"></td>
 <td bgcolor="<?=($row["othergrps"]?"#00FF00":"#FF0000")?>"></td>
  <td bgcolor="<?=($row["seedtime"]?"#00FF00":"#FF0000")?>"></td>
  <td rowspan="2"><?=($voted||!$row["active"]?$votesyes." Yes<br>".$votesno." No"
  :"Votes: ".$tvotes."<br>".
  "<input name=\"ballet\" type=\"submit\" value=\"Yes\">".
 "<input name=\"ballet\" type=\"submit\" value=\"No\">").
 (($CURUSER["class"]>=UC_ADMINISTRATOR&&$row["active"])?"<input name=\"closepoll\" type=\"submit\" value=\"Close Poll\">":
  "<br>".($row["active"]?"<br><font color=#00FF00>Poll Open</font>":"<br><font color=#FF0000>Poll Closed</font>")).
 ($CURUSER["class"]>=UC_ADMINISTRATOR?"<br><input name=\"removepoll\" type=\"submit\" value=\"Remove Poll\">":"")
 ?></td></tr>  </form><tr>
   <form action="uploadapp.php" method="post" enctype="multipart/form-data" name="poll<?=$row["id"]?>" id="uploadapp">
   <input name="form" type="hidden" value="11">
 <input name="pollid" type="hidden" value="<?=$row["id"]?>">
 <td>&nbsp</td>
 <td>Comments from Mods</td>
 <td colspan="4"><textarea name="modcomments" rows="5" cols="50"><?=$row["modcomments"]?></textarea></td>
 <td colspan="5"><input name="newcomments" type="text" value="" maxlength="80"><br><input type="submit" name="addcomment" value="Add Comment"></td>
   </form>
 </tr>
<?php 
  }
  print("</table>");
 }


end_frame();
}    
//end_frame();
stdfoot();
