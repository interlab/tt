<?php

dbconn(false);
loggedinorreturn();

$userid = (int) ($_GET['id'] ?? 0);

if (! is_valid_id($userid)) {
    bark('Error', 'Invalid ID');
}

$page = (int) ($_GET['page'] ?? 0);
$action = $_GET['action'] ?? '';
$perpage = 25;

// Action: View posts - forum
if ($action == 'viewposts') {
    $query = '
        SELECT COUNT(*)
        FROM forum_posts AS p
            JOIN forum_topics as t ON p.topicid = t.id
            JOIN forum_forums AS f ON t.forumid = f.id
        WHERE p.userid = ' . $userid . '
            AND f.minclassread <= ' . $CURUSER['class'];
    $postcount = DB::fetchColumn($query);
    if (! $postcount) {
        bark('Error', 'No posts found');
    }

    // Make page menu
    [$pagertop, $pagerbottom, $limit] = pager($perpage, $postcount,
        $_SERVER['PHP_SELF'] . '?' . b(['action' => 'viewposts', 'id' => $userid])
    );

    // Get user data
    $arr = DB::fetchAssoc('SELECT username, donated, warned FROM users WHERE id = ' . $userid);
    if (empty($arr)) {
        $subject = 'unknown['.$userid.']';
    } else {
        $subject = "<a href=account-details.php?id=$userid><b>$arr[username]</b></a>".
            ($arr['donated'] > 1 ? "<img src=images/star.gif alt='Donor' style='margin-left: 4pt'>" : '') .
            ($arr['warned'] == 'yes' ? "<img src=images/warned.gif alt='Warned' style='margin-left: 4pt'>" : '');
    }

    // Get posts
    $query = '
        SELECT f.id AS f_id, f.name, t.id AS t_id, t.subject, t.lastpost, r.lastpostread, p.*
        FROM forum_posts AS p
            JOIN forum_topics as t ON p.topicid = t.id
            JOIN forum_forums AS f ON t.forumid = f.id
            LEFT JOIN forum_readposts as r ON p.topicid = r.topicid
                AND p.userid = r.userid
        WHERE p.userid = ' . $userid . '
            AND f.minclassread <= ' . $CURUSER['class'] . '
        ORDER BY p.id DESC
        ' . $limit;
    $res = DB::fetchAll($query);
    if (! $res) {
        bark('Error', 'No posts found');
    }

    stdhead('Posts history');

    // Print table

    begin_frame('Post history for ' . $subject);

    if ($postcount > $perpage) {
        echo $pagertop;
    }

    foreach ($res as $arr) {
        $postid = $arr['id'];
        $posterid = $arr['userid'];
        $topicid = $arr['t_id'];
        $topicname = $arr['subject'];
        $forumid = $arr['f_id'];
        $forumname = $arr['name'];
        $newposts = ($arr['lastpostread'] < $arr['lastpost']) && $CURUSER['id'] == $userid;
        $added = $arr['added'] . ' GMT (' . (get_elapsed_time(sql_timestamp_to_unix_timestamp($arr['added']))) . ' ago)';

        print("<br><table border=0 cellspacing=0 cellpadding=0 width=95%><tr><td width=100% bgcolor=#66CCFF>
        <b>Forum:&nbsp;</b>
        <a href=forums.php?action=viewforum&amp;forumid=$forumid>$forumname</a>
        &nbsp;--&nbsp;<b>Topic:&nbsp;</b>
        <a href=forums.php?action=viewtopic&topicid=$topicid>$topicname</a>
      &nbsp;--&nbsp;<b>Post:&nbsp;</b>
      #<a href=forums.php?action=viewtopic&topicid=$topicid&page=p$postid#$postid>$postid</a>" .
      ($newposts ? " &nbsp;<b>(<font color=red>NEW!</font>)</b>" : "") .
        "&nbsp;--&nbsp;$added</td></tr></table>\n");

        begin_table(true);

        $body = format_comment($arr["body"]);

        if (is_valid_id($arr['editedby'])) {
            $subrow = DB::fetchAssoc('SELECT username FROM users WHERE id = ' . $arr['editedby'] . ' LIMIT 1');
            if ($subrow) {
                $body .= "<p><font size=1 class=small>Last edited by <a href=userdetails.php?id=$arr[editedby]><b>".
                "$subrow[username]</b></a> at $arr[editedat] GMT</font></p>\n";
            }
        }

        print("<tr valign=top><td width=95%>&nbsp;<i>$body</i></td></tr>\n");

        end_table();
    }

    if ($postcount > $perpage) {
        echo $pagerbottom;
    }

    end_frame();

    stdfoot();
}

// Action: View comments
elseif ($action === "viewcomments") {
    // LEFT due to orphan comments
    $from_is = "comments AS c LEFT JOIN torrents as t
                ON c.torrent = t.id";

    $where_is = "c.user = $userid";
    $order_is = "c.id DESC";

    $query = '
        SELECT COUNT(*)
        FROM comments AS c
            LEFT JOIN torrents as t ON c.torrent = t.id
        WHERE c.user = ' . $userid . '
        ORDER BY c.id DESC';
    $commentcount = DB::fetchColumn($query);
    if (!$commentcount) {
        bark("Error", "No comments found");
    }

    // Make page menu
    [$pagertop, $pagerbottom, $limit] = pager($perpage, $commentcount,
            $_SERVER["PHP_SELF"] . '?'. b(['action' => 'viewcomments', 'id' => $userid]));

    // Get user data
    $arr = DB::fetchAssoc("SELECT username, donated, warned FROM users WHERE id = $userid");

    if ($arr) {
        $subject = "<a href=account-details.php?id=$userid><b>$arr[username]</b></a>" .
            ($arr["donated"] > 1 ? "<img src=images/star.gif alt='Donor' style='margin-left: 4pt'>" : "") .
            ($arr["warned"] == "yes" ? "<img src=images/warned.gif alt='Warned' style='margin-left: 4pt'>" : "");
    } else {
        $subject = "unknown[$userid]";
    }
    // Get comments

    $select_is = "t.name, c.torrent AS t_id, c.id, c.added, c.text";
    $query = "SELECT $select_is FROM $from_is WHERE $where_is " .
                "ORDER BY $order_is ".$limit;

    $res = DB::fetchAll($query);
    if (! $res) {
        bark("Error", "No comments found");
    }

    stdhead("Comments history");

    // Print table

    begin_frame("Comments history for $subject");

    if ($commentcount > $perpage) {
        echo $pagertop;
    }

    foreach ($res as $arr) {
        $commentid = $arr["id"];
        $torrent = $arr["name"];

        // make sure the line doesn't wrap
        if (strlen($torrent) > 55) {
            $torrent = mb_substr($torrent, 0, 52, 'utf-8') . '...';
        }

        $torrentid = $arr["t_id"];

        // find the page; this code should probably be in torrents-details.php instead

        $count = DB::fetchColumn("SELECT COUNT(*) FROM comments WHERE torrent = $torrentid AND id < $commentid LIMIT 1");
        $comm_page = floor($count / 20);
        $page_url = $comm_page ? "&amp;page=$comm_page" : '';

        $added = $arr["added"] . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]))) . " ago)";

        print("<br><table border=0 cellspacing=0 cellpadding=0 width=95%><tr><td width=100% bgcolor=#66CCFF>".
            "<b>Torrent:&nbsp;</b>".
            ($torrent ? ("<a href=torrents-details.php?id=$torrentid&tocomm=1>$torrent</a>")
                : " [Deleted] ").
            "&nbsp;---&nbsp;<b>Comment:&nbsp;</b>#<a href=torrents-details.php?id=$torrentid&tocomm=1$page_url>$commentid</a>
            &nbsp;---&nbsp;$added
            </td></tr></table>\n");

        begin_table(true);

        $body = format_comment($arr["text"]);

        print("<tr valign=top><td >$body</td></tr>\n");

        end_table();
    }

    if ($commentcount > $perpage) {
        echo $pagerbottom;
    }

    end_frame();

    stdfoot();
}

// Handle unknown action

elseif ($action != '') {
    bark("History Error", "Unknown action '$action'.");
}

// Any other case
else {
    bark("History Error", "Invalid or no query.");
}

