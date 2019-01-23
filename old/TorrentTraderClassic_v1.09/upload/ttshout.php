<?php

///////////////////////////////////////////////////////////////
//
//
//		Shoutbox Hack for TorrentTrader RC2.
//		
//			Coded by: PseudoX
//			Contact: pseudox@gmail.com
//
//		Feel free to use this on your own tracker
//		also to modify to suit your needs, just 
//		keeps this comment at the top and all is 
//		good.
//
//
///////////////////////////////////////////////////////////////


//Disclaimer:
//anything u screw up, is your fault. i take no responsibility.

//Copyright:
//feel free to modify/change or hack this to bits, or even rerelease it (modified signifcantly) under your name 
//ASLONG AS I STILL HAVE CREDIT SOMEWHERE. ie. Bob's Shoutbox (based on PseudoX's code)


include_once('backend/functions.php');
include_once('backend/config.php');
dbconn(false);
$local_time = get_date_time(time());

global $CURUSER;

function CensorWords($msg)
{
    global $CENSORWORDS;
    
    if ($CENSORWORDS) {
        $query = 'SELECT * FROM censor';
        $res = DB::query($query);
        while ($row = $res->fetch()) {
            $msg = str_replace($row['word'], $row['censor'], $msg);
        }
    }

    return $msg;
}

function ReplaceSmilies($msg)
{
    global $SITEURL;

    $query = 'SELECT * FROM shoutbox_emoticons';
    $res = DB::query($query);
	while ($row = $res->fetch()) {
        $imagelink = '<img src="'.$GLOBALS['SITEURL'].'/images/shoutbox/'.$row['image'].'">';
        $msg = str_replace($row['text'], $imagelink, $msg);
	}

	return $msg;
}

function MakeSQLSafe($msg)
{
    // this will allow all punctuation in the message, and also prevent sql injection.
    $msg = str_replace("'", '&#39;', $msg);
    $msg = str_replace("--", '&#45;&#45;', $msg);

    return $msg;
}

function MakeHTMLSafe($msg)
{

//this will stop people from using javascript and html tags in their posts.

$msg = str_replace('<', '&lt;', $msg);
$msg = str_replace('>', '&gt;', $msg);
$msg = str_replace('javascript:', 'java script:', $msg);

// [b]Bold[/b]
$msg = preg_replace("/\[b\]((\s|.)+?)\[\/b\]/", "<b>\\1</b>", $msg);

// [i]Italic[/i]
$msg = preg_replace("/\[i\]((\s|.)+?)\[\/i\]/", "<i>\\1</i>", $msg);

// [u]Underline[/u]
$msg = preg_replace("/\[u\]((\s|.)+?)\[\/u\]/", "<u>\\1</u>", $msg);

// [u]Underline[/u]
$msg = preg_replace("/\[u\]((\s|.)+?)\[\/u\]/i", "<u>\\1</u>", $msg);

// [color=blue]Text[/color]
$msg = preg_replace(
 "/\[color=([a-zA-Z]+)\]((\s|.)+?)\[\/color\]/i",
 "<font color=\\1>\\2</font>", $msg);

// [color=#ffcc99]Text[/color]
$msg = preg_replace(
 "/\[color=(#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])\]((\s|.)+?)\[\/color\]/i",
 "<font color=\\1>\\2</font>", $msg);

// [url=http://www.example.com]Text[/url]
$msg = preg_replace(
 "/\[url=((http|ftp|https|ftps|irc):\/\/[^<>\s]+?)\]((\s|.)+?)\[\/url\]/i",
 "<a target='_parent' href=\\1>\\3</a>", $msg);

// [url]http://www.example.com[/url]
$msg = preg_replace(
 "/\[url\]((http|ftp|https|ftps|irc):\/\/[^<>\s]+?)\[\/url\]/i",
 "<a target='_parent' href=\\1>\\1</a>", $msg);

// [font=Arial]Text[/font]
$msg = preg_replace(
 "/\[font=([a-zA-Z ,]+)\]((\s|.)+?)\[\/font\]/i",
 "<font face=\"\\1\">\\2</font>", $msg);

return $msg;
}

//deleting msges
if (isset($_GET['del']))
{
		//no sql injection
		if (is_numeric($_GET['del']))
		{
		$query = "SELECT * FROM shoutbox WHERE msgid=".$_GET['del'] ;
		$result = mysql_query($query);
		}
		else {echo "invalid msg id STOP TRYING TO INJECT SQL";exit;}

	$row = mysql_fetch_row($result);
		
		if ( (get_user_class() >= UC_JMODERATOR) || ($CURUSER['username'] == $row[1]) )
		{	
			$query = "DELETE FROM shoutbox WHERE msgid=".$_GET['del'] ;
			mysql_query($query);	
		}


}

//adding msges
if (isset($_POST['message']) && $_POST['message'] !== '') {
		
	if (isset($CURUSER)) {
		// this will check to see if there has already been an identical message posted (preventing double posts)
		$query = "SELECT COUNT(*) FROM shoutbox WHERE message='".MakeSQLSafe($_POST['message'])."' AND user='{$CURUSER['username']}' AND NOW()-date < 30";
		$count = DB::fetchColumn($query);
		if (!$count) {
            // add the message if all is ok. (not a doublepost)
            $query = "INSERT INTO shoutbox (msgid, user, message, date, userid) VALUES (NULL, '".$CURUSER['username']."', '".
            MakeSQLSafe($_POST['message'])."', '".$local_time."', '".$CURUSER['id']."')";
            DB::query($query);
		}
	}
}

    // get the current theme
    $ss_uri = getThemeUri();

?>
<HTML>
<HEAD>
<TITLE><?=$SITENAME?> Shoutbox</TITLE>
<META HTTP-EQUIV="refresh" content="300">
<!-- <link rel="stylesheet" type="text/css" href="themes/<?=$ss_uri?>/theme.css" /> -->
<link rel="stylesheet" type="text/css" href="themes/<?=$ss_uri?>/ttshout.css" />
</HEAD>

<?php

//when you post a message, if you uncomment this, the page will jump down to the shoutbox. it will also do it when you load the site.
//   not really recommended

//echo '<BODY style="font-family: verdana; color: black; float: middle" onLoad="GiveMsgBoxFocus();">';


echo '<table width=100% background=#ffffff border=0><tr><Td>';

?>
<SCRIPT LANGUAGE="JAVASCRIPT">
<!-- 
function GiveMsgBoxFocus()
{
document.ttshoutform.message.focus();
}

function ShowSmilies() {
  var SmiliesWindow = window.open("<?=$SITEURL?>/ttshout_smilies.php", "Smilies","width=200,height=200,resizable=yes,scrollbars=yes,toolbar=no,location=no,directories=no,status=no");
}

//-->
</SCRIPT>
<?php
if(!isset($_GET['history']))
{ 
echo '
<div class="contain">
<table border="0" background="#ffffff" style="width: 99%; table-layout:fixed">';
}
else
{
echo '
<div class="history">';

//page numbers

$count = DB::fetchColumn('SELECT COUNT(*) FROM shoutbox');

echo '<div align="middle">Pages: ';
$pages = round($count / 100) + 1;
$i = 1;
while ($pages > 0) {
    echo "<a href='".$SITEURL."/ttshout.php?history=1&page=".$i."'>[".$i."]</a>&nbsp;";
    $i++;
    $pages--;
}

echo '
    </div></br><table border="0" background="#ffffff" style="width: 99%; table-layout:fixed">';
}

if (isset($_GET['history']))
{
	if (isset($_GET['page']))
	{
		if($_GET['page'] > '1')
		{
		$lowerlimit = $_GET['page'] * 100 - 100;
		$upperlimit = $_GET['page'] * 100;
		}
		else
		{
		$lowerlimit = 0;
		$upperlimit = 100;
		}
	}
	else
	{
		$lowerlimit = 0;
		$upperlimit = 100;
	}
	
	$query = 'SELECT * FROM shoutbox ORDER BY msgid DESC LIMIT '.$lowerlimit.','.$upperlimit;
	//echo $query;
}
else
{
	$query = 'SELECT * FROM shoutbox ORDER BY msgid DESC LIMIT 20';
}
//echo $query;
$res = DB::query($query);
$alt = false;

while ($row = $res->fetch()) {

//alternate the colours
if ($alt)
{
	echo '<tr class="noalt">';
	$alt = false;
}
else
{
	echo '<tr class="alt">';
	$alt = true;
}

echo '<td style="font-size: 9px; width: 118px;">';
echo "<div align='left' style='float: left'>";

echo date('jS M, g:ia', sql_timestamp_to_unix_timestamp($row['date']));

echo "</div>";
if ( (get_user_class() >= UC_JMODERATOR) || ($CURUSER['username'] == $row['user']) )
{

echo "<div align='right' style='float: right'><a href='".$SITEURL."/ttshout.php?del=".$row['msgid']."' style='font-size: 8px'>[D]</a><div>";
}

echo	'</td><td style="font-size: 12px; padding-left: 5px">
<a href="'.$GLOBALS['SITEURL'].'/account-details.php?id='.$row['userid'].'" target="_parent"><b>'.$row['user'].':</b>
</a>&nbsp;&nbsp;'.nl2br(ReplaceSmilies(MakeHTMLSafe(CensorWords($row['message']))));

echo	'</td></tr>';


}
?>

</table>
</div>
<br>

<?php

//if the user is logged in, show the shoutbox, if not, dont.
if(!isset($_GET['history']))
{

if (isset($CURUSER))
{

echo "
<form name='ttshoutform' action='".$SITEURL."/ttshout.php' method='post'>
<table width=100% border=0 cellpadding=1 cellspacing=1>
<tr class='messageboxback'>
<td width='100%' align=center>
<input type='text' name='message' class='msgbox'>
</td>
<td>
<input type='submit' name='submit' value='Shout' class='shoutbtn'>
</td>
<td style='font-size: 8px';>
<a href='javascript:ShowSmilies();'>Smiles</a>
<br>
<a href='ttshout.php'>Refresh</a>
<br>
<a href='".$SITEURL."/ttshout.php?history=1' target='_blank'>History</a>
</td>
</tr>
</table>
";
echo "</form>";


}
else
{
echo "<br /><div class='error'>You must login to shout.</div>";
}

}
echo '</Td></tr></table>';
?>
</BODY>
</HTML>