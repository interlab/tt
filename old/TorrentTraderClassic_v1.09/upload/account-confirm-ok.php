<?php

require_once("backend/functions.php");
dbconn();

if (!mkglobal("type")) {
    die();
}

if ($type == "signup" && mkglobal("email")) {
	stdhead($txt['ACCOUNT_USER_SIGNUP']);
        begin_frame($txt['ACCOUNT_SIGNUP_SUCCESS']);
		if (!$ACONFIRM) {
			print("" . ACCOUNT_CONFIRM_SENT_TO_ADDY . " (" . htmlspecialchars($email) . "). " . ACCOUNT_CONFIRM_SENT_TO_ADDY_REST . " <br/ >");
		} else {
			print("" . ACCOUNT_CONFIRM_SENT_TO_ADDY . " (" . htmlspecialchars($email) . "). An admin needs to approve your account before you can use it <br/ >");
		}
	end_frame();
}
elseif ($type == "confirmed") {
	stdhead($txt['ACCOUNT_ALREADY_CONFIRMED']);
        begin_frame($txt['ACCOUNT_ALREADY_CONFIRMED']);
	print("" . ACCOUNT_ALREADY_CONFIRMED . "\n");
	end_frame();
}

//invite code
elseif ($type == "invite" && mkglobal("email")) {
stdhead("User invite");
     Begin_frame();
		Print("<CENTER>Invite successful!</CENTER><br><BR>A confirmation email has been sent to the address you specified (" . htmlspecialchars($email) . "). They need to read and respond to this email before they can use their account. If they don't do this, the new account will be deleted automatically after a few days.");
	End_frame();
stdfoot();
}//end invite code

elseif ($type == "confirm") {
	if (isset($CURUSER)) {
		stdhead($txt['ACCOUNT_SIGNUP_CONFIRMATION']);
		begin_frame($txt['ACCOUNT_SUCCESS_CONFIRMED']);
		print("" . ACCOUNT_ACTIVATED . " <a href=". $SITEURL ."/index.php>" . ACCOUNT_ACTIVATED_REST . "\n");
		print($txt['ACCOUNT_BEFOR_USING'] . $SITENAME . " " . ACCOUNT_BEFOR_USING_REST ."\n");
		end_frame();
	}
	else {
		stdhead($txt['ACCOUNT_SIGNUP_CONFIRMATION']);
		begin_frame($txt['ACCOUNT_SUCCESS_CONFIRMED']);
		print($txt['ACCOUNT_ACTIVATED']);
		end_frame();
	}
        //send welcome pm
    if ($WELCOMEPMON)
    {
        $added = sqlesc(get_date_time());
        mysql_query("INSERT INTO messages (poster, sender, receiver, msg, added) VALUES('0', '0', '$id', '$WELCOMEPMMSG', '$added')");
    }//end welcome pm
}
else
	die();

stdfoot();

