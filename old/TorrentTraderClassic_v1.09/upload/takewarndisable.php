<?
require_once("backend/functions.php");
dbconn();
loggedinorreturn();
jmodonly();

if (empty($_POST["warndisable"]))
bark("Error", "You must select a user to edit.");

if (!empty($_POST["warndisable"])){
$enable = $_POST["enable"];
$disable = $_POST["disable"];
$unwarn = $_POST["unwarn"];
$warnlength = 0 + $_POST["warnlength"];
$warnpm = $_POST["warnpm"];
$_POST["warndisable"] = array_map("intval", $_POST["warndisable"]);
$userid = implode(", ", $_POST['warndisable']);

if ($disable != '') {
$do="UPDATE users SET enabled='no' WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")";
$res=mysql_query($do);
}

if ($enable != '') {
$do = "UPDATE users SET enabled='yes' WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")";
$res = mysql_query($do);
}

if ($unwarn != '')
{
$msg = "Your Warning Has Been Removed";
$userid = implode(", ", $_POST['warndisable']);
mysql_query("INSERT INTO messages (poster, sender, receiver, added, msg) VALUES ('0', '0', '".$userid."', '" . get_date_time() . "', " . sqlesc($msg) . ")") or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());

$r = mysql_query("SELECT modcomment FROM users WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")")or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
$user = mysql_fetch_array($r);
$exmodcomment = $user["modcomment"];
$modcomment = gmdate("Y-m-d") . " - Warning Removed By " . $CURUSER['username'] . ".\n". $modcomment . $exmodcomment;
mysql_query("UPDATE users SET modcomment=" . sqlesc($modcomment) . " WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")") or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());

$do="UPDATE users SET warned='no' WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")";
$res=mysql_query($do);
}

if ($warn != '')
{
if (empty($_POST["warnpm"]))
bark("Error", "You must type a reason/mod comment.");

$msg = "You have received a warning, Reason: $warnpm";
$userid = implode(", ", $_POST['warndisable']);
$r = mysql_query("SELECT modcomment FROM users WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")")or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
$user = mysql_fetch_array($r);
$exmodcomment = $user["modcomment"];
$modcomment = gmdate("Y-m-d") . " - Warned by " . $CURUSER['username'] . ".\nReason: $warnpm\n" . $modcomment . $exmodcomment;
mysql_query("UPDATE users SET modcomment=" . sqlesc($modcomment) . " WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")") or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());

$do="UPDATE users SET warned='yes' WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")";
$res=mysql_query($do);
mysql_query("INSERT INTO messages (poster, sender, receiver, added, msg) VALUES ('0', '0', '".$userid."', '" . get_date_time() . "', " . sqlesc($msg) . ")") or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
}

}
$referer = $_POST["referer"];
header("Location: $referer");

?>