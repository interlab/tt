<?php
//
// - Theme And Language Updated 28.Nov.05
//
// If you replace $num with $rank below.  It will show ranks! 2 persons can have number 1 then.

require "backend/functions.php";
dbconn(false);

loggedinorreturn();

//DONATOR TABLE FUNCTION
 function donortable($res, $frame_caption) {
 print ("<div align=left><B>$frame_caption </B><BR>");
   if (mysql_num_rows($res) > 0)
{
 print("<table border=1 cellspacing=0 cellpadding=2 class=table_table>\n");
 $num = 0;

 while ($a = mysql_fetch_assoc($res)) {
   ++$num;
$dis = $a["donated"];
if ($dis == "0")
     break;
if ($dis == $last)
  $rank = " ";
else
  $rank = $num;
if ($rank && $num > 10)
         break;
   if ($menu != "1") {
     echo "<tr>"
      ."<td class=table_head>" . ACCOUNT_RANK . "</td>"
      ."<td class=table_head>" . ACCOUNT_USER . "</td>"
      ."<td class=table_head>" . DONATED . "</td>"
     ."</tr>";
     $menu = 1;
   }
   print("<tr><td class=table_col1>$num</td><td class=table_col2 align=left><a href=account-details.php?id=$a[id]><b>$a[username]" .
    "</b></a></td><td class=table_col1 align=right>$$dis</td></tr>");
$last = $dis;
 }
 echo "</table></div>";
  }else{
 echo "<font color=red>" . NOTHING_TO_SHOW . "</font></div>";
}
}


function usertable($res, $frame_caption) {
  print ("<div align=left><B>$frame_caption </B><BR>");
    if (mysql_num_rows($res) > 0)
	{
	print("<table border=1 cellspacing=0 cellpadding=2 class=table_table>\n");
  $num = 0;
  while ($a = mysql_fetch_assoc($res)) {
    ++$num;
    if ($a["uploaded"] == "0")
	  break;
    if ($a["downloaded"]) {
      $ratio = $a["uploaded"] / $a["downloaded"];
      $color = get_ratio_color($ratio);
      $ratio = number_format($ratio, 2);
      if ($color)
        $ratio = "<font color=$color>$ratio</font>";
    }
    else
      $ratio = "Inf.";
    if ($menu != "1") {
      echo "<tr>"
		."<td class=table_head>" . ACCOUNT_RANK . "</td>"
		."<td class=table_head align=left>" . ACCOUNT_USER . "</td>"
		."<td class=table_head>" . UPLOADED . "</td>"
		."<td class=table_head>" . DOWNLOADED . "</td>"
	    ."<td class=table_head align=right>" . RATIO . "</td>"
	    ."</tr>";
      $menu = 1;
    }
    print("<tr><td class=table_col1>$num</td><td class=table_col2 align=left><a href=account-details.php?id=" . $a["id"] . "><b>" . $a["username"] .
          "</b></a></td><td class=table_col1 align=right>" . mksize($a["uploaded"]) .
          "</td><td class=table_col2 align=right>" . mksize($a["downloaded"]) .
          "</td><td class=table_col1 align=right>" . $ratio . "</td></tr>");
  }
  echo "</table></div>";
  	}else{
		echo "<font color=red>" . NOTHING_TO_SHOW . "</font></div>";
	}
}

function _torrenttable($res, $frame_caption) {
  print ("<div align=left><B>$frame_caption </B><BR>");
  if (mysql_num_rows($res) > 0)
	{
	  print("<table border=1 cellspacing=0 cellpadding=2 class=table_table>\n");
  $num = 0;
  while ($a = mysql_fetch_assoc($res)) {
      ++$num;
      if ($a["leechers"])
      {
        $r = $a["seeders"] / $a["leechers"];
        $ratio = "<font color=" . get_ratio_color($r) . ">" . number_format($r, 2) . "</font>";
      }
      else
        $ratio = "Inf.";
        if ($menu != "1") {
          echo "<tr>"
		      ."<td class=ttable_head>" . ACCOUNT_RANK . "</td>"
		      ."<td class=ttable_head align=left>" . NAME . "</td>"
		      ."<td class=ttable_head align=right>" . COMPLETED . "</td>"
		      ."<td class=ttable_head align=right>" . SEEDS . "</td>"
		      ."<td class=ttable_head align=right>" . LEECH . "</td>"
		      ."<td class=ttable_head align=right>" . PEERS . "</td>"
	 	      ."<td class=ttable_head align=right>" . RATIO . "</td>"
 	          ."</tr>";
 	      $menu = 1;
        }
        print("<tr><td class=ttable_col1>$num</td><td class=ttable_col2 align=left><a href=torrents-details.php?id=" . $a["id"] . "&hit=1><b>" .
        $a["name"] . "</b></a></td><td class=ttable_col1 align=center>" . number_format($a["times_completed"]) .
        "</td><td class=ttable_col2 align=center>" . number_format($a["seeders"]) .
        "</td><td class=ttable_col1 align=center>" . number_format($a["leechers"]) .
        "</td><td class=ttable_col2 align=center>" . ($a["leechers"] + $a["seeders"]) .
        "</td><td class=ttable_col1 align=right>$ratio</td>\n");
    }
    echo "</table></div>";
	}else{
		echo "<font color=red>" . NOTHING_TO_SHOW . "</font></div>";
	}
}

function countriestable($res, $frame_caption) {
    print ("<div align=left><B>$frame_caption </B><BR>");
	  if (mysql_num_rows($res) > 0)
	{
    print("<table border=1 cellspacing=0 cellpadding=2 class=table_table>\n");
	
	echo "<tr>";
	echo "<td class=table_head>" . ACCOUNT_RANK . "</td>";
	echo "<td class=table_head align=left>" . COUNTRY . "</td>";
	echo "<td class=table_head align=right>" . USERS . "</td>";
	echo "</tr>";
	
    $num = 0;
    while ($a = mysql_fetch_assoc($res))
    {
      ++$num;
      print("<tr><td class=table_col1>$num</td><td class=table_col2 align=left><img align=center src=images/flag/$a[flagpic]>&nbsp;<b>$a[name]</b></td><td align=right class=table_col1>$a[num]</td></tr>\n");
    }
    echo "</table></div>";
		}else{
		echo "<font color=red>" . NOTHING_TO_SHOW . "</font></div>";
	}
}
function postertable($res, $frame_caption) {
    print ("<div align=left><B>$frame_caption </B><BR>");
	  if (mysql_num_rows($res) > 0)
	{
	print("<table border=1 cellspacing=0 cellpadding=2 class=table_table>\n");
	
	echo "<tr>";
	echo "<td class=table_head width=80>" . ACCOUNT_RANK . "</td>";
	echo "<td class=table_head align=left>" . ACCOUNT_USER . "</td>";
	echo "<td class=table_head align=left width=100>" . TORRENTS_POSTED . "</td>";
	echo "</tr>";
	
    $num = 0;
    while ($a = mysql_fetch_assoc($res))
    {
      ++$num;
      print("<tr><td class=table_col1>$num</td><td class=table_col2 align=left><a href=account-details.php?id=$a[id]><b>$a[username]</b></td><td align=right class=table_col1>$a[num]</td></tr>\n");
    }
    echo "</table></div>";
		}else{
		echo "<font color=red>" . NOTHING_TO_SHOW . "</font></div>";
	}
}

//main stats here
$a = @mysql_fetch_assoc(@mysql_query("SELECT id,username FROM users WHERE status='confirmed' ORDER BY id DESC LIMIT 1"));
if ($CURUSER)
  $latestuser = "<a href=account-details.php?id=" . $a["id"] . ">" . $a["username"] . "</a>";
else
  $latestuser = "<b>$a[username]</b>";
$registered = number_format(get_row_count("users"));
$torrents = number_format(get_row_count("torrents"));

$result = mysql_query("SELECT SUM(downloaded) AS totaldl FROM users") or sqlerr(__FILE__, __LINE__); 

while ($row = mysql_fetch_array ($result)) 
{ 
$totaldownloaded      = $row["totaldl"]; 
} 
$result = mysql_query("SELECT SUM(uploaded) AS totalul FROM users") or sqlerr(__FILE__, __LINE__); 

while ($row = mysql_fetch_array ($result)) 
{ 
$totaluploaded      = $row["totalul"]; 
}
$seeders = get_row_count("peers", "WHERE seeder='yes'");
$leechers = get_row_count("peers", "WHERE seeder='no'");
$usersactive = 0;
if ($leechers == 0)
  $ratio = "100";
else
  $ratio = round($seeders / $leechers * 100);
 if ($ratio < 20)
    $ratio = "<font class=red>" . $ratio . "%</font>";
 else
	$ratio .= "%";
$peers = number_format($seeders + $leechers);
$seeders = number_format($seeders);
$leechers = number_format($leechers);
//start count visited today
$res = mysql_query("SELECT COUNT(*) FROM users WHERE UNIX_TIMESTAMP(" . get_dt_num() . ") - UNIX_TIMESTAMP(last_access) < 86400");
$arr3 = mysql_fetch_row($res);
$totaltoday = $arr3[0];
// start count registered today
$res = mysql_query("SELECT COUNT(*) FROM users WHERE UNIX_TIMESTAMP(" . get_dt_num() . ") - UNIX_TIMESTAMP(added) < 86400");
$arr44 = mysql_fetch_row($res);
$regtoday = $arr44[0];
//start count online now
$res = mysql_query("SELECT COUNT(*) FROM users WHERE UNIX_TIMESTAMP(" . get_dt_num() . ") - UNIX_TIMESTAMP(last_access) < 900");
$arr4 = mysql_fetch_row($res);
$totalnow = $arr4[0];
if ($CURUSER)
	guestadd();
if (!$activepeople)
  $activepeople = "" . NO_USERS . "";

  if (!$todayactive)
  $todayactive = "" . NO_USERS . "";
$guests = getguests();
if (!$guests)
	$guests = "0";

$time_start = getmicrotime();
//end here

///////////////////////////////////////// PAGE LAYOUT //////////////////////////////

  stdhead();

  begin_frame("" . STATS . "", center);

echo "<div align=left><font class=stats>" . WELCOME_NEW . ": " . $latestuser . "</font><br>";
if (!$activepeople)
    echo "<br><font class=stats>" . ONLINE_USERS . ": (" . $totalnow . ")<b>0 Members</b></font>";
else
	echo "<br><font class=stats>" . ONLINE_USERS . ": (" . $totalnow . ")</font>";
$totalusers = $totalnow + $guests;
echo "<br><font class=stats>" . GUESTS_ONLINE . ": (" . $guests . ")</font>";
echo "<br><font class=stats>" . TOTAL_ONLINE . ": (" . $totalusers . ")</font>";
if (!$todayactive)
    echo "<br><font class=stats>" . VISITORS_TODAY . ": (<!--<a href='visitorstoday.php'>-->" . $totaltoday . "<!--</a>-->)<br>0 Members</font>";
else
	echo "<br><font class=stats>" . VISITORS_TODAY . ": (<!--<a href='visitorstoday.php'>-->" . $totaltoday . "<!--</a>-->)<!--<br>" . $todayactive . "--></font>";
echo "<br><font class=stats>" . TOTAL_USERS . ": " . $registered . "</font>";
echo "<br><font class=stats>" . NEWUSERS_TODAY . ": " . $regtoday . "</font>";
echo "<br><font class=stats>" . ACTIVE_TRANSFERS . ": " . $peers . "</font>";
ECHO "<br><font class=stats>" . DOWNLOADED . ": " . mksize($totaldownloaded) . "</FONT>";
ECHO "<br><font class=stats>" . UPLOADED . ": " . mksize($totaluploaded) . "</FONT>";
echo "<br><font class=stats>" . TRACKING . " " . $torrents . " Torrents</font>";
echo "<br><font class=stats>" . SEEDS . ": " . $seeders . "</font>";
echo "<br><font class=stats>" . LEECH . ": " . $leechers . "</font>";
echo "<br><font class=stats>" . SEED_RATIO . ": " . $ratio . "</font>";
echo "<br><br></div>";

/////////////////////////////////////////

  $r = mysql_query("SELECT users.id, users.username, COUNT(torrents.owner) as num FROM torrents LEFT JOIN users ON users.id = torrents.owner GROUP BY owner ORDER BY num DESC LIMIT 10") or sqlerr();
  postertable($r, "Top 10 Posters</font>"); echo "<br>";

  $r = mysql_query("SELECT * FROM users WHERE secret <> '' ORDER BY uploaded DESC LIMIT 10") or die;
  usertable($r, "Top 10 Uploaders"); echo "<br>";

  $r = mysql_query("SELECT * FROM users WHERE secret <> '' ORDER BY downloaded DESC LIMIT 10") or die;
  usertable($r, "Top 10 Leechers"); echo "<br>";

  $r = mysql_query("SELECT * FROM users WHERE downloaded > 104857600 ORDER BY uploaded - downloaded DESC LIMIT 10") or die;
  usertable($r, "Top 10 Best Sharers <font class=small>(with minimum 100 MB downloaded)</font>"); echo "<br>";

  $r = mysql_query("SELECT * FROM users WHERE downloaded > 104857600 AND secret <> '' ORDER BY downloaded - uploaded DESC, downloaded DESC LIMIT 10") or die;
  usertable($r, "Top 10 Worst Sharers <font class=small>(with minimum 100 MB downloaded)</font>"); echo "<br>";

  $r = mysql_query("SELECT * FROM torrents ORDER BY seeders + leechers DESC, seeders DESC, added ASC LIMIT 10") or sqlerr();
  _torrenttable($r, "Top 10 Most Active Torrents</font>"); echo "<br>";

  $r = mysql_query("SELECT * FROM torrents WHERE seeders >= 5 ORDER BY seeders / leechers DESC, seeders DESC, added ASC LIMIT 10") or sqlerr();
  _torrenttable($r, "Top 10 Best Seeded Torrents <font class=small>(with minimum 5 seeders)</font>"); echo "<br>";

  $r = mysql_query("SELECT * FROM torrents WHERE leechers >= 5 AND times_completed > 0 ORDER BY seeders / leechers ASC, leechers DESC LIMIT 10") or sqlerr();
  _torrenttable($r, "Top 10 Worst Seeded Torrents <font class=small>(with minimum 5 leechers, excluding unsnatched torrents)</font>"); echo "<br>";

  $r = mysql_query("SELECT * FROM users ORDER BY donated DESC, username LIMIT 100") or die;
  donortable($r, "Top 10 Donors"); echo "<br>";

  $r = mysql_query("SELECT name, flagpic, COUNT(users.country) as num FROM countries LEFT JOIN users ON users.country = countries.id GROUP BY name ORDER BY num DESC LIMIT 10") or sqlerr();
  countriestable($r, "Top 10 Countries</font>");echo "<br><br>";

  end_frame();
  stdfoot();
?>