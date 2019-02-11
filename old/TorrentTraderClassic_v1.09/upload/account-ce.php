<?php

require_once("backend/functions.php");
dbconn(true);

$id = 0 + $_GET["id"];
$md5 = $_GET["secret"];
$email = $_GET["email"];

stdhead();

if (!$id || !$md5 || !$email)
	bark("Couldn't change the email", "Error retrieving ID, KEY or Email.");


$row = DB::fetchAssoc('SELECT editsecret FROM users WHERE id = '.$id);

if (!$row)
	bark("Couldn't change the email", "No user found wanting to change the email.");

$sec = hash_pad($row["editsecret"]);
if (preg_match('/^ *$/s', $sec))
	bark("Couldn't change the email", "No match found.");
if ($md5 != md5($sec . $email . $sec))
	bark("Couldn't change the email", "No md5.");

$aff_rows = DB::executeUpdate('
    UPDATE users SET editsecret = ?, email = ? WHERE id = ' . $id . ' AND editsecret = ?',
    ['', $email, $row["editsecret"]]
);

if (! $aff_rows)
	bark("Couldn't change the email", "No affected rows.");

header("Refresh: 0; url=$SITEURL/account-settings.php?emailch=1");

stdfoot();
