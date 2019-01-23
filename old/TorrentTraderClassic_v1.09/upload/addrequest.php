<?php

require_once("backend/functions.php");

dbconn();

stdhead("Vote");

begin_frame($txt['VOTES']);

$requestid = (int) $_GET["id"];
$userid = (int) $CURUSER["id"];
$res = mysql_query("SELECT * FROM addedrequests WHERE requestid=$requestid and userid = $userid") or sqlerr();
$arr = mysql_fetch_assoc($res);
$voted = $arr;

if ($voted) {
?>
<br><p>You've already voted for this request, only 1 vote for each request is allowed</p><p>Back to <a href=viewrequests.php><b>requests</b></a></p><br><br>
<?php
} else {
mysql_query("UPDATE requests SET hits = hits + 1 WHERE id=$requestid") or sqlerr();
@mysql_query("INSERT INTO addedrequests VALUES(0, $requestid, $userid)") or sqlerr();

print("<br><p>Successfully voted for request $requestid</p><p>Back to <a href=viewrequests.php><b>requests</b></a></p><br><br>");

}

end_frame();

stdfoot();
