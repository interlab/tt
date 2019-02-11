<?php

function intget($key)
{
    return (int) ($_GET[$key] ?? 0);
}

// note to self, loook at showerror()
ob_start("ob_gzhandler");
require "backend/functions.php";
dbconn(false);
loggedinorreturn();

$forumbanned = $CURUSER["forumbanned"];
if ($forumbanned == "yes") {
    stdhead("Banned");
    begin_frame("Notice", 'center');
    echo '<BR><b>Unfortunately you have been banned from accessing the forums.'
        .' Please contact a member of staff if you do not know the reason.</b><BR><BR>';
    end_frame();
    stdfoot();
} else {

//Here we decide if the forums is on or off
if ($FORUMS)
{
//define the clickable smilies
function quicktags()
{
    echo "<center><table border=0 cellpadding=0 cellspacing=0><tr>";
    echo "<td width=26><a href=\"javascript:Smilies(':)')\"><img src=images/smilies/smile1.gif border=0 alt=':)'></a></td>";
    echo "<td width=26><a href=\"javascript:Smilies(';)')\"><img src=images/smilies/wink.gif border=0 alt=';)'></a></td>";
    echo "<td width=26><a href=\"javascript:Smilies(':D')\"><img src=images/smilies/grin.gif border=0 alt=':D'></a></td>";
    echo "</tr><tr>";
    echo "<td width=26><a href=\"javascript:Smilies(':P')\"><img src=images/smilies/tongue.gif border=0 alt=':P'></a></td>";
    echo "<td width=26><a href=\"javascript:Smilies(':lol:')\"><img src=images/smilies/laugh.gif border=0 alt=':lol:'></a></td>";
    echo "<td width=26><a href=\"javascript:Smilies(':yes:')\"><img src=images/smilies/yes.gif border=0 alt=':yes:'></a></td>";
    echo "</tr><tr>";
    echo "<td width=26><a href=\"javascript:Smilies(':no:')\"><img src=images/smilies/no.gif border=0 alt=':no:'></a></td>";
    echo "<td width=26><a href=\"javascript:Smilies(':wave:')\"><img src=images/smilies/wave.gif border=0 alt=':wave:'></a></td>";
    echo "<td width=26><a href=\"javascript:Smilies(':ras:')\"><img src=images/smilies/ras.gif border=0 alt=':ras:'></a></td>";
    echo "</tr><tr>";
    echo "<td width=26><a href=\"javascript:Smilies(':sick:')\"><img src=images/smilies/sick.gif border=0 alt=':sick:'></a></td>";
    echo "<td width=26><a href=\"javascript:Smilies(':yucky:')\"><img src=images/smilies/yucky.gif border=0 alt=':yucky:'></a></td>";
    echo "<td width=26><a href=\"javascript:Smilies(':rolleyes:')\"><img src=images/smilies/rolleyes.gif border=0 alt=':rolleyes:'></a></td>";
    echo "</tr></table>";
    echo "<br><a href=smilies.php target=_blank>[More Smilies]</a><br><br><a href=tags.php target=_blank>[BB Tags]</a></center>";
}

//define the clickable tags
function quickbb()
{
    echo "<center><table border=0 cellpadding=0 cellspacing=2><tr>";
    echo "<tr>";
    echo "<td width=22><a href=\"javascript:Smilies('[b] [/b]')\">
            <img src=./images/bbcode/bbcode_bold.gif border=0 alt='Bold'></a></td>";
    echo "<td width=22><a href=\"javascript:Smilies('[i] [/i]')\">
            <img src=./images/bbcode/bbcode_italic.gif border=0 alt='Italic'></a></td>";
    echo "<td width=22><a href=\"javascript:Smilies('[u] [/u]')\">
            <img src=./images/bbcode/bbcode_underline.gif border=0 alt='Underline'></a></td></tr>";
    echo "<tr><td width=22><a href=\"javascript:Smilies('[center] [/center]')\">
            <img src=./images/bbcode/bbcode_center.gif border=0 alt='Center'></a></td>";
    echo "<td width=22><a href=\"javascript:Smilies('[url] [/url]')\">
            <img src=./images/bbcode/bbcode_url.gif border=0 alt='Url'></a></td>";
    echo "<td width=22><a href=\"javascript:Smilies('[img] [/img]')\">
            <img src=./images/bbcode/bbcode_image.gif border=0 alt='Img'></a></td>";
    echo "</tr></table>";
}


$themedir = "themes/".getThemeUri()."/forums/";

// setup the forum head aread
function forumheader($location)
{
?>
    <table align=center cellpadding=0 cellspacing=0 style='border-collapse: collapse' bordercolor=#646262 width=100% border=1>
    <tr><td><table  width='100%' cellspacing='5' border=0 bgcolor=#E0F1FE >
        <tr>
        <td><a href='forums.php'>Welcome to our Forums</a></td>
        <td align='right'><img src='images/atb_help.gif' border='0' alt='' />&nbsp;<a href='faq.php'>FAQ</a>&nbsp; &nbsp;&nbsp;
            <img src='images/atb_search.gif' border='0' alt='' />&nbsp;<a href='forums.php?action=search'>Search</a></td></tr></table>
            <table width='100%' cellspacing='5' border=0 bgcolor=#E0F1FE><tr><td><strong>&nbsp;</td>
            <td align='right'><b>Controls</a></b> &middot; <a href='forums.php?action=viewunread'>View New Posts</a> &middot;
            <a href='?catchup'>Mark All Read</a></td></tr></table></td></tr></table><br>
    <table align=center cellpadding=0 cellspacing=5 style='border-collapse: collapse'
        bordercolor=#646262 width=100% border=1 bgcolor=#E0F1FE>
    <tr><td><div align='left'>You are in: &nbsp;<a href='forums.php'>Forums</a> > <?= $location ?></b></div></td></tr>
    </table><br>
<?php
}

$action = strip_tags($_GET["action"] ?? '');

function showerror($heading = "Error", $text, $sort = "Error") {
  stdhead("$sort: $heading");
  begin_frame("<font color=red>$sort: $heading</font>", 'center');
  echo $text;
  end_frame();
  stdfoot();
  die;
}

// Mark all forums as read
function catch_up()
{
    global $CURUSER;

    $userid = $CURUSER["id"];
    // $res = DB::query("SELECT id, lastpost FROM forum_topics");
    // while ($arr = $res->fetch()) {
        // $topicid = $arr["id"];
        // $postid = $arr["lastpost"];

        // DB::executeQuery('REPLACE into forum_readposts (userid, topicid, lastpostread)
        //     VALUES(?, ?, ?)', [$userid, $topicid, $postid]);
        DB::executeQuery('REPLACE into forum_readposts (userid, topicid, lastpostread)
            SELECT ?, id, lastpost FROM forum_topics', [$userid]);
    // }
}

// Returns the minimum read/write class levels of a forum
function get_forum_access_levels($forumid)
{
    $arr = DB::fetchAssoc("SELECT minclassread, minclasswrite FROM forum_forums WHERE id = $forumid");
    if (! $arr)
        return false;

    return array("read" => $arr["minclassread"], "write" => $arr["minclasswrite"]);
}

// Returns the forum ID of a topic, or false on error
function get_topic_forum($topicid)
{
    $fid = DB::fetchColumn("SELECT forumid FROM forum_topics WHERE id = $topicid");
    if (! $fid)
        return false;

    return $fid;
}

// Returns the ID of the last post of a forum
function update_topic_last_post($topicid)
{
    $postid = DB::fetchColumn("SELECT id FROM forum_posts WHERE topicid = $topicid ORDER BY id DESC LIMIT 1");
    DB::executeUpdate("UPDATE forum_topics SET lastpost = $postid WHERE id = $topicid");
}

function get_forum_last_post($forumid)
{
    $postid = DB::fetchColumn("SELECT lastpost FROM forum_topics WHERE forumid=$forumid ORDER BY lastpost DESC LIMIT 1");
    if ($postid)
        return $postid;
    else
        return 0;
}

//Top forum posts
function forumpostertable($res, $frame_caption)
{
    $frame_caption ?>
    <br><table width=160 border=0><tr><td>
    <table align=center cellpadding=1 cellspacing=0 style='border-collapse: collapse' bordercolor=#646262 width=100% border=1>
    <tr>
    <td width="10"><font size=1 face=Verdana><b>Rank</b></td>
    <td width="130" align=left><font size=1 face=Verdana><b>User</b></td>
    <td width="10" align=right><font size=1 face=Verdana><b>Posts</b></td>
    </tr>
    <?php
    $num = 0;
    while ($a = $res->fetch()) {
        ++$num;
        print("
        <tr><td class=alt1>$num</td>
        <td class=alt2 align=left><a href=account-details.php?id=$a[id]><b>$a[username]</b></td>
        <td align=right class=alt1>$a[num]</td></tr>\n");
    }
    print("
        </table>
        </td></tr></table>");
}

// Inserts a quick jump menu
function insert_quick_jump_menu($currentforum = 0)
{
    print("<p align=right><form method=get action=? name=jump>\n");
    print("<input type=hidden name=action value=viewforum>\n");
    print("Quick Jump: ");
    print("<select name=forumid onchange=\"if(this.options[this.selectedIndex].value != -1){ forms['jump'].submit() }\">\n");
    $res = DB::query("SELECT * FROM forum_forums ORDER BY name");
    while ($arr = $res->fetch()) {
        if (get_user_class() >= $arr["minclassread"])
            print("<option value=" . $arr["id"] . ($currentforum == $arr["id"] ? " selected>" : ">") . $arr["name"] . "\n");
    }
    print("</select>\n");
    print("<input type=image class=btn src=images/go.gif border=0>\n");
   // print("<input type=submit value='Go!'>\n");
    print("</form>\n</p>");
}

// Inserts a compose frame
function insert_compose_frame($id, $newtopic = true)
{
    global $maxsubjectlength;

    if ($newtopic) {
        $arr = DB::fetchAssoc("SELECT name FROM forum_forums WHERE id = $id");
        if (! $arr) {
            die("Bad forum id");
        }
        $forumname = $arr["name"];
        print("<p align=center><b>New topic in <a href=forums.php?action=viewforum&forumid=$id>$forumname</a></b></p>\n");
    } else {
        $arr = DB::fetchAssoc("SELECT * FROM forum_topics WHERE id = $id");
        if (! $arr) {
            showerror("Forum error", "Topic not found.");
        }
        $subject = $arr["subject"];
        print("<p align=center>Reply to topic: <a href=forums.php?action=viewtopic&topicid=$id>$subject</a></p>");
    }

    print("<p align=center>Flaming or other anti-social behavior will not be tolerated.\n");
    print("<br>Please do try not to discuss upload/release-specific stuff here, post a torrent comment instead!
        <br><br><B>Please make sure to read the <a href=rules.php>rules</a> before you post</B><br></p>\n");

    begin_frame("Compose Message", true);
    print("<form name=Form method=post action=?action=post>\n");
    if ($newtopic)
        print("<input type=hidden name=forumid value=$id>\n");
    else
        print("<input type=hidden name=topicid value=$id>\n");
    print("<table border=0 width=100%>");

    if ($newtopic) {
        print("
        <center><table border=0 cellpadding=0 cellspacing=0><tr><td colspan=3>&nbsp;</td></tr>
        <tr><td> <b>Subject:</b></td><td width=10>&nbsp;</td>
        <td><input type=text size=70 maxlength=$maxsubjectlength name=subject ></td></tr>
        <tr><td valign=top>");
        quickbb();
        quicktags();
        print("
        </td><td width=10>&nbsp;</td>
        <td class=alt1 align=left style='padding: 0px'><textarea name=body cols=70 rows=20></textarea></td></tr>
        <tr><td colspan=3 align=center>
        <br><input type=submit class=btn value='Submit'>
        <br><br></td></tr></center>");
    }
    print("</table>");
    print("</form>\n");

    end_frame();

    insert_quick_jump_menu();
}

//LASTEST FORUM POSTS
function latestforumposts()
{
    print("<b>Latest Topics</b><br>");
    print("<table align=center cellpadding=1 cellspacing=0
        style='border-collapse: collapse' bordercolor=#646262 width=100% border=1 ><tr>".
    "<td bgcolor=#E0F1FE align=left  width=100%><b>Topic Title</b></td>".
    "<td bgcolor=#E0F1FE align=center width=47><b>Replies</b></td>".
    "<td bgcolor=#E0F1FE align=center width=47><b>Views</b></td>".
    "<td bgcolor=#E0F1FE align=center width=85><b>Author</b></td>".
    "<td bgcolor=#E0F1FE align=right width=85><b>Last Post</b></td>".
    "</tr>");


    /// HERE GOES THE QUERY TO RETRIEVE DATA FROM THE DATABASE AND WE START LOOPING ///
    $for = DB::query("SELECT * FROM forum_topics ORDER BY lastpost DESC LIMIT 5");

    while ($topicarr = $for->fetch()) {
        // Set minclass
        $forum = DB::fetchAssoc('SELECT name, minclassread FROM forum_forums WHERE id = ' . $topicarr['forumid']);

        if ($forum["minclassread"] == '0') {
            $forumname = "<a href=?action=viewforum&amp;forumid=$topicarr[forumid]><b>" . h($forum["name"]) . "</b></a>";

            $topicid = $topicarr["id"];
            $topic_title = $topicarr["subject"];
            $topic_userid = $topicarr["userid"];

            // Topic Views
            $views = $topicarr["views"];
            // End

            /// GETTING TOTAL NUMBER OF POSTS ///
            $posts = DB::fetchColumn("SELECT COUNT(*) FROM forum_posts WHERE topicid = " . $topicid);
            $replies = max(0, $posts - 1);

            /// GETTING USERID AND DATE OF LAST POST ///
            $arr = DB::fetchAssoc("SELECT * FROM forum_posts WHERE topicid = $topicid ORDER BY id DESC LIMIT 1");
            $postid = 0 + $arr["id"];
            $userid = 0 + $arr["userid"];
            $added = "<nobr>" . $arr["added"] . "</nobr>";

            /// GET NAME OF LAST POSTER ///
            $username = DB::fetchColumn("SELECT id, username FROM users WHERE id = $userid LIMIT 1");
            if ($username) {
                $username = '<a href="account-details.php?id=' . $topic_userid . '">' . $username . '</a>';
            }
            else
                $username = 'Unknown[' . $topic_userid . ']';

            /// GET NAME OF THE AUTHOR ///
            $username = DB::fetchColumn("SELECT username FROM users WHERE id = $topic_userid LIMIT 1");
            if ($username) {
                $author = '<a href="account-details.php?id=' . $topic_userid . '">' . $username . '</a>';
            }
            else
                $author = 'Unknown[' . $topic_userid . ']';

            /// GETTING THE LAST INFO AND MAKE THE TABLE ROWS ///
            $a = DB::fetchColumn("SELECT lastpostread FROM forum_readposts WHERE userid = $userid AND topicid = $topicid");
            $new = !$a || $postid > $a[0];
            $subject = "<a href=forums.php?action=viewtopic&topicid=$topicid><b>" . encodehtml($topicarr["subject"]) . "</b></a>";

            print("<tr class=alt1><td style='padding-right: 5px'>$subject</td>".
            "<td class=alt2 align=center>$replies</td>" .
            "<td class=alt1 align=center>$views</td>" .
            "<td class=alt1 align=center>$author</td>" .
            "<td class=alt2 align=right><nobr><small>by&nbsp;$username<br>$added</small></nobr></td>");

            print("</tr>");
        } // while
    }
    print("</table><br>");
} // end function

//Global variables
$postsperpage = 20;
$maxsubjectlength = 50;

//Action: New topic
if ($action == "newtopic") {
    $forumid = intget('forumid');
    if (!is_valid_id($forumid))
      die;
    stdhead("New topic");
    begin_frame("New topic");

    forumheader("Compose New Thread");

    insert_compose_frame($forumid);
    end_frame();
    stdfoot();
    die;
}

///////////////////////////////////////////////////////// Action: POST
if ($action == "post") {
    $forumid = (int) ($_POST["forumid"] ?? 0);
    $topicid = (int) ($_POST["topicid"] ?? 0);
    if (!is_valid_id($forumid) && !is_valid_id($topicid))
        die("w00t");
    $newtopic = $forumid > 0;
    $subject = $_POST["subject"];
    if ($newtopic) {
        if (!$subject)
            showerror("Error", "You must enter a subject.");
        $subject = trim($subject);
        //if (!$subject)
            //showerror("Error", "You must enter a subject.");
        //showerror("Error", "Subject is limited to $maxsubjectlength characters.");
    } else {
      $forumid = get_topic_forum($topicid) or die("Bad topic ID");
    }

    ////// Make sure sure user has write access in forum
    $arr = get_forum_access_levels($forumid) or die("Bad forum ID");
    if (get_user_class() < $arr["write"])
        die("Not permitted");
    $body = trim($_POST["body"]);
    if (!$body)
        showerror("Error", "No body text.");
    $userid = $CURUSER["id"];

    // Create topic
    if ($newtopic) {
        DB::executeUpdate('INSERT INTO forum_topics (userid, forumid, subject) VALUES(?, ?, ?)',
            [$userid, $forumid, $subject]);
        $topicid = DB::lastInsertId();

        if (! $topicid) {
            die("No topic ID returned");
        }
    } else {
        //Make sure topic exists and is unlocked
        $arr = DB::fetchAssoc("SELECT * FROM forum_topics WHERE id = $topicid");
        if (! $arr) {
            die("Topic id n/a");
        }
        if ($arr["locked"] == 'yes')
            die('topic is locked');
        // Get forum ID
        $forumid = $arr["forumid"];
    }

    // Insert the new post
    DB::executeUpdate('INSERT INTO forum_posts (topicid, userid, added, body) VALUES(?, ?, ?, ?)',
        [$topicid, $userid, get_date_time(), $body]
    );
    $postid = DB::lastInsertId();
    if (! $postid) {
        die("Post id n/a");
    }

    // Update topic last post
    update_topic_last_post($topicid);

    // All done, redirect user to the post
    $headerstr = "Location: {$GLOBALS['SITEURL']}/forums.php?action=viewtopic&topicid=$topicid&page=last";
    if ($newtopic)
        header($headerstr);
    else
        header("$headerstr#$postid");
    die;
}

///////////////////////////////////////////////////////// Action: VIEW TOPIC
if ($action == "viewtopic") {
    $topicid = (int) ($_GET["topicid"] ?? 0);
    $page = (int) ($_GET["page"] ?? 0);
    if (!is_valid_id($topicid))
        die;
    $userid = $CURUSER["id"];

    //------ Get topic info
    $arr = DB::fetchAssoc("SELECT * FROM forum_topics WHERE id = $topicid");
    if (! $arr) {
        showerror("Forum error", "Topic not found");
    }
    $locked = ($arr["locked"] == 'yes');
    $subject = $arr["subject"];
    $sticky = $arr["sticky"] == "yes";
    $forumid = $arr["forumid"];

    // Check if user has access to this forum
    $arr2 = DB::fetchAssoc("SELECT minclassread FROM forum_forums WHERE id = $forumid");
    if (get_user_class() < $arr2["minclassread"]) {
        stderr("Access Denied", "You do not have access to the forum this topic is in.", "Error");
    }
    // Update Topic Views
    $views = DB::fetchColumn("SELECT views FROM forum_topics WHERE id = $topicid");
    $new_views = $views + 1;
    DB::query("UPDATE forum_topics SET views = $new_views WHERE id = $topicid");
    // End

    //------ Get forum
    $arr = DB::fetchAssoc("SELECT * FROM forum_forums WHERE id = $forumid");
    if (! $arr) {
        showerror("Forum error", "Forum is empty");
    }
    $forum = $arr["name"];

    //------ Get post count
    $postcount = DB::fetchColumn("SELECT COUNT(*) FROM forum_posts WHERE topicid = $topicid");

    //------ Make page menu
    $pagemenu = "<br><small>\n";
    $perpage = $postsperpage;
    $pages = floor($postcount / $perpage);
    if ($pages * $perpage != $postcount)
        ++$pages;
    if ($page == "last")
        $page = $pages;
    else {
        if($page < 1)
            $page = 1;
        elseif ($page > $pages)
            $page = $pages;
    }
    $offset = $page * $perpage - $perpage;
    //
    if ($page == 1)
      $pagemenu .= "<b>&lt;&lt; Prev</b>";
    else
      $pagemenu .= "<a href=forums.php?action=viewtopic&topicid=$topicid&page=" . ($page - 1) .
        "><b>&lt;&lt; Prev</b></a>";
    //
    $pagemenu .= "&nbsp;&nbsp;";
        for ($i = 1; $i <= $pages; ++$i) {
      if ($i == $page)
        $pagemenu .= "<b>$i</b>\n";
      else
        $pagemenu .= "<a href=forums.php?action=viewtopic&topicid=$topicid&page=$i><b>$i</b></a>\n";
    }
    //
    $pagemenu .= "&nbsp;&nbsp;";
    if ($page == $pages)
      $pagemenu .= "<b>Next &gt;&gt;</b><br><br>\n";
    else
      $pagemenu .= "<a href=forums.php?action=viewtopic&topicid=$topicid&page=" . ($page + 1) .
        "><b>Next &gt;&gt;</b></a><br><br>\n";

    $pagemenu .= '</small>';

    // Get topic posts
    $res = DB::fetchAll("SELECT * FROM forum_posts WHERE topicid = $topicid ORDER BY id LIMIT $offset, $perpage");

    stdhead("View Topic: $subject");
    begin_frame("$forum &gt; $subject", 'center');
    forumheader("<a href=forums.php?action=viewforum&forumid=$forumid>$forum</a> > $subject");

    print ("<table align=center cellpadding=0 cellspacing=5 width=100% border=0 ><tr><td>");

    if (!$locked) {
        print ("<div align='right'><a href=#bottom><img src=" . $themedir . "button_reply.gif border=0></a></div>");
    } else {
        print ("<div align='right'><img src=" . $themedir . "button_locked.gif border=0 alt=Locked></div>");
    }
    print ("</td></tr></table>");

    //------ Print table of posts
    $pc = count($res[0]);
    $pn = 0;
    $lpr = DB::fetchColumn('
        SELECT lastpostread FROM forum_readposts WHERE userid = '
            . $CURUSER["id"] . ' AND topicid = ' . $topicid);
    if (is_null($lpr)) {
        DB::query("REPLACE INTO forum_readposts (userid, topicid) VALUES($userid, $topicid)");
    }

    // posts
    foreach ($res as $arr) {
        ++$pn;
        $postid = $arr["id"];
        $posterid = $arr["userid"];
        $added = $arr["added"] . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]))) . " ago)";

        //---- Get poster details
        $forumposts = DB::fetchColumn("SELECT COUNT(*) FROM forum_posts WHERE userid = $posterid");

        $arr2 = DB::fetchAssoc("SELECT * FROM users WHERE id = $posterid");
        $postername = $arr2["username"];

        if ($postername == "") {
            $by = "Deluser";
            $title = "Deleted Account";
            $privacylevel = "strong";
            $usersignature = " ";
            $userdownloaded = "0";
            $useruploaded = "0";
            $avatar = "";
            $nposts = "-";
            $tposts = "-";
        } else {
            $avatar = h($arr2["avatar"]);
            $userdownloaded = mksize($arr2["downloaded"]);
            $useruploaded = mksize($arr2["uploaded"]);
            $privacylevel = $arr2["privacy"];
            $usersignature = format_comment($arr2["signature"]);
            if ($arr2["downloaded"] > 0) {
                $userratio = number_format($arr2["uploaded"] / $arr2["downloaded"], 2);
            } elseif ($arr2["uploaded"] > 0) {
                $userratio = "Inf.";
            } else {
                $userratio = "---";
            }

            if (!$arr2["country"]) {
                $usercountry = "unknown";
            } else {
                $arr4 = DB::fetchAssoc("SELECT name, flagpic FROM countries WHERE id = $arr2[country] LIMIT 1");
                $usercountry = $arr4["name"];
            }

            $title = strip_tags($arr2["title"]);
            $donated = $arr2['donated'];
            $by = "<a href=account-details.php?id=$posterid><b>$postername</b></a>"
                . ($donated > 0 ? "<img src=".$GLOBALS['SITEURL']."/images/star.gif alt='Donated'>" : "") . "";
        }

        if (!$avatar)
            $avatar = $GLOBALS['SITEURL']."/images/default_avatar.gif";
        print("<a name=$postid></a>\n");

        if ($pn == $pc) {
            print("<a name=last></a>\n");
            if ($postid > $lpr) {
                DB::query("UPDATE forum_readposts SET lastpostread = $postid WHERE userid = $userid AND topicid = $topicid");
            }
        }
//working here

        print("<table align=center cellpadding=3 cellspacing=0 style='border-collapse: collapse'
            bordercolor=646262 width=100% border=1 bgcolor=#E0F1FE>
        <tr><td width=150 align=center>$by<td with=100% align=left><small>Posted at $added </small></tr></table>");

        print("<table align=center cellpadding=3 cellspacing=0 style='border-collapse: collapse' bordercolor=#646262
            width=100% border=1>\n");

        $body = format_comment($arr["body"]);

        // bad word censor
        if ($CENSORWORDS) {
            // todo: subquery
            $res = DB::query('SELECT * FROM censor');
            while ($row = $res->fetch()) {
                $body = str_replace($row['word'], $row['censor'], $body);
            }
        }
        // censor end

        if (is_valid_id($arr['editedby'])) {
            $arr2 = DB::fetchAssoc("SELECT username FROM users WHERE id = $arr[editedby]");

            if ($arr2) {
                // edited by comment out if needed
                $body .= "<br><br><font size=1 class=small><i>Last edited by 
                    <a href=account-details.php?id=$arr[editedby]>$arr2[username]</b></a> on $arr[editedat]</i></font><br>\n";
                $body .= "\n";
            }
        }

        $quote = h($arr["body"]);

        // $postcount1 = DB::fetchColumn("SELECT COUNT(forum_posts.userid) FROM forum_posts WHERE id = $posterid");

        if  ($privacylevel == "strong" && get_user_class() < UC_JMODERATOR){//hide stats, but not from staff
            $useruploaded = "---";
            $userdownloaded = "---";
            $userratio = "---";
            $nposts = "-";
            $tposts = "-";
        }
        print ("<tr valign=top><td width=150 align=left><center><i>$title</i></center>
        <br><center><img width=80 height=80 src=\"$avatar\"></center>
        <br>Uploaded: $useruploaded<br>Downloaded: $userdownloaded
        <br>Posts: $forumposts<br><br>Ratio: $userratio<br>Location: $usercountry
        <br><br></td>");

        print ("<td class=comment>$body<br>");

        if (!$usersignature) {
            print("<br><br></td></tr>\n");
        } else {
            print("<br><br>---------------<br>$usersignature</td></tr>\n");
        }

        print("</table>\n");

        print("<table align=center cellpadding=3 cellspacing=0 style='border-collapse: collapse'
            bordercolor=646262 width=100% border=1 bgcolor=#E0F1FE>
            <tr><td width=150 align=center><nobr> <a href=account-details.php?id=$posterid><img src=".
                $themedir."icon_profile.gif border=0></a> <a href=account-inbox.php?receiver=$postername><img src=".
                $themedir."icon_pm.gif border=0></a> </nobr><td with=100%>");

        print ("<div style='float: left;'><a href=report.php?forumid=$topicid&forumpost=$postid><img src=".
        $themedir."p_report.gif border='0' alt='Report This Post'></a>&nbsp;<a href='javascript:scroll(0,0);'><img src=".
        $themedir."p_up.gif border='0' alt='Go to the top of the page'></a></div><div align=right>");

    //define buttons and who can use them
    if ($CURUSER["id"] == $posterid || get_user_class() >= UC_JMODERATOR){
        print ("<a href='forums.php?action=editpost&postid=$postid'><img src=".$themedir."p_edit.gif border='0' ></a>&nbsp;");
    }
    if (get_user_class() >= UC_JMODERATOR){
        print ("<a href='forums.php?action=deletepost&postid=$postid&sure=0'>
        <img src=".$themedir."p_delete.gif border='0' ></a>&nbsp;");
    }
    if (!$locked){
        print ("<a href=\"javascript:Smilies('[quote] $quote [/quote]')\"><img src=".$themedir."p_quote.gif border='0' ></a>&nbsp;");
        print ("<a href='#bottom'><img src=".$themedir."p_reply.gif border='0' ></a>");
    }
        print("&nbsp;</div></td></tr></table>");
        print("</p>\n");// post seperate
    }
//-------- end posts table ---------//
    print($pagemenu);

    //quick reply
    if (!$locked) {
    //begin_frame("Reply", $newtopic = false);
    print ("<table align=center cellpadding=3 cellspacing=0 style='border-collapse: collapse' bordercolor=646262 width=100%
        border=1 bgcolor=#E0F1FE><TR><TD><BR><CENTER><B>POST REPLY</B></CENTER><BR>");
    $newtopic = false;
    print("<a name=\"bottom\"></a>");
    print("<form name=Form method=post action=?action=post>\n");
    if ($newtopic)
        print("<input type=hidden name=forumid value=$id>\n");
    else
        print("<input type=hidden name=topicid value=$topicid>\n");

    print("<center><table border=0 cellspacing=0 cellpadding=0>");
    if ($newtopic)
        print("<tr><td class=alt2>Subject</td><td class=alt1 align=left style='padding: 0px'><input type=text size=100
            maxlength=$maxsubjectlength name=subject style='border: 0px; height: 19px'></td></tr>\n");

    print("<tr><td>");
    quickbb();
    quicktags();
    print("</td><td width=10>&nbsp;</td><td align=left><textarea name=body cols=60 rows=10></textarea></td>\n");
    print("<td>&nbsp;</td></tr>\n");
    print("<tr><td colspan=3 align=center><br><input type=image class=btn src=".$themedir."button_reply.gif border=0></td></tr>\n");
    print("</table></form></center>\n");
    //end_frame();
    print ("</TD></TR></TABLE>");
    } else {
        print ("<CENTER><img src=".$themedir."button_locked.gif alt=Locked></CENTER>");
    }
    //end quick reply

    if ($locked)
        print("<p>This topic is locked; no new posts are allowed.</p>\n");
    else {
      $arr = get_forum_access_levels($forumid) or die;
      if (get_user_class() < $arr["write"])
        print("<p><i>You are not permitted to post in this forum.</i></p>\n");
      else
        $maypost = true;
    }

    //insert page numbers and quick jump

   // insert_quick_jump_menu($forumid);

    // MODERATOR OPTIONS
    if (get_user_class() >= UC_JMODERATOR) {
        end_frame();
        begin_frame("Moderator Options");
        $res = DB::query("SELECT id, name, minclasswrite FROM forum_forums ORDER BY name");
        print("<table border=0 cellspacing=0 cellpadding=0>\n");
        print("<form method=post action=forums.php?action=renametopic>\n");
        print("<input type=hidden name=topicid value=$topicid>\n");
        print("<input type=hidden name=returnto value=$HTTP_SERVER_VARS[REQUEST_URI]>\n");
        print("<tr><td class=embedded align=right>Rename topic:</td><td class=embedded>
                <input type=text name=subject size=60 maxlength=$maxsubjectlength value=\"" . h($subject) . "\">\n");
        print("<input type=submit value='Apply'></td></tr>");
        print("</form>\n");
        print("<form method=post action=forums.php?action=movetopic&topicid=$topicid>\n");
        print("<tr><td class=embedded align=right>Move this thread to:&nbsp;</td><td class=embedded><select name=forumid>");
        while ($arr = $res->fetch()) {
        if ($arr["id"] != $forumid && get_user_class() >= $arr["minclasswrite"])
            print("<option value=" . $arr["id"] . ">" . $arr["name"] . "\n");
        }
        print("</select> <input type=submit value='Apply'></form></td></tr>\n");
        print("</table>\n");


        print("<table width=100%><tr><td align=center>\n");
            if ($locked)
                print("Locked: <a href=forums.php?action=unlocktopic&forumid=$forumid&topicid=$topicid&page=$page title='Unlock'>
                    <img src=". $themedir ."topic_unlock.gif border=0 alt=UnLock Topic></a>\n");
            else
                print("Locked: <a href=forums.php?action=locktopic&forumid=$forumid&topicid=$topicid&page=$page title='Lock'>
                    <img src=". $themedir ."topic_lock.gif border=0 alt=Lock Topic></a>\n");
            print("Delete Entire Topic: <a href=forums.php?action=deletetopic&topicid=$topicid&sure=0 title='Delete'><img src=".
                $themedir ."topic_delete.gif border=0 alt=Delete Topic></a>\n");
            if ($sticky)
               print("Sticky: <a href=forums.php?action=unsetsticky&forumid=$forumid&topicid=$topicid&page=$page title='UnStick'>
                <img src=". $themedir ."folder_sticky_new.gif border=0 alt=UnStick Topic></a>\n");
            else
               print("Sticky: <a href=forums.php?action=setsticky&forumid=$forumid&topicid=$topicid&page=$page title='Stick'>
                <img src=". $themedir ."folder_sticky.gif border=0 alt=Stick Topic></a>\n");
            print("</td></tr></table>\n");
    }
    end_frame();

    stdfoot();
    die;
}

///////////////////////////////////////////////////////// Action: REPLY
if ($action == "reply") {
    $topicid = $_GET["topicid"];
    if (!is_valid_id($topicid))
        die('invalid id');
    stdhead("Post reply");
    begin_frame("Post reply");
    insert_compose_frame($topicid, false);
    end_frame();
    stdfoot();
    die;
}

///////////////////////////////////////////////////////// Action: MOVE TOPIC
if ($action == "movetopic") {
    $forumid = (int) ($_POST["forumid"] ?? 0);
    $topicid = (int) ($_GET["topicid"] ?? 0);
    if (!is_valid_id($forumid) || !is_valid_id($topicid) || get_user_class() < UC_JMODERATOR) {
        die('invalid id or not perms');
    }

    // Make sure topic and forum is valid
    $minclasswrite = DB::fetchColumn("SELECT minclasswrite FROM forum_forums WHERE id = $forumid");
    if (! $res) {
        showerror("Error", "Forum not found.");
    }
    if (get_user_class() < $minclasswrite)
        die('not perms');
    $arr = DB::fetchAssoc("SELECT subject, forumid FROM forum_topics WHERE id = $topicid");
    if (! $arr)
        showerror("Error", "Topic not found.");
    if ($arr["forumid"] != $forumid)
        DB::executeUpdate("UPDATE forum_topics SET forumid = $forumid, moved = 'yes' WHERE id = $topicid");

    // Redirect to forum page
    header("Location: {$GLOBALS['SITEURL']}/forums.php?action=viewforum&forumid=$forumid");
    die;
}

///////////////////////////////////////////////////////// Action: DELETE TOPIC
if ($action === 'deletetopic') {
    $topicid = (int) ($_GET['topicid'] ?? 0);
    if (!is_valid_id($topicid) || get_user_class() < UC_JMODERATOR) {
        die('invalid id or not perms');
    }

    $sure = (int) ($_GET["sure"] ?? 0);
    if ($sure === 0) {
        showerror("Delete topic", "Sanity check: You are about to delete a topic. Click 
            <a href=forums.php?action=deletetopic&topicid=$topicid&sure=1>here</a> if you are sure.");
    }

    DB::query("DELETE FROM forum_topics WHERE id = $topicid");
    DB::query("DELETE FROM forum_posts WHERE topicid = $topicid");
    header("Location: {$GLOBALS['SITEURL']}/forums.php");
    die;
}

///////////////////////////////////////////////////////// Action: EDIT TOPIC
if ($action === 'editpost') {
    $postid = (int) ($_GET['postid'] ?? 0);
    if (!is_valid_id($postid))
        die('bad id');
    $arr = DB::fetchAssoc('SELECT * FROM forum_posts WHERE id = '.$postid);
    if (! $arr)
        showerror('Error', 'No post with ID $postid.');
    if ($CURUSER['id'] != $arr['userid'] && get_user_class() < UC_JMODERATOR)
        showerror('Error', 'Denied!');

    if ($HTTP_SERVER_VARS['REQUEST_METHOD'] === 'POST') {
        $body = $_POST['body'];
        if ($body === '')
            showerror('Error', 'Body cannot be empty!');

        $editedat = get_date_time();
        DB::executeUpdate('UPDATE forum_posts SET body = ?, editedat = ?, editedby = ? WHERE id = ' . $postid,
            [$body, $editedat, $CURUSER['id']]
        );
        $returnto = $HTTP_POST_VARS['returnto'];
        if ($returnto != '')
            header('Location: '.$returnto);
        else
            showerror('Success', 'Post was edited successfully.');
    }

    stdhead();

    begin_frame('Edit Post');
    print("<form name=Form method=post action=?action=editpost&postid=$postid>\n");
    print("<input type=hidden name=returnto value=\"" . h($HTTP_SERVER_VARS["HTTP_REFERER"]) . "\">\n");
    print("<center><table border=0 cellspacing=0 cellpadding=5>\n");
    print("<tr><td>\n");
    quicktags();
    print("</td><td style='padding: 0px'><textarea name=body cols=50 rows=20 >"
        . h($arr["body"]) . "</textarea></td></tr>\n");
    print("<tr><td align=center colspan=2><input type=submit value='Submit Changes' class=btn></td></tr>\n");
    print("</table></center>\n");
    print("</form>\n");
    end_frame();
    stdfoot();
    die;
}

///////////////////////////////////////////////////////// Action: DELETE POST
if ($action == "deletepost") {
    $postid = $_GET["postid"];
    $sure = $_GET["sure"];
    if (get_user_class() < UC_JMODERATOR || !is_valid_id($postid))
        die;

    // SURE?
    if ($sure == "0") {
        showerror("Delete post", "Sanity check: You are about to delete a post. ".
            "Click <a href=forums.php?action=deletepost&postid=$postid&sure=1>here</a> if you are sure.");
    }

    // Get topic id
    $topicid = DB::fetchColumn("SELECT topicid FROM forum_posts WHERE id = $postid");
    if (! $topicid) {
        showerror("Error", "Post not found");
    }

    //------- We can not delete the post if it is the only one of the topic
    $fnumposts = DB::fetchColumn("SELECT COUNT(*) FROM forum_posts WHERE topicid = $topicid");
    if ($fnumposts < 2) {
        showerror("Error", "Can't delete post; it is the only post of the topic. ".
            "You should <a href=forums.php?action=deletetopic&topicid=$topicid&sure=1>delete the topic</a> instead.\n");
    }

    //------- Delete post
    DB::query("DELETE FROM forum_posts WHERE id = $postid");

    //------- Update topic
    update_topic_last_post($topicid);
    header("Location: {$GLOBALS['SITEURL']}/forums.php?action=viewtopic&topicid=$topicid");
    die;
}

///////////////////////////////////////////////////////// Action: LOCK TOPIC
if ($action == "locktopic") {
    $forumid = intget('forumid');
    $topicid = intget('topicid');
    $page = intget('page');
    if (!is_valid_id($topicid) || get_user_class() < UC_JMODERATOR)
        die;
    DB::ecxequteUpdate("UPDATE forum_topics SET locked = 'yes' WHERE id = $topicid");
    header("Location: {$GLOBALS['SITEURL']}/forums.php?action=viewforum&forumid=$forumid&page=$page");
    die;
}

///////////////////////////////////////////////////////// Action: UNLOCK TOPIC
if ($action == "unlocktopic") {
    $forumid = intget('forumid');
    $topicid = intget('topicid');
    $page = intget('page');
    if (!is_valid_id($topicid) || get_user_class() < UC_JMODERATOR)
        die;
    DB::ecxequteUpdate("UPDATE forum_topics SET locked = 'no' WHERE id = $topicid");
    header("Location: {$GLOBALS['SITEURL']}/forums.php?action=viewforum&forumid=$forumid&page=$page");
    die;
}

///////////////////////////////////////////////////////// Action: STICK TOPIC
if ($action == "setsticky") {
    $forumid = intget('forumid');
    $topicid = intget('topicid');
    $page = intget('page');
    if (!is_valid_id($topicid) || get_user_class() < UC_JMODERATOR)
        die;
    DB::ecxequteUpdate("UPDATE forum_topics SET sticky = 'yes' WHERE id = $topicid");
    header("Location: {$GLOBALS['SITEURL']}/forums.php?action=viewforum&forumid=$forumid&page=$page");
    die;
}

///////////////////////////////////////////////////////// Action: UNSTICK TOPIC
if ($action == "unsetsticky") {
    $forumid = intget('forumid');
    $topicid = intget('topicid');
    $page = intget('page');
    if (!is_valid_id($topicid) || get_user_class() < UC_JMODERATOR)
        die;
    DB::ecxequteUpdate("UPDATE forum_topics SET sticky = 'no' WHERE id = $topicid");
    header("Location: {$GLOBALS['SITEURL']}/forums.php?action=viewforum&forumid=$forumid&page=$page");
    die;
}

///////////////////////////////////////////////////////// Action: RENAME TOPIC
if ($action == 'renametopic') {
    if (get_user_class() < UC_JMODERATOR)
        die;
    $topicid = (int) ($_POST['topicid'] ?? 0);
    if (!is_valid_id($topicid))
        die;
    $subject = $_POST['subject'] ?? '';
    if ($subject == '')
        showerror('Error', 'You must enter a new title!');
    DB::ecxequteUpdate("UPDATE forum_topics SET subject = ? WHERE id = $topicid", [$subject]);
    $returnto = $HTTP_POST_VARS['returnto'];
    if ($returnto)
        header("Location: $returnto");
    die;
}

///////////////////////////////////////////////////////// Action: VIEW FORUM
if ($action == "viewforum") {
    $forumid = intget('forumid');
    if (!is_valid_id($forumid))
        die('ivalid id - ' . $forumid);
    $page = intget('page');
    $userid = $CURUSER["id"];

    //------ Get forum name
    $arr = DB::fetchAssoc("SELECT name, minclassread FROM forum_forums WHERE id = $forumid");
    if (! $arr) {
        die;
    }
    $forumname = $arr["name"];
    if (get_user_class() < $arr["minclassread"])
        die("Not permitted");

    //------ Get topic count
    $perpage = 20;
    $num = DB::fetchColumn("SELECT COUNT(*) FROM forum_topics WHERE forumid = $forumid");
    if ($page == 0)
      $page = 1;
    $first = ($page * $perpage) - $perpage + 1;
    $last = $first + $perpage - 1;
    if ($last > $num)
      $last = $num;
    $pages = floor($num / $perpage);
    if ($perpage * $pages < $num)
      ++$pages;

    //------ Build menu
    $menu = "<p align=center><b>\n";
    $lastspace = false;
    for ($i = 1; $i <= $pages; ++$i) {
      if ($i == $page)
        $menu .= "<font class=gray>$i</font>\n";
      elseif ($i > 3 && ($i < $pages - 2) && ($page - $i > 3 || $i - $page > 3)) {
        if ($lastspace)
          continue;
           $menu .= "... \n";
        $lastspace = true;
      }
      else {
        $menu .= "<a href=forums.php?action=viewforum&forumid=$forumid&page=$i>$i</a>\n";
        $lastspace = false;
      }
      if ($i < $pages)
        $menu .= "</b>|<b>\n";
    }
    $menu .= "<br>\n";
    if ($page == 1)
      $menu .= "<font class=gray>&lt;&lt; Prev</font>";
    else
      $menu .= "<a href=forums.php?action=viewforum&forumid=$forumid&page=" . ($page - 1) . ">&lt;&lt; Prev</a>";
    $menu .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    if ($last == $num)
      $menu .= "<font class=gray>Next &gt;&gt;</font>";
    else
      $menu .= "<a href=forums.php?action=viewforum&forumid=$forumid&page=" . ($page + 1) . ">Next &gt;&gt;</a>";
    $menu .= "</b></p>\n";
    $offset = $first - 1;

    //------ Get topics data and display category
    $topicsres = DB::fetchAll("
        SELECT *
        FROM forum_topics
        WHERE forumid = $forumid
        ORDER BY sticky, lastpost DESC
        LIMIT $offset, $perpage");

    stdhead("Forum : $forumname");

    begin_frame("$forumname", 'center');
    forumheader("<a href=forums.php?action=viewforum&forumid=$forumid>$forumname</a>");

    print ("
        <table align=center cellpadding=0 cellspacing=5 width=95% border=0 ><tr>
        <td><div align='right'><a href=forums.php?action=newtopic&forumid=$forumid>
            <img src=". $themedir. "button_new_post.gif border=0></a></div></td></tr></table>");

    if ($topicsres) {
        print("<table align=center cellpadding=2 cellspacing=1 style='border-collapse: collapse' bordercolor=#646262 width=100% border=1 >");

        print("<tr><td align=left width=100% bgcolor=#E0F1FE><b>Topic</b></td><td bgcolor=#E0F1FE align=center><b>Replies</b></td><td bgcolor=#E0F1FE align=center><b>Views</b></td><td bgcolor=#E0F1FE align=center><b>Author</b></td><td bgcolor=#E0F1FE align=right><b>Last post</b></td>\n");
    if (get_user_class() >= UC_JMODERATOR) {
            print("<td bgcolor=#E0F1FE><b>Moderator</b></td>");
        }
        print("</tr>\n");
        foreach ($topicsres as $topicarr) {
            // todo: sub queries!

            $topicid = $topicarr["id"];
            $topic_userid = $topicarr["userid"];
            $locked = $topicarr["locked"] == "yes";
            $moved = $topicarr["moved"] == "yes";
            $sticky = $topicarr["sticky"] == "yes";
            //---- Get reply count
            // todo: subquery
            $posts = DB::fetchColumn("SELECT COUNT(*) FROM forum_posts WHERE topicid = $topicid");
            $replies = max(0, $posts - 1);
            $tpages = floor($posts / $postsperpage);
            if ($tpages * $postsperpage != $posts)
                ++$tpages;
            if ($tpages > 1) {
                $topicpages = " (<img src=". $GLOBALS['SITEURL'] ."/images/multipage.gif>";
                for ($i = 1; $i <= $tpages; ++$i)
                    $topicpages .= " <a href=forums.php?action=viewtopic&topicid=$topicid&page=$i>$i</a>";
                $topicpages .= ")";
            }
            else {
                $topicpages = "";
            }

            //---- Get userID and date of last post
            $arr = DB::fetchAssoc("SELECT * FROM forum_posts WHERE topicid = $topicid ORDER BY id DESC LIMIT 1");
            $lppostid = $arr["id"];
            $lpuserid = (int) $arr["userid"];
            $lpadded = $arr["added"];

            //------ Get name of last poster
            $arr = DB::fetchAssoc("SELECT * FROM users WHERE id = $lpuserid");
            if ($arr) {
                $lpusername = "<a href=account-details.php?id=$lpuserid>$arr[username]</a>";
            } else {
                $lpusername = "Deluser";
            }

            //------ Get author
            $arr = DB::fetchAssoc("SELECT username FROM users WHERE id = $topic_userid");
            if ($arr) {
                $lpauthor = "<a href=account-details.php?id=$topic_userid>$arr[username]</a>";
            } else {
                $lpauthor = "Deluser";
            }

            // Topic Views
            $views = DB::fetchColumn("SELECT views FROM forum_topics WHERE id = $topicid LIMIT 1");
            // End

            //---- Print row
            $a = DB::fetchColumn("SELECT lastpostread FROM forum_readposts WHERE userid = $userid AND topicid = $topicid LIMIT 1");
            $new = !$a || $lppostid > $a;
            $topicpic = ($locked ? ($new ? "folder_locked_new" : "folder_locked") : ($new ? "folder_new" : "folder"));
            $subject = ($sticky ? "<b>Sticky: </b>" : "") . "<a href=forums.php?action=viewtopic&topicid=$topicid><b>" .
                encodehtml($topicarr["subject"]) . "</b></a>$topicpages";
            print("<tr><td align=left class=alt1><table border=0 cellspacing=0 cellpadding=0><tr>" .
                "<td style='padding-right: 5px'><img src=". $themedir ."$topicpic.gif>" .
                "</td><td class=alt1 align=left>\n" .
                "$subject</td></tr></table></td><td class=alt2 align=center>$replies</td>\n" .
                "<td class=alt1 align=center>$views</td>\n" .
                "<td class=alt1 align=center>$lpauthor</td>\n" .
                "<td class=alt2 align=right><nobr><small>by&nbsp;$lpusername<br>$lpadded</small></nobr></td>\n");
            if (get_user_class() >= UC_JMODERATOR) {
                print("<td class=alt1 align=center>\n");
                if ($locked)
                    print("<a href=forums.php?action=unlocktopic&forumid=$forumid&topicid=$topicid&page=$page title='Unlock'>
                        <img src=". $themedir ."topic_unlock.gif border=0 alt=UnLock Topic></a>\n");
                else
                    print("<a href=forums.php?action=locktopic&forumid=$forumid&topicid=$topicid&page=$page title='Lock'><img src=".
                        $themedir ."topic_lock.gif border=0 alt=Lock Topic></a>\n");
                print("<a href=forums.php?action=deletetopic&topicid=$topicid&sure=0 title='Delete'><img src=".
                        $themedir ."topic_delete.gif border=0 alt=Delete Topic></a>\n");
                if ($sticky)
                    print("<a href=forums.php?action=unsetsticky&forumid=$forumid&topicid=$topicid&page=$page title='UnStick'>
                        <img src=".$themedir ."folder_sticky_new.gif border=0 alt=UnStick Topic></a>\n");
                else
                    print("<a href=forums.php?action=setsticky&forumid=$forumid&topicid=$topicid&page=$page title='Stick'><img src=".
                        $themedir ."folder_sticky.gif border=0 alt=Stick Topic></a>\n");
                print("</td>\n");
            }
            print("</tr>\n");
        } // foreach
        //   end_table();
        print("</table>");
        print($menu);
    } // if
    else {
        print("<p align=center>No topics found</p>\n");
    }
    print("<p><table border=0 cellspacing=0 cellpadding=0><tr valing=center>\n");
    print("<td ><img src=". $themedir ."folder_new.gif style='margin-right: 5px'></td><td >New posts</td>\n");
    print("<td ><img src=". $themedir ."folder.gif style='margin-left: 10px; margin-right: 5px'>" .
        "</td><td >No New posts</td>\n");
    print("<td ><img src=". $themedir ."folder_locked.gif style='margin-left: 10px; margin-right: 5px'>" .
        "</td><td >Locked topic</td>\n");
    print("</tr></table></p>\n");
    $arr = get_forum_access_levels($forumid) or die;
    $maypost = get_user_class() >= $arr["write"];
    if (!$maypost)
    print("<p><i>You are not permitted to post in this forum.</i></p>\n");
    print("<p><table border=0 cellspacing=0 cellpadding=0><tr>\n");

    if ($maypost)
    print("<td ><a href=forums.php?action=newtopic&forumid=$forumid><img src="
        . $themedir . "button_new_post.gif border=0></a></td>\n");
    print("</tr></table>\n");
    insert_quick_jump_menu($forumid);
    end_frame();
    stdfoot();
    die;
}

///////////////////////////////////////////////////////// Action: VIEW NEW POSTS
if ($action === 'viewunread') {
    $userid = $CURUSER['id'];
    $maxresults = 25;
    $res = DB::query("SELECT id, forumid, subject, lastpost FROM forum_topics ORDER BY lastpost");
    stdhead();
    begin_frame("Topics with unread posts");
    forumheader("New Topics");

    $n = 0;
    $uc = get_user_class();
    while ($arr = $res->fetch()) {
        $topicid = $arr['id'];
        $forumid = $arr['forumid'];

        // Check if post is read
        $r = DB::fetchColumn("SELECT lastpostread FROM forum_readposts WHERE userid = $userid AND topicid = $topicid");
        if ($r && $r == $arr['lastpost']) {
            continue;
        }

        // Check access & get forum name
        $a = DB::fetchAssoc("SELECT name, minclassread FROM forum_forums WHERE id = $forumid");
        if ($uc < $a['minclassread']) {
              continue;
        }
        ++$n;
        if ($n > $maxresults)
            break;
        $forumname = $a['name'];
        if ($n == 1) {
            print("<center><table border=1 cellspacing=0 cellpadding=5 width=95%>\n");
            print("<tr><td class=alt3 align=left>Topic</td><td class=alt3 align=left>Forum</td></tr>\n");
        }
        print("<tr><td align=left><table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>" .
            "<img src=". $GLOBALS['SITEURL'] ."/images/unlockednew.gif style='margin-right: 5px'></td><td class=embedded>" .
            "<a href=forums.php?action=viewtopic&topicid=$topicid&page=last#last><b>"
            . h($arr["subject"]) ."</b></a></td></tr></table></td>
            <td align=left><a href=forums.php?action=viewforum&amp;forumid=$forumid><b>$forumname</b></a></td></tr>\n");
    }
    if ($n > 0) {
        print("</table>\n");
        if ($n > $maxresults)
            print("<p>More than $maxresults items found, displaying first $maxresults.</p>\n");
        print("<p><a href=forums.php?catchup><b>Mark All Forums Read.</b></a></center><br></p>\n");
    } else {
        print("<b>Nothing found</b>");
    }
    end_frame();
    stdfoot();
    die;
}

///////////////////////////////////////////////////////// Action: SEARCH
if ($action == "search") {
    stdhead("Forum Search");
    begin_frame("Search Forum");
    forumheader("Search Forums");

    $keywords = trim($_GET["keywords"] ?? '');

    if ($keywords != '') {
        print("<p>Search Phrase: <b>" . h($keywords) . "</b></p>\n");
        $maxresults = 50;
        $res = DB::fetchAll("SELECT * FROM forum_posts WHERE MATCH (body) AGAINST (?)", [$keywords]);

        // search and display results...
        $num = count($res[0] ?? []);

        if ($num > $maxresults) {
            $num = $maxresults;
            print("<p>Found more than $maxresults posts; displaying first $num.</p>\n");
        }

        if ($num == 0) {
            print("<p><b>Sorry, nothing found!</b></p>");
        } else {
            print("<p><center><table border=1 cellspacing=0 cellpadding=2 width=95%>\n");
            print("<tr><td class=colhead>Post ID</td>"
                    . "<td class=colhead align=left>Topic</td>"
                    . "<td class=colhead align=left>Forum</td>"
                    . "<td class=colhead align=left>Posted by</td></tr>\n");

            foreach ($res as $post) {
                // todo: subquery
                $topic = DB::fetchAssoc("SELECT forumid, subject FROM forum_topics WHERE id=$post[topicid]");

                $forum = DB::fetchAssoc("SELECT name,minclassread FROM forum_forums WHERE id=$topic[forumid]");

                if ($forum["name"] == "" || $forum["minclassread"] > $CURUSER["class"])
                    continue;

                $user = DB::fetchAssoc("SELECT username FROM users WHERE id=$post[userid]");

                if ($user["username"] == "")
                    $user["username"] = "Deluser";
                print("<tr><td>$post[id]</td>"
                    . "<td align=left><a href=forums.php?action=viewtopic&topicid=$post[topicid]#$post[id]><b>"
                    . h($topic["subject"]) . "</b></a></td>"
                    . "<td align=left><a href=forums.php?action=viewforum&amp;forumid=$topic[forumid]><b>"
                    . h($forum["name"]) . "</b></a>"
                    . "<td align=left><a href=account-details.php?id=$post[userid]><b>$user[username]</b></a>"
                    . "<br>at $post[added]</tr>\n");
            }
            print("</table></center></p>\n");
            print("<p><b>Search again</b></p>\n");
        }
    }

    print("<center><form method=get action=?>\n");
    print("<input type=hidden name=action value=search>\n");
    print("<table border=0 cellspacing=0 cellpadding=5>\n");
    print("<tr><td valign=bottom align=right>Search For: </td><td align=left><input type=text size=40 name=keywords><br></td></tr>\n");
    print("<tr><td colspan=2 align=center><input type=submit value='Search' class=btn></td></tr>\n");
    print("</table>\n</form></center>\n");
    end_frame();
    stdfoot();
    die;
}

// Action: UNKNOWN
if ($action != '')
    showerror("Forum Error", "Unknown action '$action'.");

// Action: DEFAULT ACTION (VIEW FORUMS)
if (isset($_GET["catchup"]))
    catch_up();

// Action: SHOW MAIN FORUM INDEX
$forums_res = DB::query('
    SELECT forumcats.id AS fcid, forumcats.name AS fcname, forum_forums.*
    FROM forum_forums
        LEFT JOIN forumcats ON forumcats.id = forum_forums.category
    ORDER BY forumcats.sort, forum_forums.sort, forum_forums.name');

stdhead("Forums");
begin_frame("Forum Home", 'center');
forumheader("Index");
latestforumposts();

print("<table align=center cellpadding=3 cellspacing=1
    style='border-collapse: collapse' bordercolor=#646262 width=100% border=1 >");// MAIN LAYOUT

print("<tr><td align=left width=100% bgcolor=#E0F1FE><b> Forum </b></td>
<td  width=37 align=right bgcolor=#E0F1FE><b> Topics <b/></td>
<td width=47 align=right bgcolor=#E0F1FE><b> Posts </b></td>
<td align=right width=85 bgcolor=#E0F1FE><b> Last post </b></td></tr>\n");// head of forum index

$fcid = 0;

while ($forums_arr = $forums_res->fetch()) {
    if ($forums_arr['fcid'] != $fcid) {// add forum cat headers
        print("<tr><td colspan=\"4\" class=\"forumcat\" align=center bgcolor=#E0F1FE><b><font size=\"2\">"
            .h($forums_arr['fcname'])."</font></b></td></tr>\n");

        $fcid = $forums_arr['fcid'];
    }

    if (get_user_class() < $forums_arr["minclassread"])
        continue;

    // ??? teamid not found in other files ???
    // if ($forums_arr["teamid"] != 0 && $CURUSER["team"]! = $forums_arr["teamid"] && get_user_class() < 4)
        // continue;

    $forumid = 0 + $forums_arr["id"];

    $forumname = h($forums_arr["name"]);

    $forumdescription = h($forums_arr["description"]);
    $topicids_res = DB::query("SELECT id FROM forum_topics WHERE forumid = $forumid");
    $topiccount = 0;
    $postcount = 0;
    while ($topicids_arr = $topicids_res->fetch()) {
        $topiccount += 1;
        $postcount += DB::fetchColumn('SELECT COUNT(*) FROM forum_posts WHERE topicid = ' . $topicids_arr['id']);
    }
    $topiccount = number_format($topiccount);
    $postcount = number_format($postcount);

    // Find last post ID
    $lastpostid = get_forum_last_post($forumid);

    // Get last post info
    $post_arr = DB::fetchAssoc("SELECT added, topicid, userid FROM forum_posts WHERE id = $lastpostid");
    if ($post_arr) {
        $lastposterid = $post_arr["userid"];
        $lastpostdate = $post_arr["added"];
        $lasttopicid = $post_arr["topicid"];
        $user_arr = DB::fetchAssoc("SELECT username FROM users WHERE id=$lastposterid");
        $lastposter = h($user_arr['username']);
        $topic_arr = DB::fetchAssoc("SELECT subject FROM forum_topics WHERE id=$lasttopicid");
        $lasttopic = h($topic_arr['subject']);

        //cut last topic
        $latestleng = 10;

    $lastpost = "<nobr><small><a href=forums.php?action=viewtopic&topicid=$lasttopicid&page=last#last>" .
        CutName($lasttopic, $latestleng) . "</a> by <a href=account-details.php?id=$lastposterid>$lastposter</a><br>$lastpostdate</small></nobr>";


    $a = DB::fetchColumn("SELECT lastpostread FROM forum_readposts WHERE userid=$CURUSER[id] AND topicid=$lasttopicid LIMIT 1");
    //define the images for new posts or not on index
    if ($a && $a == $lastpostid)
            $img = "folder";
    else
            $img = "folder_new";
    } else {
    $lastpost = "<small>No Posts</small>";
    $img = "folder";
    }
    //following line is each forums display
    print("<tr><td class=alt1 align=left><table border=0 cellspacing=0 cellpadding=0><tr><td style='padding-right: 5px'><img src=". $themedir ."$img.gif></td><td class=alt1><a href=forums.php?action=viewforum&forumid=$forumid><b>$forumname</b></a><br>\n" .
    "<SMALL>- $forumdescription</SMALL></td></tr></table></td><td class=alt2 align=center>$topiccount</td></td><td class=alt1 align=center>$postcount</td>" .
    "<td class=alt2 align=right>$lastpost</td></tr>\n");
}
print("</table>");
//forum Key
print("<p><table border=0 cellspacing=0 cellpadding=0><tr valing=center>\n");
print("<td ><img src=". $themedir ."folder_new.gif style='margin-right: 5px'></td><td >New posts</td>\n");
print("<td ><img src=". $themedir ."folder.gif style='margin-left: 10px; margin-right: 5px'></td><td >No New posts</td>\n");
print("<td ><img src=". $themedir ."folder_locked.gif style='margin-left: 10px; margin-right: 5px'></td><td >Locked topic</td>\n");
print("</tr></table></p>\n");

// Top posters
$r = DB::query("
    SELECT users.id, users.username, COUNT(forum_posts.userid) as num
    FROM forum_posts
        LEFT JOIN users ON users.id = forum_posts.userid
    GROUP BY userid
    ORDER BY num DESC
    LIMIT 10");
forumpostertable($r, "Top 10 Posters</font>");

//topic count and post counts
$postcount = number_format(get_row_count("forum_posts"));
$topiccount = number_format(get_row_count("forum_topics"));
print("<br><center>Our members have made " . $postcount . " posts in  " . $topiccount . " topics</center><BR>");

insert_quick_jump_menu();
end_frame();
stdfoot();

} ELSE {//HEY IF FORUMS ARE OFF, SHOW THIS...
    stdhead("Forums");
    begin_frame("Notice", 'center');
    echo '<BR>Unfortunately The Forums Are Not Currently Available<BR><BR>';
    end_frame();
    stdfoot();
}

}//end ban check


