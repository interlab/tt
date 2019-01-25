<?php

require_once("backend/functions.php");
dbconn(true);

$id = 0 + $HTTP_GET_VARS["id"];
$md5 = $HTTP_GET_VARS["secret"];
$email = $HTTP_GET_VARS["email"];

stdhead();

if (!$id || !$md5 || !$email)
	bark("Couldn't change the email", "Error retrieving ID, KEY or Email.");


$res = mysql_query("SELECT editsecret FROM users WHERE id = $id");
$row = mysql_fetch_array($res);

if (!$row)
	bark("Couldn't change the email", "No user found wanting to change the email.");

$sec = hash_pad($row["editsecret"]);
if (preg_match('/^ *$/s', $sec))
	bark("Couldn't change the email", "No match found.");
if ($md5 != md5($sec . $email . $sec))
	bark("Couldn't change the email", "No md5.");

mysql_query("UPDATE users SET editsecret='', email=" . sqlesc($email) . " WHERE id=$id AND editsecret=" . sqlesc($row["editsecret"]));

if (!mysql_affected_rows())
	bark("Couldn't change the email", "No affected rows.");

header("Refresh: 0; url=$SITEURL/account-settings.php?emailch=1");

stdfoot();
?>