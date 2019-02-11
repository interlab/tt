<?php

require_once("backend/functions.php");
dbconn(false);

$_GET["id"] = (int) ($_GET["id"] ?? 0);
$_GET["secret"] = $_GET["secret"] ?? '';
$kind = "0";

if (is_valid_id($_POST["id"]) && strlen($_POST["secret"]) == 32) {
    $password = $_POST["password"];
    $password1 = $_POST["password1"];
    if (empty($password) || empty($password1)) {
        $kind = "Error";
        $msg = "You must fill out the form.";
    }
    if ($password != $password1) {
        $kind = "Error";
        $msg = "Password doesn't match.";
    } else {
        $id = (int) $_POST["id"];
        DB::executeUpdate('UPDATE `users` SET `password` = ?, `secret` = ? WHERE `id` = ? AND MD5(`secret`) = ?',
            [password_hash($password, PASSWORD_DEFAULT), mksecret(), $id, $_POST["secret"]]
        );
        $kind = "Success";
        $msg = "Password changed.";
        $row = DB::fetchAssoc('SELECT username, email FROM users WHERE id = ' . $id);
        write_log("[RECOVER] $_SERVER[REMOTE_ADDR] reset the password for $row[username] (Email: $row[email])");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_GET["take"] == 1) {
    $email = trim($_POST["email"]);

    if (! validemail($email)) {
        $msg = "This is not a valid email";
        $kind = "Error";
    } else {
        $arr = DB::fetchAssoc('SELECT id, status, username, email FROM users WHERE email = ? LIMIT 1', [$email]);
        if (! $arr) {
            $msg = "No members with this email address found";
            $kind = "Error";
        }

        if ($arr["status"] == "pending") {
            $msg = "You cannot request the details for unconfirmed accounts.";
            $kind = "Error";
        }

        if ($arr && !$kind) {
            $sec = mksecret();
            $secmd5 = md5($sec);
            $id = $arr['id'];

            write_log("[RECOVER] $_SERVER[REMOTE_ADDR] requested the details for $arr[email]");

            $body = "Someone from " . $_SERVER["REMOTE_ADDR"] . 
                ", hopefully you, requested that the account details for the account associated with this email address ($email) be mailed back. 
                \r\n\r\n Here is the information we have on file for this account: \r\n\r\n User name: "
                .$arr["username"]." \r\n To change your password, you have to follow this link:
                \n\n$SITEURL/account-recover.php?id=$id&secret=$secmd5\n\n\n$SITENAME\r\n";

            mail($arr["email"], "Your account details", $body, "From: $SITEEMAIL", "-f$SITEEMAIL")
                or show_error_msg("Error", "Unable to send mail. Please contact an administrator about this error.", 1);

            $res2 = DB::executeUpdate("UPDATE `users` SET `secret` = ? WHERE `email`= ? LIMIT 1",
                [$sec, $email]
            );

            $msg = "The account details have been mailed to <b>". h($email)
                ."</b>.<br>Please allow a few minutes for the mail to arrive.";

            $kind = "Success";
        }
    }
}

stdhead();

begin_frame($txt['RECOVER_ACCOUNT'], 'center');
if ($kind != "0") {
    show_error_msg("Notice","$kind: $msg",0);
}

if (is_valid_id($_GET["id"]) && strlen($_GET["secret"]) == 32) {?>
<form method=post action=account-recover.php>
<table border=0 cellspacing=0 cellpadding=5>
    <tr>
        <td class=rowhead>
            <B><?=NEW_PASSWORD?></B>:
        </td>
        <td>
            <input type=hidden name='secret' value='<?=$_GET['secret']?>'>
            <input type=hidden name='id' value='<?=$_GET['id']?>'>
            <input type=password size=40 name='password'>
        </td>
    </tr>
    <tr>
        <td class=rowhead>
            <B><?= $txt['REPEAT'] ?></B>:
        </td>
        <td>
            <input type=password size=40 name='password1'>
        </td>
    </tr>
    <tr>
        <td class=rowhead>&nbsp;</td>
        <td><input type='submit' value='<?= $txt['SUBMIT'] ?>'></td>
    </tr>
</table>
</form>
<?php } else { echo $txt['USE_FORM_FOR_ACCOUNT_DETAILS']; ?>

<form method=post action='account-recover.php?take=1'>
    <table border=0 cellspacing=0 cellpadding=5>
        <tr>
            <td class=rowhead><B><?= $txt['EMAIL_ADDRESS'] ?>:</B> </td>
            <td><input type=text size=40 name=email>&nbsp;<input type=submit value='<?= $txt['SUBMIT'] ?>' class=btn></td>
        </tr>
    </table>
</form>

<?php
}
end_frame();
stdfoot();
