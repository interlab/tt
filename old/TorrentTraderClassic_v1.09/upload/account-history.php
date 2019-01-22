<?
ob_start("ob_gzhandler"); 
require "backend/functions.php";
dbconn(false);
loggedinorreturn();

$userid = (int)$_GET["id"];

if (!is_valid_id($userid))
	bark("Error", "Invalid ID");

//jmodonly();

$page = $_GET["page"];

$action = $_GET["action"];

//-------- Global variables

$perpage = 25;

//-------- Action: View posts

if ($action == "viewposts")
{
	$select_is = "COUNT(DISTINCT p.id)";

	$from_is = "forum_posts AS p JOIN forum_topics as t ON p.id = t.id
	                JOIN forum_forums AS f ON t.forumid = f.id";

	$where_is = "p.userid = $userid AND f.minclassread <= " . $CURUSER['class'];

	$order_is = "p.id DESC";

	$query = "SELECT $select_is FROM $from_is WHERE $where_is";

	$res = mysql_query($query) or sqlerr(__FILE__, __LINE__);

	$arr = mysql_fetch_row($res) or bark("Error", "No posts found");

	$postcount = $arr[0];

	//------ Make page menu

	list($pagertop, $pagerbottom, $limit) = pager($perpage, $postcount, $_SERVER["PHP_SELF"] . "?action=viewposts&id=$userid&");

	//------ Get user data

	$res = mysql_query("SELECT username, donated, warned FROM users WHERE id=$userid") or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) == 1)
	{
  	$arr = mysql_fetch_assoc($res);

	  $subject = "<a href=account-details.php?id=$userid><b>$arr[username]</b></a>".
	  	($arr["donated"] > 1 ? "<img src=images/star.gif alt='Donor' style='margin-left: 4pt'>" : "") .
	  	($arr["warned"] == "yes" ? "<img src=images/warned.gif alt='Warned' style='margin-left: 4pt'>" : "");
	}
	else
	    $subject = "unknown[$userid]";

	//------ Get posts

 	$from_is = "forum_posts AS p JOIN forum_topics as t ON p.topicid = t.id
             JOIN forum_forums AS f ON t.forumid = f.id LEFT JOIN forum_readposts as r
             ON p.topicid = r.topicid AND p.userid = r.userid";

	$select_is = "f.id AS f_id, f.name, t.id AS t_id, t.subject, t.lastpost, r.lastpostread, p.*";

	$query = "SELECT $select_is FROM $from_is WHERE $where_is " .
	         "ORDER BY $order_is $limit";

	$res = mysql_query($query) or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) == 0)
	    bark("Error", "No posts found");

	stdhead("Posts history");

	//print("<h1>Post history for $subject</h1>\n");

//	if ($postcount > $perpage) echo $pagertop;

	//------ Print table

	begin_frame("Post history for $subject");

	if ($postcount > $perpage) echo $pagertop;

	while ($arr = mysql_fetch_assoc($res))
	{
	    $postid = $arr["id"];

	    $posterid = $arr["userid"];

	    $topicid = $arr["t_id"];

	    $topicname = $arr["subject"];

	    $forumid = $arr["f_id"];

	    $forumname = $arr["name"];

	    $newposts = ($arr["lastpostread"] < $arr["lastpost"]) && $CURUSER["id"] == $userid;

	    $added = $arr["added"] . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]))) . " ago)";

	    print("<br><table border=0 cellspacing=0 cellpadding=0 width=95%><tr><td width=100% bgcolor=#66CCFF>
	    <b>Forum:&nbsp;</b>
	    <a href=forums.php?action=viewforum&forumid=$forumid>$forumname</a>
	    &nbsp;--&nbsp;<b>Topic:&nbsp;</b>
	    <a href=forums.php?action=viewtopic&topicid=$topicid>$topicname</a>
      &nbsp;--&nbsp;<b>Post:&nbsp;</b>
      #<a href=forums.php?action=viewtopic&topicid=$topicid&page=p$postid#$postid>$postid</a>" .
      ($newposts ? " &nbsp;<b>(<font color=red>NEW!</font>)</b>" : "") .
	    "&nbsp;--&nbsp;$added</td></tr></table>\n");

	    begin_table(true);

	    $body = format_comment($arr["body"]);

	    if (is_valid_id($arr['editedby']))
	    {
        	$subres = mysql_query("SELECT username FROM users WHERE id=$arr[editedby]");
	        if (mysql_num_rows($subres) == 1)
	        {
	            $subrow = mysql_fetch_assoc($subres);
	            $body .= "<p><font size=1 class=small>Last edited by <a href=userdetails.php?id=$arr[editedby]><b>$subrow[username]</b></a> at $arr[editedat] GMT</font></p>\n";
	        }
	    }

	    print("<tr valign=top><td width=95%>&nbsp;<i>$body</i></td></tr>\n");

	    end_table();
	}

if ($postcount > $perpage) echo $pagerbottom;

	end_frame();


//	if ($postcount > $perpage) echo $pagerbottom;

	stdfoot();

	die;
}

//-------- Action: View comments

if ($action == "viewcomments")
{
	$select_is = "COUNT(*)";

	// LEFT due to orphan comments
	$from_is = "comments AS c LEFT JOIN torrents as t
	            ON c.torrent = t.id";

	$where_is = "c.user = $userid";
	$order_is = "c.id DESC";

	$query = "SELECT $select_is FROM $from_is WHERE $where_is ORDER BY $order_is";

	$res = mysql_query($query) or sqlerr(__FILE__, __LINE__);

	$arr = mysql_fetch_row($res) or bark("Error", "No comments found");

	$commentcount = $arr[0];

	//------ Make page menu

	list($pagertop, $pagerbottom, $limit) = pager($perpage, $commentcount, $_SERVER["PHP_SELF"] . "?action=viewcomments&id=$userid&");

	//------ Get user data

	$res = mysql_query("SELECT username, donated, warned FROM users WHERE id=$userid") or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) == 1)
	{
		$arr = mysql_fetch_assoc($res);

	  $subject = "<a href=account-details.php?id=$userid><b>$arr[username]</b></a>" .
	  	($arr["donated"] > 1 ? "<img src=images/star.gif alt='Donor' style='margin-left: 4pt'>" : "") .
	  	($arr["warned"] == "yes" ? "<img src=images/warned.gif alt='Warned' style='margin-left: 4pt'>" : "");
	}
	else
	  $subject = "unknown[$userid]";

	//------ Get comments

	$select_is = "t.name, c.torrent AS t_id, c.id, c.added, c.text";

	$query = "SELECT $select_is FROM $from_is WHERE $where_is " .
	            "ORDER BY $order_is ".$limit;

	$res = mysql_query($query) or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) == 0) bark("Error", "No comments found");

	stdhead("Comments history");

//	print("<h1>Comments history for $subject</h1>\n");

//	if ($commentcount > $perpage) echo $pagertop;

	//------ Print table

	begin_frame("Comments history for $subject");

	if ($commentcount > $perpage) echo $pagertop;

	while ($arr = mysql_fetch_assoc($res))
	{

		$commentid = $arr["id"];

	  $torrent = $arr["name"];

    // make sure the line doesn't wrap
	  if (strlen($torrent) > 55)
	  	$torrent = substr($torrent,0,52)."...";

	  $torrentid = $arr["t_id"];

	  //find the page; this code should probably be in torrents-details.php instead

	  $subres = mysql_query("SELECT COUNT(*) FROM comments WHERE torrent = $torrentid AND id < $commentid")
	  	or sqlerr(__FILE__, __LINE__);
	  $subrow = mysql_fetch_row($subres);
    $count = $subrow[0];
    $comm_page = floor($count/20);
    $page_url = $comm_page?"&page=$comm_page":"";

	  $added = $arr["added"] . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]))) . " ago)";

	  print("<br><table border=0 cellspacing=0 cellpadding=0 width=95%><tr><td width=100% bgcolor=#66CCFF>".
	  "<b>Torrent:&nbsp;</b>".
	  ($torrent?("<a href=/torrents-details.php?id=$torrentid&tocomm=1>$torrent</a>"):" [Deleted] ").
	  "&nbsp;---&nbsp;<b>Comment:&nbsp;</b>#<a href=/torrents-details.php?id=$torrentid&tocomm=1$page_url>$commentid</a>&nbsp;---&nbsp;$added
	  </td></tr></table>\n");

	  begin_table(true);

	  $body = format_comment($arr["text"]);

	  print("<tr valign=top><td >$body</td></tr>\n");

	  end_table();
	}

	if ($commentcount > $perpage) echo $pagerbottom;

	end_frame();


//	if ($commentcount > $perpage) echo $pagerbottom;

	stdfoot();

	die;
}

//-------- Handle unknown action

if ($action != "")
	bark("History Error", "Unknown action '$action'.");

//-------- Any other case

bark("History Error", "Invalid or no query.");

?>