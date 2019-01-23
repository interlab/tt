<?php

require_once("backend/functions.php");
dbconn();

//Here we decide if the invite system is needed
if ($INVITEONLY)
{
stdhead("Invite only");
begin_frame("Invite only");
print("<br><br><center>Sorry this site has disabled user registration, the only way to register is via a invite from a existing member.<br><br></center>");
end_frame();
stdfoot();
exit;
}
//end invite only check

$r = mysql_query("SELECT COUNT(*) FROM users") or sqlerr();
$a = mysql_fetch_row($r);
if ($a[0] >= $maxsiteusers)
  bark("Sorry...", "The site is full!<br />The limit of $maxusers users have been reached.<br />HOWEVER, user accounts expires all the time so please check back again later!");

$nuIP = getip();
$dom = @gethostbyaddr($nuIP);
if ($dom == $nuIP || @gethostbyname($dom) != $nuIP)
  $dom = "";
else {
  $dom = strtoupper($dom);
  preg_match('/^(.+)\.([A-Z]{2,3})$/', $dom, $tldm);
  $dom = $tldm[2];
}

$client = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $client); 
$age = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $age); 
$email = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $email); 
$wantusername = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $wantusername); 
$passagain = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $passagain); 
$country = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $country); 


if ($wantusername != "") {
  $message == "";
  function validusername($username) {
    $allowedchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    for ($i = 0; $i < strlen($username); ++$i)
      if (strpos($allowedchars, $username[$i]) === false)
        return false;
      return true;
  }

  if (empty($wantpassword) || empty($email))
	$message = "Don't leave any required field blank.";
  if (strlen($wantusername) > 12)
	$message = "Sorry, username is too long (max is 12 chars)";
  if ($wantpassword != $passagain)
	$message = "The passwords didn't match! Must've typoed. Try again.";
  if (strlen($wantpassword) < 6)
	$message = "Sorry, password is too short (min is 6 chars)";
  if (strlen($wantpassword) > 40)
	$message = "Sorry, password is too long (max is 40 chars)";
  if ($wantpassword == $wantusername)
 	$message = "Sorry, password cannot be same as user name.";
  if (!validemail($email))
	$message = "That doesn't look like a valid email address.";
  if (!validusername($wantusername))
	$message = "Invalid username.";
do {
  // check if email addy is already in use
  $a = (@mysql_fetch_row(@mysql_query("select count(*) from users where email='$email'"))) or die(mysql_error());
  if ($a[0] != 0)
    $message = "The e-mail address $email is already in use.";
   //check username isnt in use
  $a = (@mysql_fetch_row(@mysql_query("select count(*) from users where username='$wantusername'"))) or die(mysql_error());
  if ($a[0] != 0)
    $message = "The username $wantusername is already in use."; 
  $secret = mksecret();
  $wantpassword = md5($wantpassword);
if (!empty($message))
	break;

    $ret = mysql_query("INSERT INTO users (username, password, secret, email, status, added, age, country, gender, client) VALUES (" .
	  implode(",", array_map("sqlesc", array($wantusername, $wantpassword, $secret, $email, 'pending'))) .
		",'" . get_date_time() . "','$age','$country','$gender','$client')");

    if (!$ret) {
      $message = "Mysql Error!";
      if (mysql_errno() == 1062)
        $message = "Username already exists!";
    }
    $id = mysql_insert_id();
    //write_log("User account $id ($wantusername) was created");

    $psecret = md5($secret);
    $thishost = $_SERVER["HTTP_HOST"];
    $thisdomain = preg_replace('/^www\./is', "", $thishost);
//NO ADMIN CONFIRM
if ($ACONFIRM) {
	$body = "Your account at $SITENAME has been created.\n\nYou will have to wait for the approval of an admin before you can use your new account.\n\n$SITENAME Admin";
} else {//ADMIN CONFIRM
	$body = "Your account at $SITENAME has been : APPROVED\n\nTo confirm your user registration, you have to follow this link:\n\n	$SITEURL/account-confirm.php?id=$id&secret=$psecret\n\nAfter you do this, you will be able to use your new account.\n\n	If you fail to do this, your account will be deleted within a few days.\n\n$SITENAME Admin";
}

    ini_set("sendmail_from", ""); // Null envelope (Return-Path: <>)
    mail($email, "Your $SITENAME User Account", $body, "From: $SITENAME <$SITEEMAIL>");

    header("Refresh: 0; url=account-confirm-ok.php?type=signup&email=" . urlencode($email));
    die;
  } while(0);
}

stdhead("Signup");
begin_frame("Signup");
if ($message != "")
	bark2("Signup Failed", $message);
?>

<?= $txt['COOKIES'] ?>
<p>
<form method="post" action="account-signup.php">
	<table cellSpacing="0" cellPadding="2" border="0" >
			<tr>
				<td>Username: <font class="small"><font color="#FF0000">*</font></td>
				<td><input type="text" size="40" name="wantusername" /></td>
			</tr>
			<tr>
				<td>Password: <font class="small"><font color="#FF0000">*</font></td>
				<td><input type="password" size="40" name="wantpassword" /></td>
			</tr>
			<tr>
				<td>Confirm: <font class="small"><font color="#FF0000">*</font></td>
				<td><input type="password" size="40" name="passagain" /></td>
			</tr>
			<tr>
				<td>Email: <font class="small"><font color="#FF0000">*</font></td>
				<td><input type="text" size="40" name="email"/></td>
			</tr>
			<tr>
				<td>Age:</td>
				<td><input type="text" size="40" name="age" maxlength="3" /></td>
			</tr>
			<tr>
				<td>Country:</td>
				<td>
					<select name="country" size="1">
						<?php
						$countries = "<option value=\"0\">---- None selected ----</option>\n";
						$ct_r = mysql_query("SELECT id,name,domain from countries ORDER BY name") or die;
						while ($ct_a = mysql_fetch_array($ct_r)) {
						  $countries .= "\t\t\t\t\t\t<option value=\"$ct_a[id]\"";
						  if ($dom == $ct_a["domain"])
						    $countries .= " SELECTED";
						  $countries .= ">$ct_a[name]</option>\n";
						}
						?>
						<?=$countries ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Gender:</td>
				<td>
					<input type="radio" name="gender" value="Male">Male
					&nbsp;&nbsp;
					<input type="radio" name="gender" value="Female">Female
				</td>
			</tr>
			<tr>
				<td>Preferred BitTorrent Client:</td>
				<td><input type="text" size="40" name="client"  maxlength="20" /></td>
			</tr>
			<tr>
				<td>Signup Time:</td>
				<td><b><? print date("D dS M, Y h:i a"); ?></b></td>
			</tr>
			<tr>
				<td align="middle" colSpan="2">
                <input type="submit" value="Sign Up" />
              </td>
			</tr>
	</table>
</form>

<?

end_frame();
stdfoot();

?>
