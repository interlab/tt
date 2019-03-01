<?php

dbconn();

loadLanguage();

$type = $_GET['type'] ?? '';
$email = $_GET['email'] ?? '';

if ($type == "signup" && $email !== '') {
    stdhead($txt['ACCOUNT_USER_SIGNUP']);
    begin_frame($txt['ACCOUNT_SIGNUP_SUCCESS']);
    if (! $ACONFIRM) {
        print($txt['ACCOUNT_CONFIRM_SENT_TO_ADDY'] . " (" . h($email) . "). "
            . $txt['ACCOUNT_CONFIRM_SENT_TO_ADDY_REST'] . " <br>");
    } else {
        print($txt['ACCOUNT_CONFIRM_SENT_TO_ADDY'] . " (" . h($email)
            . "). An admin needs to approve your account before you can use it <br>");
    }
    end_frame();
}
elseif ($type == "confirmed") {
    stdhead($txt['ACCOUNT_ALREADY_CONFIRMED']);
    begin_frame($txt['ACCOUNT_ALREADY_CONFIRMED']);
    print($txt['ACCOUNT_ALREADY_CONFIRMED']);
    end_frame();
}

// invite code
elseif ($type == "invite" && $email !== '') {
    stdhead("User invite");
    begin_frame();
    print("<CENTER>Invite successful!</CENTER><br><BR>A confirmation email has been sent to the address you specified ("
        . h($email) . "). They need to read and respond to this email before they can use their account."
        . " If they don't do this, the new account will be deleted automatically after a few days.");
    end_frame();
}// end invite code

elseif ($type == "confirm") {
    if (isset($CURUSER)) {
        stdhead($txt['ACCOUNT_SIGNUP_CONFIRMATION']);
        begin_frame($txt['ACCOUNT_SUCCESS_CONFIRMED']);
        print($txt['ACCOUNT_ACTIVATED'] . " <a href=". $SITEURL ."/index.php>" . $txt['ACCOUNT_ACTIVATED_REST']);
        print($txt['ACCOUNT_BEFOR_USING'] . $SITENAME . " " . $txt['ACCOUNT_BEFOR_USING_REST']);
        end_frame();
    } else {
        stdhead($txt['ACCOUNT_SIGNUP_CONFIRMATION']);
        begin_frame($txt['ACCOUNT_SUCCESS_CONFIRMED']);
        print($txt['ACCOUNT_ACTIVATED']);
        end_frame();
    }
    // send welcome pm
    /*
    if ($WELCOMEPMON) {
        DB::executeUpdate('INSERT INTO messages (poster, sender, receiver, msg, added) VALUES(?, ?, ?, ?, ?)',
            [0, 0, $id, $WELCOMEPMMSG, get_date_time()]
        );
    }
    */
    // end welcome pm
}
else {
    die('unknown request');
}
stdfoot();

