<?php

require_once("backend/functions.php");

dbconn();

stdhead("Vote");

begin_frame($txt['VOTES']);

$requestid = (int) ($_GET["id"] ?? 0);
$userid = (int) ($CURUSER["id"] ?? 0);
$voted = DB::fetchAssoc("SELECT * FROM addedrequests WHERE requestid = $requestid and userid = $userid");

if ($voted) {
?>
<br><p>You've already voted for this request, only 1 vote for each request is allowed</p>
<p>Back to <a href=requests.php?sa=view><b>requests</b></a></p>
<br><br>
<?php
} else {
DB::query("UPDATE requests SET hits = hits + 1 WHERE id = $requestid");
DB::query("INSERT INTO addedrequests VALUES(0, $requestid, $userid)");

print("<br><p>Successfully voted for request $requestid</p>"
        . "<p>Back to <a href=requests.php?sa=view><b>requests</b></a></p>"
        . "<br><br>");
}

end_frame();

stdfoot();
