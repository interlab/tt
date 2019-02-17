<?php

require "backend/functions.php";
dbconn(false);
loggedinorreturn();

$action = $_GET["action"] ?? '';
$pollid = (int) ($_GET["pollid"] ?? 0);
$returnto = $_GET["returnto"] ?? '';

if ($action == "delete") {
  	if (get_user_class() < UC_MODERATOR)
  		stderr("Error", "Permission denied.");

  	if (! is_valid_id($pollid))
		stderr("Error", "Invalid ID.");

   	$sure = (int) $_GET["sure"];
   	if (! $sure) {
    	stderr("Delete poll","Do you really want to delete a poll? Click\n" .
    		"<a href=?action=delete&pollid=$pollid&returnto=$returnto&sure=1>here</a> if you are sure.");
    }

    DB::executeUpdate("DELETE FROM pollanswers WHERE pollid = $pollid");
    DB::executeUpdate("DELETE FROM polls WHERE id = $pollid");
    if ($returnto == "main")
        header("Location: $SITEURL");
    else
        header("Location: $SITEURL/polls.php?deleted=1");
    die;
}

$pollcount = DB::fetchColumn("SELECT COUNT(*) FROM polls");
if (! $pollcount)
    stderr("Sorry...", "There are no polls!");

$polls = DB::query("SELECT * FROM polls ORDER BY id DESC");
stdhead("Previous polls");

begin_frame("Previous Polls");

while ($poll = $polls->fetch()) {
    $o = array($poll["option0"], $poll["option1"], $poll["option2"], $poll["option3"], $poll["option4"],
    $poll["option5"], $poll["option6"], $poll["option7"], $poll["option8"], $poll["option9"]);

    print("<p><table width=750 border=1 cellspacing=0 cellpadding=10 align=center><tr><td align=center>\n");

    print("<p class=sub>");
    $added = gmdate("Y-m-d", strtotime($poll['added'])) . " GMT ("
        . (get_elapsed_time(sql_timestamp_to_unix_timestamp($poll["added"]))) . " ago)";

    echo $added;

    if (get_user_class() >= UC_MODERATOR) {
    	print(" - [<a href=makepoll.php?action=edit&pollid=$poll[id]><b>Edit</b></a>]\n");
		print(" - [<a href=?action=delete&pollid=$poll[id]><b>Delete</b></a>]\n");
	}

	print("<a name=$poll[id]></a>");
	print("</p>\n");
    print("<table width=400 class=main border=1 cellspacing=0 cellpadding=5><tr><td class=text>\n");
    print("<p align=center><b>" . $poll["question"] . "</b></p>");

    // todo: subquery
    $pollanswers = DB::query("SELECT selection FROM pollanswers WHERE pollid = " . $poll["id"] . " AND  selection < 20");
    $tvotes = 0;

    $vs = []; // count for each option ([0]..[19])
    $os = []; // votes and options: array(array(123, "Option 1"), array(45, "Option 2"))

    // Count votes
    while ($pollanswer = $pollanswers->fetch()) {
        $vs[$pollanswer['selection']] = ($vs[$pollanswer['selection']] ?? 0) + 1;
        $tvotes += 1;
    }

    reset($o);
    for ($i = 0; $i < count($o); ++$i) {
        if ($o[$i])
            $os[$i] = array($vs[$i] ?? 0, $o[$i]);
    }

    // now os is an array like this:
    if ($poll["sort"] == "yes") {
    	usort($os, function($a, $b) { return $b[0] <=> $a[0]; });
    }

    print("<table width=100% class=main border=0 cellspacing=0 cellpadding=0>\n");
    $i = 0;
    $c = '';
    while ($os[$i] ?? false) {
        $a = $os[$i];
	  	if ($tvotes > 0)
	  		$p = round($a[0] / $tvotes * 100);
	  	else
			$p = 0;
        if ($i % 2)
            $c = " class=embedded";
      print("<tr><td$c>" . $a[1] . "&nbsp;&nbsp;</td><td$c>" .
        "<img src=images/bar.gif height=9 width=" . ($p * 3) . "> $p%</td></tr>\n");
      ++$i;
    }
    print("</table>\n");
	$tvotes = number_format($tvotes);
    print("<p align=center>Votes: $tvotes</p>\n");
    print("</td></tr></table>\n");

    print("</td></tr></table></p>\n");

}

end_frame();
stdfoot();

