<?php

require "backend/functions.php";
dbconn(false);
loggedinorreturn();
ob_start("ob_gzhandler");

$requestid = (int) ($_GET['requestid'] ?? 0);

$count = DB::fetchColumn("
    select count(addedrequests.id)
    from addedrequests
        inner join users on addedrequests.userid = users.id
        inner join requests on addedrequests.requestid = requests.id
    WHERE addedrequests.requestid = $requestid");

$perpage = 50;

[$pagertop, $pagerbottom, $limit] = pager($perpage, $count, $_SERVER["PHP_SELF"] ."?");

$res = DB::fetchAll("
    select
        users.id as userid, users.username, users.downloaded,users.uploaded,
        requests.id as requestid, requests.request
    from addedrequests
        inner join users on addedrequests.userid = users.id
        inner join requests on addedrequests.requestid = requests.id
    WHERE addedrequests.requestid = $requestid
    $limit");

stdhead("Votes");

$arr2 = DB::fetchAssoc("select request from requests where id=$requestid");

begin_frame($txt['VOTES'] . ": <a href=requests.php?details=$requestid>$arr2[request]</a>");
print("<p>" . $txt['VOTE_FOR_THIS'] . "<a href=addrequest.php?id=$requestid><b>" . $txt['REQUEST'] . "</b></a></p>");

echo $pagertop;

if (! $res) {
    print("<p align=center><b>" . $txt['NOTHING_FOUND'] . "</b></p>\n");
} else {
    print("<center><table cellspacing=0 cellpadding=3 class=table_table>
        <tr><td class=table_head>" . $txt['USERNAME'] . "</td>
        <td class=table_head align=left>" . $txt['UPLOADED'] . "</td>
        <td class=table_head align=left>" . $txt['DOWNLOADED'] . "</td>
        <td class=table_head align=left>" . $txt['RATIO'] . "</td>\n");

    foreach ($res as $arr) {
        if ($arr["downloaded"] > 0) {
           $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
           $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
        }
        elseif ($arr["uploaded"] > 0)
            $ratio = "Inf.";
        else
            $ratio = "---";
        $uploaded = mksize($arr["uploaded"]);
        $downloaded = mksize($arr["downloaded"]);

        print("<tr><td class=table_col1><a href=account-details.php?id=$arr[userid]><b>$arr[username]</b></a></td>
            <td align=left class=table_col2>$uploaded</td>
            <td align=left class=table_col1>$downloaded</td>
            <td align=left class=table_col2>$ratio</td></tr>\n");
    }

    print("</table></center><BR><BR>\n");
}

end_frame();

stdfoot();

