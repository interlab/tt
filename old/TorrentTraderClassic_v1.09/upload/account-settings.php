<?php
//
// CSS and language updated 30.11.05
//
require_once("backend/functions.php");
dbconn(false);
loggedinorreturn();

if ($submit == "1") {
  $set = array();

  $updateset = array();
  $changedemail = $newsecret = 0;

  if ($chpassword != "") {
    if ($CURUSER["password"] != md5($originalpassword))
        $message = "" . THATS_NOT_YOUR_ORIGNAL_PASS . "";
    if (strlen($chpassword) < 6)
        $message = "" . PASS_TO_SHORT . "";
    if ($chpassword != $passagain)
        $message = "" . PASSWORDS_NOT_MATCH . "";
    $chpassword = md5($chpassword);
    $updateset[] = "password = " . sqlesc($chpassword);
    $newsecret = 1;
  }

  if ($email != $CURUSER["email"]) {
	if (!validemail($email))
		$message = "" . NOT_VAILD_EMAIL . "";
	$changedemail = 1;
  }

  $acceptpms = $_POST["acceptpms"];
  $pmnotif = $_POST["pmnotif"];
  $privacy = $_POST["privacy"];
  $notifs = ($pmnotif == 'yes' ? "[pm]" : "");
  $r = mysql_query("SELECT id FROM categories") or sqlerr();
  $rows = mysql_num_rows($r);
  for ($i = 0; $i < $rows; ++$i) {
	$a = mysql_fetch_assoc($r);
	if ($HTTP_POST_VARS["cat$a[id]"] == 'yes')
	  $notifs .= "[cat$a[id]]";
  }
  $avatar = strip_tags($_POST["avatar"]);
  $title = strip_tags($_POST["title"]);
  $signature = $_POST["signature"];
  $stylesheet = $_POST["stylesheet"];
  $commentpm = $_POST["commentpm"];
  $language = $_POST["language"];
  $client = $_POST["client"];
  $age = $_POST["age"];
  $gender= $_POST["gender"];
  $country = $_POST["country"];
  $tzoffset = $_POST["tzoffset"];
  $privacy = $_POST["privacy"];

  if (is_valid_id($stylesheet))
    $updateset[] = "stylesheet = '$stylesheet'";
  if (is_valid_id($language))
    $updateset[] = "language = '$language'";
  if (is_valid_id($country))
    $updateset[] = "country = $country";
  $updateset[] = "tzoffset = " . sqlesc($tzoffset);
  if ($acceptpms == "yes")
    $acceptpms = 'yes';
  else
    $acceptpms = 'no';
   if (is_valid_id($age))
    $updateset[] = "age = '$age'";
  $updateset[] = "acceptpms = ".sqlesc($acceptpms);
  $updateset[] = "commentpm = " . sqlesc($commentpm);
  $updateset[] = "notifs = ".sqlesc($notifs);
  $updateset[] = "privacy = ".sqlesc($privacy);
  $updateset[] = "gender = ".sqlesc($gender);
  $updateset[] = "client = ".sqlesc($client);
  $updateset[] = "avatar = " . sqlesc(stripslashes($avatar));
  $updateset[] = "signature = ".sqlesc(stripslashes($signature));
  $updateset[] = "title = ".sqlesc(stripslashes($title));

  /* ****** */

  if ($message == "") {

    if ($newsecret) {
	$sec = mksecret();
	$updateset[] = "secret = " . sqlesc($sec);
	logincookie($CURUSER["id"], $chpassword, $sec);
    }

    if ($changedemail) {
	$sec = mksecret();
	$hash = md5($sec . $email . $sec);
	$obemail = rawurlencode($email);
	$updateset[] = "editsecret = " . sqlesc($sec);
	$thishost = $_SERVER["HTTP_HOST"];
	$thisdomain = preg_replace('/^www\./is', "", $thishost);
	$body = <<<EOD
You have requested that your user profile (username {$CURUSER["username"]})
on $SITEURL should be updated with this email address ($email) as
user contact.

If you did not do this, please ignore this email. The person who entered your
email address had the IP address {$_SERVER["REMOTE_ADDR"]}. Please do not reply.

To complete the update of your user profile, please follow this link:

$SITEURL/account-ce.php?id={$CURUSER["id"]}&secret=$hash&email=$obemail

Your new email address will appear in your profile after you do this. Otherwise
your profile will remain unchanged.
EOD;

	mail($email, "$SITENAME profile change confirmation", $body, "From: $SITEEMAIL", "-f$SITEEMAIL");
	$mailsent = 1;
    }
    mysql_query("UPDATE users SET " . implode(",", $updateset) . " WHERE id = " . $CURUSER["id"]);
    $edited=1;
  }
  header("Location: account.php?edited=$edited&message=$message&mailsent=$mailsent");
  die;
}


$res = mysql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"]) or print(mysql_error());
$arr = mysql_fetch_row($res);
$messages = $arr[0];
$res = mysql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " and unread='yes'") or print(mysql_error());
$arr = mysql_fetch_row($res);
$unread = $arr[0];

stdhead("User CP");
begin_frame("" . YOUR_SETTINGS . "");

?>
<form method=post action=account-settings.php>
<input type=hidden name=submit value=1>
<table border="0" cellspacing=0 cellpadding="5" width="100%">
<?

$ss_r = mysql_query("SELECT * from stylesheets") or die;
$ss_sa = array();
while ($ss_a = mysql_fetch_array($ss_r))
{
  $ss_id = $ss_a["id"];
  $ss_name = $ss_a["name"];
  $ss_sa[$ss_name] = $ss_id;
}
ksort($ss_sa);
reset($ss_sa);
while (list($ss_name, $ss_id) = each($ss_sa))
{
  if ($ss_id == $CURUSER["stylesheet"]) $ss = " selected"; else $ss = "";
  $stylesheets .= "<option value=$ss_id$ss>$ss_name</option>\n";
}

$countries = "<option value=0>----</option>\n";
$ct_r = mysql_query("SELECT id,name from countries ORDER BY name") or die;
while ($ct_a = mysql_fetch_array($ct_r))
  $countries .= "<option value=$ct_a[id]" . ($CURUSER["country"] == $ct_a['id'] ? " selected" : "") . ">$ct_a[name]</option>\n";

ksort($tzs);
reset($tzs);
while (list($key, $val) = each($tzs)) {
if ($CURUSER["tzoffset"] == $key) {
  $timezone .= "<option value=\"$key\" selected>$val</option>\n";
} else {
  $timezone .= "<option value=\"$key\">$val</option>\n";
}
}


$acceptpms = $CURUSER["acceptpms"] == "yes";
tr("" . ACCOUNT_ACCEPTPM . "", "<input type=radio name=acceptpms" . ($acceptpms ? " checked" : "") .
  " value=yes>From all <input type=radio name=acceptpms" .
  ($acceptpms ? "" : " checked") . " value=no>" . ACCOUNT_PMSTAFFONLY . "",1);

$gender = "<option value=Male" . ($CURUSER["gender"] == Male ? " selected" : "") . ">" . MALE . "</option>\n"
	 ."<option value=Female" . ($CURUSER["gender"] == Female ? " selected" : "") . ">" . FEMALE . "</option>\n";

$torrentnotif = "<input type=checkbox checked>" . ACCOUNT_NOTIFY_WHEN_TORRENT_UPLOADED_IN . ":<br />";
$r = mysql_query("SELECT id,name FROM categories ORDER by sort_index, name") or sqlerr();
$i = 0;
while ($a = mysql_fetch_assoc($r))
{
  $torrentnotif .= "&nbsp;&nbsp;&nbsp;&nbsp;<input type=checkbox name=cat$a[id]" . (strpos($CURUSER['notifs'], "[cat$a[id]]") !== false ? " checked" : "") .
   " value='yes'>$a[name]<br />\n";
  ++$i;
}

function priv($name, $descr) {
	global $CURUSER;
	if ($CURUSER["privacy"] == $name)
		return "<input type=\"radio\" name=\"privacy\" value=\"$name\" checked=\"checked\" /> $descr";
	return "<input type=\"radio\" name=\"privacy\" value=\"$name\" /> $descr";
}

tr("" . ACCOUNT_PRIVACY_LV . "",  priv("normal", "" . NORMAL . "") . " " . priv("low", "" . LOW . "") . " " . priv("strong", "" . STRONG . " <br>(Stong level will hide your ratio and make your uploads anonymous)"), 1);

print("<tr><td align=right>PM on Comments</td><td align=left><input type=radio name=commentpm" . ($CURUSER["commentpm"] == "yes" ? " checked" : "") . " value=yes>yes<input type=radio name=commentpm" .  ($CURUSER["commentpm"] == "no" ? " checked" : "") . " value=no>no");

tr("" . ACCOUNT_EMAIL_NOTIFICATION . "", "<input type=checkbox name=pmnotif" . (strpos($CURUSER['notifs'], "[pm]") !== false ? " checked" : "") .
   " value=yes>" . ACCOUNT_PM_NOTIFY_ME . "<br />\n" .
   $torrentnotif, 1);


tr("" . THEME . "", "<select name=stylesheet>\n$stylesheets\n</select>",1);
tr("" . CLIENT ."", "<input type=text size=20 maxlength=20 name=client value=\"" . htmlspecialchars($CURUSER["client"]) . "\" />",1);
tr("" . AGE . "", "<input type=text size=4 maxlength=3 name=age value=\"" . htmlspecialchars($CURUSER["age"]) . "\" />",1);
tr("" . GENDER . "", "<select size=1 name=gender>\n$gender\n</select>",1);
tr("" . COUNTRY . "", "<select name=country>\n$countries\n</select>",1);
tr("" . ACCOUNT_TIMEZONE . "", "<select name=tzoffset>\n$timezone\n</select><br />" . ACCOUNT_TIMEZONEMSG . "",1);
tr("" . AVATAR_URL . "", "<input name=avatar size=50 value=\"" . htmlspecialchars($CURUSER["avatar"]) .
  "\"><br />\n80x80 px",1);
tr("" . CUSTOMTITLE . "", "<input name=title size=50 value=\"" . strip_tags($CURUSER["title"]) .
  "\"><br />\n " . HTML_NOT_ALLOWED . "",1);
tr("" . SIGNATURE . "", "<textarea name=signature cols=50 rows=10>" . htmlspecialchars($CURUSER["signature"]) .
  "</textarea><br />\n " . HTML_NOT_ALLOWED . "",1);
tr("" . EMAIL_ADDRESS . "", "<input type=\"text\" name=\"email\" size=50 value=\"" . htmlspecialchars($CURUSER["email"]) .
  "\"><br />\n" . REPLY_TO_CONFIRM_EMAIL . "<br>",1);
?>
<tr><td colspan="2" align="center"><input type="submit" value="<? echo "" . SUBMIT . "";?>" style='height: 25px'> <input type="reset" value="<? echo "" . REVERT . "";?>" style='height: 25px'></td></tr>
</table>

<? end_frame(); ?>

<br /><br />

<? begin_frame("" . CHANGE_YOUR_PASS . ""); ?>

<table border="0" cellspacing=0 cellpadding="5" width="100%">
<?
tr("" . CURRENT_PASSWORD . "", "<input type=\"password\" name=\"originalpassword\" size=\"50\" />", 1);
tr("" . NEW_PASSWORD . "", "<input type=\"password\" name=\"chpassword\" size=\"50\" />", 1);
tr("" . REPEAT . "", "<input type=\"password\" name=\"passagain\" size=\"50\" />", 1);
?>
<tr><td colspan="2" align="center"><input type="submit" value="<? echo "" . SUBMIT . "";?>" style='height: 25px'> <input type="reset" value="<? echo "" . REVERT . "";?>" style='height: 25px'></td></tr>
</table></form>

<?

end_frame();
stdfoot();

?>