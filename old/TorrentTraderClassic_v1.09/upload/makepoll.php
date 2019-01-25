<?php 
require "backend/functions.php";
dbconn();
loggedinorreturn();

if (get_user_class() < UC_MODERATOR)
  stderr("Error","Permission denied.");

$action = $_GET["action"];
$pollid = (int)$_GET["pollid"];

if ($action == "edit")
{
	if (!is_valid_id($pollid))
		stderr("Error","Invalid ID $pollid.");
	$res = mysql_query("SELECT * FROM polls WHERE id = $pollid")
			or sqlerr(__FILE__, __LINE__);
	if (mysql_num_rows($res) == 0)
		stderr("Error","No poll found with ID $pollid.");
	$poll = mysql_fetch_array($res);
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
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
  $sort = $_POST["sort"];
  $returnto = $_POST["returnto"];

  if (!$question || !$option0 || !$option1)
    stderr("Error", "Missing form data!");

  if ($pollid)
		mysql_query("UPDATE polls SET " .
		"question = " . sqlesc($question) . ", " .
		"option0 = " . sqlesc($option0) . ", " .
		"option1 = " . sqlesc($option1) . ", " .
		"option2 = " . sqlesc($option2) . ", " .
		"option3 = " . sqlesc($option3) . ", " .
		"option4 = " . sqlesc($option4) . ", " .
		"option5 = " . sqlesc($option5) . ", " .
		"option6 = " . sqlesc($option6) . ", " .
		"option7 = " . sqlesc($option7) . ", " .
		"option8 = " . sqlesc($option8) . ", " .
		"option9 = " . sqlesc($option9) . ", " .
		"sort = " . sqlesc($sort) . " " .
    "WHERE id = $pollid") or sqlerr(__FILE__, __LINE__);
  else
  	mysql_query("INSERT INTO polls VALUES(0" .
		", '" . get_date_time() . "'" .
    ", " . sqlesc($question) .
    ", " . sqlesc($option0) .
    ", " . sqlesc($option1) .
    ", " . sqlesc($option2) .
    ", " . sqlesc($option3) .
    ", " . sqlesc($option4) .
    ", " . sqlesc($option5) .
    ", " . sqlesc($option6) .
    ", " . sqlesc($option7) .
    ", " . sqlesc($option8) .
    ", " . sqlesc($option9) .
    ", " . sqlesc($sort) .
  	")") or sqlerr(__FILE__, __LINE__);

  if ($returnto == "main")
		header("Location: $SITEURL");
  elseif ($pollid)
		header("Location: $SITEURL/polls.php#$pollid");
	else
		header("Location: $SITEURL");
	die;
}

stdhead();

if ($pollid)
	print("<center><h1>Edit poll</h1></center>");
else
{
	// Warn if current poll is less than 3 days old
	$res = mysql_query("SELECT question,added FROM polls ORDER BY added DESC LIMIT 1") or sqlerr();
	$arr = mysql_fetch_assoc($res);
	if ($arr)
	{
	  $hours = floor((gmtime() - sql_timestamp_to_unix_timestamp($arr["added"])) / 3600);
	  $days = floor($hours / 24);
	  if ($days < 3)
	  {
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
<tr><td class=rowhead>Question <font color=red>*</font></td><td align=left><input name=question size=80 maxlength=255 value="<?=$poll['question']?>"></td></tr>
<tr><td class=rowhead>Option 1 <font color=red>*</font></td><td align=left><input name=option0 size=80 maxlength=60 value="<?=$poll['option0']?>"><br></td></tr>
<tr><td class=rowhead>Option 2 <font color=red>*</font></td><td align=left><input name=option1 size=80 maxlength=60 value="<?=$poll['option1']?>"><br></td></tr>
<tr><td class=rowhead>Option 3</td><td align=left><input name=option2 size=80 maxlength=60 value="<?=$poll['option2']?>"><br></td></tr>
<tr><td class=rowhead>Option 4</td><td align=left><input name=option3 size=80 maxlength=60 value="<?=$poll['option3']?>"><br></td></tr>
<tr><td class=rowhead>Option 5</td><td align=left><input name=option4 size=80 maxlength=60 value="<?=$poll['option4']?>"><br></td></tr>
<tr><td class=rowhead>Option 6</td><td align=left><input name=option5 size=80 maxlength=60 value="<?=$poll['option5']?>"><br></td></tr>
<tr><td class=rowhead>Option 7</td><td align=left><input name=option6 size=80 maxlength=60 value="<?=$poll['option6']?>"><br></td></tr>
<tr><td class=rowhead>Option 8</td><td align=left><input name=option7 size=80 maxlength=60 value="<?=$poll['option7']?>"><br></td></tr>
<tr><td class=rowhead>Option 9</td><td align=left><input name=option8 size=80 maxlength=60 value="<?=$poll['option8']?>"><br></td></tr>
<tr><td class=rowhead>Sort</td><td>
<input type=radio name=sort value=yes <?=$poll["sort"] != "no" ? " checked" : "" ?>>Yes
<input type=radio name=sort value=no <?=$poll["sort"] == "no" ? " checked" : "" ?>> No
</td></tr>
<tr><td colspan=2 align=center><input type=submit value=<?=$pollid?"'Edit poll'":"'Create poll'"?> style='height: 20pt'></td></tr>
</table>
<input type=hidden name=pollid value=<?=$poll["id"]?>>
<input type=hidden name=returnto value=<?=$_GET["returnto"]?>>
</form>
<br>
<?php end_frame();
stdfoot();

