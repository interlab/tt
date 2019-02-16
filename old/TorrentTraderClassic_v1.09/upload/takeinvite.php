<?php

require_once("backend/functions.php");

dbconn();

//if (get_user_class() < UC_USER)
//stderr("Error", "Access denied.");

$count = DB::fetchColumn("SELECT COUNT(*) FROM users");

if ($count >= $invites)
    stderr("Error", "Sorry, user limit reached. Please try again later.");

if ($CURUSER["invites"] == 0)
    stderr("Sorry","No invites!");

$mess = $_POST["mess"];

if (! $mess)
    barkmsg("You must enter a message!");

if (!mkglobal("email"))
    die();


function barkmsg($msg)
{
    stdhead();
    begin_frame("ERROR");
    echo "<BR><BR>Invite Failed!<BR><BR>";
    echo $msg;
    end_frame();
    stdfoot();
    exit;
}

if (!validemail($email))
    barkmsg("That doesn't look like a valid email address.");

$a = DB::fetchColumn('select count(*) from users where email = ?', [$email]);
if ($a) {
    barkmsg("The e-mail address $email is already in use.");
}

$a = DB::fetchColumn('select count(*) from users where real_name = ? OR username = ?',
    [$username, $username]
);
if ($a) {
    barkmsg("The username $username is already in use."); 
}

$secret = mksecret();
$editsecret = mksecret();
$username = 'User-'. generateRandomString(15);

$password = generateRandomString(15);
$password = password_hash($password, PASSWORD_DEFAULT);

try {
    $ret = DB::executeUpdate('
        INSERT INTO users (username, real_name, password, secret, editsecret,
            email, status, invited_by, added, about_myself, passkey)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
        [$username, $username, $password, $secret, $editsecret,
        $email, 'pending', $CURUSER["id"], get_date_time(), '', generateRandomString(32)
        ]
    );

    $id = DB::lastInsertId();

    if (! $ret) {
        barkmsg("Mysql Error!");
    }
} catch(\Exception $e) {
    trigger_error($e->getMessage(), E_USER_ERROR);
    if ($e->getCode() === 1062) {
        barkmsg("Username already exists!");
    } else {
        barkmsg("Mysql Error!");
    }
}

$id2 = $CURUSER["id"];
$invites = $CURUSER["invites"]-1;
$invitees = $CURUSER["invitees"];
$invitees2 = "$id $invitees";
$ret2 = DB::query("UPDATE users SET invites='$invites', invitees='$invitees2' WHERE id = $id2");
$username=$CURUSER["username"];

$psecret = md5($editsecret);

$message = ($html ? strip_tags($mess) : $mess);

$body = <<<EOD
You have been invited to $SITENAME by $username. They have
specified this address ($email) as your email. If you do not know this person, please ignore this email. Please do not reply.

Message:
-------------------------------------------------------------------------------
$message
-------------------------------------------------------------------------------

This is a private site and you must agree to the rules before you can enter:

$SITEURL/rules.php

$SITEURL/faq.php


To confirm your invitation, you have to follow this link:

$SITEURL/invite.php?confirm=1&id=$id&secret=$psecret

After you do this, you will be able to use your new account. If you fail to
do this, your account will be deleted within a few days. We urge you to read
the RULES and FAQ before you start using $SITENAME.
EOD;
//mail($email, "$SITENAME user registration confirmation", $body, "From: $SITEEMAIL", "-f$SITEEMAIL");
mail($email, "$SITENAME user registration confirmation", $body, "From: $SITENAME <$SITEEMAIL>");

header("Refresh: 0; url=account-confirm-ok.php?type=invite&email=" . urlencode($email));

