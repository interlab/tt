<?php

dbconn();

// Confirm Invite
if (isset($_GET['confirm'])) {
    $id = (int) ($_GET["id"] ?? 0);
    $md5 = $_GET["secret"] ?? '';

    if (! $id) {
        httperr();
    }

    $count = DB::fetchColumn("SELECT COUNT(*) FROM users");

    if ($count >= $invites)
        stderr("Sorry", "The current user account limit (" . number_format($invites) .
            ") has been reached. Inactive accounts are pruned all the time, please check back again later...");

    $row = DB::fetchAssoc("SELECT editsecret, secret, status FROM users WHERE id = $id");

    if (! $row) {
        httperr();
    }

    if ($row["status"] != "pending") {
        header("Refresh: 0; url=account-confirm-ok.php?type=confirmed");
        exit();
    }

    $sec = hash_pad($row["editsecret"]);
    if ($md5 != md5($sec)) {
        httperr();
    }

    $secret = $row["secret"];
    $psecret = md5($row["editsecret"]);
    stdhead("Confirm Invite");
    begin_frame("Confirm Invite");
    ?>

    Note: You need cookies enabled to sign up or log in.
    <p>
    <form method="post" action="invite.php?id=<?= $id ?>&secret=<?= $psecret ?>">
        <input type="hidden" name="sa" value="takeconfirminvite">
    <CENTER><table border="0" cellspacing=0 cellpadding="3" width=90%>
    <tr><td align="right" class="heading">Desired Username:</td>
    <td align=left><input type="text" size="40" name="wantusername" /></td></tr>
    <tr><td align="right" class="heading">Pick a password:</td>
    <td align=left><input type="password" size="40" name="wantpassword" /></td></tr>
    <tr><td align="right" class="heading">Enter password again:</td>
    <td align=left><input type="password" size="40" name="passagain" /></td></tr>

    </td></tr>
    <tr><td align="right" class="heading"></td>
    <td align=left><input type=checkbox name=rulesverify value=yes> I have read the site 
    <a href=/rules.php/ target=_blank font color=red>rules</a> page.<br>
    <input type=checkbox name=faqverify value=yes> I agree to read the 
    <a href=/faq.php/ target=_blank font color=red>FAQ</a> before asking questions.<br>
    <input type=checkbox name=ageverify value=yes> I am at least 13 years old.</td></tr>
    <tr><td colspan="2" align="center">
        <input type=submit value="Sign up! (PRESS ONLY ONCE)" style='height: 25px'>
    </td></tr>
    </table></CENTER>
    </form>
    <?php
    end_frame();
    stdfoot();

    die('');
}

// POST
// -- sa: takeconfirminvite
if (isset($_REQUEST['sa']) && $_REQUEST['sa'] === 'takeconfirminvite') {
    $id = (int) ($_GET['id'] ?? 0);
    $md5 = $_GET['secret'] ?? '';
    if (!$id) {
        httperr();
    }

    $total = DB::fetchColumn('SELECT COUNT(*) FROM users');
    if ($total >= $invites) {
        stderr('Error', 'Sorry, user limit reached. Please try again later.');
    }

    $row = DB::fetchAssoc('SELECT editsecret, status FROM users WHERE id = ' . $id);

    if (!$row) {
        httperr();
    }

    if ($row['status'] != 'pending') {
        header('Refresh: 0; url=account-confirm-ok.php?type=confirmed');
        exit();
    }

    $sec = hash_pad($row['editsecret']);
    if ($md5 != md5($sec)) {
        httperr();
    }
    if (empty($wantusername) || empty($wantpassword)) {
        barkmsg('Don\'t leave any fields blank.');
    }
    if (!mkglobal('wantusername:wantpassword:passagain')) {
        die('field not found');
    }

    function barkmsg($msg)
    {
        stdhead();
        begin_frame('Error');
        print('There has been a error: ' . $msg);
        end_frame();
        stdfoot();
        exit;
    }

    function isportopen($port)
    {
        global $HTTP_SERVER_VARS;

        $sd = @fsockopen($HTTP_SERVER_VARS['REMOTE_ADDR'], $port, $errno, $errstr, 1);
        if ($sd) {
            fclose($sd);
            return true;
        } else {
            return false;
        }
    }

    if (strlen($wantusername) > 12)
        barkmsg('Sorry, username is too long (max is 12 chars)');

    if ($wantpassword != $passagain)
        barkmsg("The passwords didn't match! Must've typoed. Try again.");

    if (strlen($wantpassword) < 6)
        barkmsg('Sorry, password is too short (min is 6 chars)');

    if (strlen($wantpassword) > 40)
        barkmsg('Sorry, password is too long (max is 40 chars)');

    if ($wantpassword == $wantusername)
        barkmsg('Sorry, password cannot be same as user name.');

    if (!validusername($wantusername))
        barkmsg('Invalid username.');

    // make sure user agrees to everything...
    if ($_POST['rulesverify'] != 'yes'
            || $_POST['faqverify'] != 'yes'
            || $_POST['ageverify'] != 'yes'
    ) {
        stderr('Signup failed', "Sorry, you're not qualified to become a member of this site.");
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
            barkmsg('Username already exists!');
        } else {
            barkmsg('Database Update Failed');
        }
    }

    // logincookie($id, $wantpasshash);

    header('Refresh: 0; url=account-confirm-ok.php?type=confirm');
    
    die('');
}

loggedinorreturn();

// POST
// -- sa: takeinvite
if (isset($_REQUEST['sa']) && $_REQUEST['sa'] === 'takeinvite') {
    //if (get_user_class() < UC_USER)
    //   stderr("Error", "Access denied.");

    $count = DB::fetchColumn("SELECT COUNT(*) FROM users");

    if ($count >= $invites)
        stderr("Error", "Sorry, user limit reached. Please try again later.");

    if ($CURUSER["invites"] == 0)
        stderr("Sorry","No invites!");

    $mess = $_POST["mess"] ?? '';

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

    if (!validemail($email)) {
        barkmsg("That doesn't look like a valid email address.");
    }

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
    $invites = $CURUSER["invites"] - 1;
    $invitees = $CURUSER["invitees"];
    $invitees2 = $id . ' ' . $invitees;
    $ret2 = DB::executeUpdate('UPDATE users SET invites = ?, invitees = ? WHERE id = ?', [$invites, $invitees2, $id2]);
    $username = $CURUSER["username"];
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

    die('');
}

if (! $INVITEONLY) {
    stdhead("Invite");
    begin_frame("Invite");
    echo "<BR><BR>Invites are disabled, please use the register link.<BR><BR>";
    end_frame();
    stdfoot();
    exit;
}


stdhead("Invite");
begin_frame("Invite");

$num = DB::fetchColumn("SELECT COUNT(*) FROM users");

if ($num >= $invites) {
	print("Sorry, The current user account limit (" . number_format($invites)
        . ") has been reached. Inactive accounts are pruned all the time, please check back again later...");
	end_frame();
	exit;
}

if (! $CURUSER["invites"]) {
	print("Sorry, No invites!");
	end_frame();
	exit;
}
?>

<p>
<form method="post" action="invite.php">
    <input type="hidden" name="sa" value="takeinvite">
<table border="0" cellspacing=0 cellpadding="3">
<tr valign=top><td align="right" class="heading"><B>Email Address:</B></td>
<td align=left><input type="text" size="40" name="email" />
<table width=250 border=0 cellspacing=0 cellpadding=0>
<tr><td class=embedded><font class=small>Please make sure this is a valid 
email address, the recipient will receive a confirmation email.</td></tr>
</font></td></tr></table>
<tr><td align="right" class="heading"><B>Message:</B></td>
<td align=left><textarea name="mess" rows="10" cols="80"></textarea>
</td></tr>
<tr><td colspan="2" align="center"><input type=submit value="Send Invite (PRESS ONLY ONCE)" style='height: 25px'></td></tr>
</table>
</form>
<?php
end_frame();
stdfoot();
