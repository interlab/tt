<?php 

require "backend/functions.php";
dbconn();
loggedinorreturn();

if (get_user_class() < UC_MODERATOR) {
    stderr("Error","Permission denied.");
}

$action = $_GET["action"] ?? '';
$pollid = (int) ($_GET["pollid"] ?? 0);

if ($action == "edit") {
	if (!is_valid_id($pollid)) {
		stderr("Error","Invalid ID $pollid.");
    }
	$poll = DB::fetchAssoc("SELECT * FROM polls WHERE id = $pollid LIMIT 1");
	if (! $poll) {
		stderr("Error","No poll found with ID $pollid.");
	}
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // dump($_POST);
    $pollid = $_POST["pollid"];
    $question = $_POST["question"];
    $option0 = $_POST["option0"];
    $option1 = $_POST["option1"];
    $option2 = $_POST["option2"];
    $option3 = $_POST["option3"];
    $option4 = $_POST["option4"];
    $option5 = $_POST["option5"];
    $option6 = $_POST["option6"];
    $option7 = $_POST["option7"];
    $option8 = $_POST["option8"];
    $option9 = $_POST["option9"];
    $sort = $_POST["sort"] ?? 'yes';
    $returnto = $_POST["returnto"];

    if (!$question || !$option0 || !$option1) {
        stderr("Error", "Missing form data!");
    }

    if ($pollid) {
		DB::update('polls', [
            'question' => $question, 'option0' => $option0,
            'option1' => $option1, 'option2' => $option2,
            'option3' => $option3, 'option4' => $option4,
            'option5' => $option5, 'option6' => $option6,
            'option7' => $option7, 'option8' => $option8,
            'option9' => $option9, 'sort' => $sort],
            ['id' => $pollid]
        );
    } else {
        DB::insert('polls', [
            'added' => get_date_time(),
            'question' => $question,
            'option0' => $option0,
            'option1' => $option1,
            'option2' => $option2,
            'option3' => $option3,
            'option4' => $option4,
            'option5' => $option5,
            'option6' => $option6,
            'option7' => $option7,
            'option8' => $option8,
            'option9' => $option9,
            'sort' => $sort]
        );
    }

    if ($returnto == "main")
        header("Location: $SITEURL");
    elseif ($pollid)
        header("Location: $SITEURL/polls.php#$pollid");
    else
        header("Location: $SITEURL");
    die;
}

stdhead();

if ($pollid) {
	print("<center><h1>Edit poll</h1></center>");
} else {
	// Warn if current poll is less than 3 days old
	$arr = DB::fetchAssoc("SELECT question,added FROM polls ORDER BY added DESC LIMIT 1");
	if ($arr) {
        $hours = floor((gmtime() - sql_timestamp_to_unix_timestamp($arr["added"])) / 3600);
        $days = floor($hours / 24);
        if ($days < 3) {
            $hours -= $days * 24;
            if ($days)
                $t = "$days day" . ($days > 1 ? "s" : "");
            else
                $t = "$hours hour" . ($hours > 1 ? "s" : "");
            print("<p><center><font color=red><b>Note: The current poll (<i>" . $arr["question"] . "</i>) is only $t old.</b></font></center></p>");
        }
	}
}
	begin_frame("Make poll");
    print("<p><font color=red><center>*</font> required</center></p>");
?>

<table border=0 cellspacing=1 cellpadding=5 align=center>
<form method=post action=makepoll.php>
<tr><td class=rowhead>Question <font color=red>*</font></td><td align=left><input name=question size=80 maxlength=255 value="<?= $poll['question'] ?? '' ?>"></td></tr>
<tr><td class=rowhead>Option 1 <font color=red>*</font></td><td align=left><input name=option0 size=80 maxlength=60 value="<?= $poll['option0'] ?? '' ?>"><br></td></tr>
<tr><td class=rowhead>Option 2 <font color=red>*</font></td><td align=left><input name=option1 size=80 maxlength=60 value="<?= $poll['option1'] ?? '' ?>"><br></td></tr>
<tr><td class=rowhead>Option 3</td><td align=left><input name=option2 size=80 maxlength=60 value="<?= $poll['option2'] ?? '' ?>"><br></td></tr>
<tr><td class=rowhead>Option 4</td><td align=left><input name=option3 size=80 maxlength=60 value="<?= $poll['option3'] ?? '' ?>"><br></td></tr>
<tr><td class=rowhead>Option 5</td><td align=left><input name=option4 size=80 maxlength=60 value="<?= $poll['option4'] ?? '' ?>"><br></td></tr>
<tr><td class=rowhead>Option 6</td><td align=left><input name=option5 size=80 maxlength=60 value="<?= $poll['option5'] ?? '' ?>"><br></td></tr>
<tr><td class=rowhead>Option 7</td><td align=left><input name=option6 size=80 maxlength=60 value="<?= $poll['option6'] ?? '' ?>"><br></td></tr>
<tr><td class=rowhead>Option 8</td><td align=left><input name=option7 size=80 maxlength=60 value="<?= $poll['option7'] ?? '' ?>"><br></td></tr>
<tr><td class=rowhead>Option 9</td><td align=left><input name=option8 size=80 maxlength=60 value="<?= $poll['option8'] ?? '' ?>"><br></td></tr>
<tr><td class=rowhead>Option 10</td><td align=left><input name=option9 size=80 maxlength=60 value="<?= $poll['option9'] ?? '' ?>"><br></td></tr>
<tr><td class=rowhead>Sort</td><td>
<input type=radio name=sort value=yes <?= $pollid ? ($poll["sort"] != "no" ? " checked" : "") : '' ?>>Yes
<input type=radio name=sort value=no <?= $pollid ? ($poll["sort"] == "no" ? " checked" : "") : '' ?>> No
</td></tr>
<tr><td colspan=2 align=center><input type=submit value=<?= $pollid ? "'Edit poll'" : "'Create poll'"?> style='height: 20pt'></td></tr>
</table>
<input type=hidden name=pollid value=<?= $poll["id"] ?? 0 ?>>
<input type=hidden name=returnto value=<?= $_GET["returnto"] ?>>
</form>
<br>

<?php end_frame();
stdfoot();

