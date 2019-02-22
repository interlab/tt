<?php

require_once __DIR__ . '/../../backend/functions.php';

dbconn(false);

loggedinorreturn();

stdhead("Completed Details");

$id = (int) ($_GET['id'] ?? 0);

$row = DB::fetchAssoc('
    select count(snatched.id) AS num, t.name
    from snatched
        left join users on snatched.userid = users.id
        left join torrents AS t on snatched.torrentid = t.id
    where snatched.finished = ?
        AND snatched.torrentid = ' . $id . '
    LIMIT 1',
    ['yes']
);

$perpage = 30;
[ $pagertop, $pagerbottom, $limit ] = pager($perpage, $row['num'], $_SERVER["PHP_SELF"] . "?id=$id&");


$dt = gmtime() - 180;
// $dt = get_date_time($dt);

begin_frame("<a href=torrents-details.php?id=$id><b>$row[name]</b></a>");

print("<p align=center>The users at the top finished the download most recently</p>");

echo $pagertop;

print("<table border=1 cellspacing=0 cellpadding=1 align=center>\n");
print("<tr>
    <td class=table_head align=center>Username</td>
    <td class=table_head align=center>Uploaded</td>
    <td class=table_head align=center>Downloaded</td>
    <td class=table_head align=center>Ratio</td>
    <td class=table_head align=center>When Completed</td>
    <td class=table_head align=center>Last Action</td>
    <td class=table_head align=center>Seeding</td>
    <td class=table_head align=center>PM User</td>
    <td class=table_head align=center><font color=red>Report</font></td>
    <td class=table_head align=center>On/Off</td></tr>");

$res = DB::query("
    select u.id, u.username, u.title, u.uploaded, u.downloaded,
        s.completedat, s. last_action, s.seeder, s.userid,
        unix_timestamp(u.last_access) as last_access,
        s.uploaded AS suploaded, s.downloaded AS sdownloaded
    from snatched AS s
        left join users AS u on s.userid = u.id
        left join torrents AS t on s.torrentid = t.id
    where s.finished = 'yes'
        AND s.torrentid = $id
    ORDER BY s.id desc
    $limit");

while ($arr = $res->fetch()) {
    // start Global
    if ($arr["downloaded"] > 0) {
        $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
        $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
    } elseif ($arr["uploaded"] > 0) {
        $ratio = "Inf.";
    } else {
        $ratio = "---";
    }
    $uploaded = mksize($arr["uploaded"]);
    $downloaded = mksize($arr["downloaded"]);
    // start torrent
    if ($arr["downloaded"] > 0) {
        $ratio2 = number_format($arr["suploaded"] / $arr["sdownloaded"], 3);
        $ratio2 = "<font color=" . get_ratio_color($ratio2) . ">$ratio2</font>";
    } elseif ($arr["suploaded"] > 0) {
        $ratio2 = "Inf.";
    } else {
        $ratio2 = "---";
    }
    $uploaded2 = mksize($arr["suploaded"]);
    $downloaded2 = mksize($arr["sdownloaded"]);
    // end
    $highlight = $CURUSER["id"] == $arr["id"] ? " bgcolor=#00A527" : "";
    if (empty($arr['username'])) {
        $arr['username'] = "Unknown";
    }
    print("<td align=center class=table_col1><a href=account-details.php?id=$arr[userid]><b>$arr[username]</b></a></td>
    <td align=left class=table_col2>$uploaded Global<br>$uploaded2 Torrent</td>
    <td align=left class=table_col1>$downloaded Global<br>$downloaded2 Torrent</td>
    <td align=left class=table_col2>$ratio Global<br>$ratio2 Torrent</td>
    <td align=center class=table_col1>$arr[completedat]</td>
    <td align=center class=table_col2>$arr[last_action]</td>
    <td align=center class=table_col1>" . ($arr["seeder"] == "yes" ? "<b><font color=green>Yes</font>"
        : "<font color=red>No</font></b>") . "</td>
    <td align=center class=table_col2><a href=$SITEURL/account-inbox.php?receiver=$arr[userid]>
        <img src=images/button_pm.gif></a></td>
    <td align=center class=table_col1><a href=$SITEURL/report.php?user=$arr[userid]>
        <img src=images/button_report.gif></a></td><td align=center class=table_col2>".
        ($arr['last_access'] > $dt ? "<img src=images/button_online.gif border=0 alt=\"Online\">"
            : "<img src=images/button_offline.gif border=0 alt=\"Offline\">" )."</td>"."
    </tr>\n");
}
print("</table>\n");

echo $pagerbottom;
end_frame();
stdfoot();
