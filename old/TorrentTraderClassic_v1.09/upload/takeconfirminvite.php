<?php

require_once("backend/functions.php");

$id = 0 + $_GET["id"];
$md5 = $_GET["secret"];
if (!$id)
    httperr();

dbconn();

$res = mysql_query("SELECT COUNT(*) FROM users");
$arr = mysql_fetch_row($res);
if ($arr[0] >= $invites)
    stderr("Error", "Sorry, user limit reached. Please try again later.");

$res = mysql_query("SELECT editsecret, status FROM users WHERE id = $id");
$row = mysql_fetch_array($res);

if (!$row)
    httperr();

if ($row["status"] != "pending") {
    header("Refresh: 0; url=account-confirm-ok.php?type=confirmed");
    exit();
}

$sec = hash_pad($row["editsecret"]);
if ($md5 != md5($sec))
    httperr();
if (empty($wantusername) || empty($wantpassword))
    barkmsg("Don't leave any fields blank.");
if (!mkglobal("wantusername:wantpassword:passagain"))
    die();

function barkmsg($msg)
{
    stdhead();
    begin_frame("Error");
    print("There has been a error: " . $msg . "");
    end_frame();
    stdfoot();
    exit;
}

function isportopen($port)
{
    global $HTTP_SERVER_VARS;

    $sd = @fsockopen($HTTP_SERVER_VARS["REMOTE_ADDR"], $port, $errno, $errstr, 1);
    if ($sd) {
        fclose($sd);
        return true;
    } else {
        return false;
    }
}

if (strlen($wantusername) > 12)
    barkmsg("Sorry, username is too long (max is 12 chars)");

if ($wantpassword != $passagain)
    barkmsg("The passwords didn't match! Must've typoed. Try again.");

if (strlen($wantpassword) < 6)
    barkmsg("Sorry, password is too short (min is 6 chars)");

if (strlen($wantpassword) > 40)
    barkmsg("Sorry, password is too long (max is 40 chars)");

if ($wantpassword == $wantusername)
    barkmsg("Sorry, password cannot be same as user name.");

if (!validusername($wantusername))
    barkmsg("Invalid username.");

// make sure user agrees to everything...
if ($HTTP_POST_VARS["rulesverify"] != "yes"
        || $HTTP_POST_VARS["faqverify"] != "yes"
        || $HTTP_POST_VARS["ageverify"] != "yes"
) {
    stderr("Signup failed", "Sorry, you're not qualified to become a member of this site.");
}

$secret = mksecret();
// $wantpasshash = md5($wantpassword);
$wantpasshash = password_hash($wantpassword, PASSWORD_DEFAULT);

try {
    $ret = DB::executeUpdate('
        UPDATE users SET username = ?, real_name = ?, password = ?,
            status = ?, editsecret = ?, secret = ?
        WHERE id = ' . $id,
        [$wantusername, $wantusername, $wantpasshash, 'confirmed', '', $secret]
    );
} catch (\Exception $e) {
    trigger_error($e->getMessage(), E_USER_ERROR);
    if ($e->getCode() === 1062) {
        barkmsg("Username already exists!");
    } else {
        barkmsg("Database Update Failed");
    }
}

// logincookie($id, $wantpasshash);

header("Refresh: 0; url=account-confirm-ok.php?type=confirm");

