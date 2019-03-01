<?php

dbconn(true);
$DEBUGMODE = true; // bool

global $userratio;

if ($CURUSER) {
	if ($DEBUGMODE) {
		echo "RATIOWARN MODULE LOADED<br>"; ###DEBUG PURPOSES
	}

	// CALC USERS RATIO
    if ($CURUSER["downloaded"] > (1024**3)) {
        $userratio = number_format($CURUSER["uploaded"] / $CURUSER["downloaded"], 2);
    }

    $userid = $CURUSER["id"];

    // $warningmsg ="This is a warning. Your ratio is too low and has been low for $RATIOWARN_TIME days.
    // You have $RATIOWARN_BAN days to get your ratio above $RATIOWARN_AMMOUNT or you will be banned!";
    $warningmsg = 'Это предупреждение. Ваш рейтинг слишком низкий уже более ' . $RATIOWARN_TIME
        .  ' дней. У вас есть ' . $RATIOWARN_BAN . ' дней чтобы поднять ваш рейтинг выше '
        . $RATIOWARN_AMMOUNT . ' или вы будете забанены!';

    if ($userratio <= $RATIOWARN_AMMOUNT && $userratio && (get_user_class() == UC_USER) && !$CURUSER["donated"]) {
        if ($DEBUGMODE) {
            # DEBUG PURPOSES
            echo "RATIOWARN MODULE LOADED :: POOR RATIO (Current Ratio: $userratio) - (Minimum Ratio: $RATIOWARN_AMMOUNT)<br>";
        }

        // CHECK TO SEE IF USER IS ALREADY IN RATIOWARN TABLE
        $status = DB::fetchAssoc("SELECT * FROM ratiowarn WHERE userid='$userid'");

        // USER IS IN TABLE
        if ($status) {
            // CHECK WHEN WARNED USER WAS WARNED
            if ($status["warned"] == "yes") {
                if ($DEBUGMODE == '1'){
                    echo "RATIOWARN MODULE LOADED :: USER WARNED PREVIOUSLY (Current Ratio: $userratio) - (Minimum Ratio: $RATIOWARN_AMMOUNT)<br>"; ###DEBUG PURPOSES
                }

                $warnedate = DB::fetchAssoc("SELECT warntime, TO_DAYS(NOW()) - TO_DAYS(warntime) as difference FROM ratiowarn WHERE userid='$userid'");

                if ($warnedate["difference"] > $RATIOWARN_BAN){ //BAN IF USER HAS BEEN WARNED MORE THAN WARNDAYS

                    if ($DEBUGMODE) {
                        echo "RATIOWARN MODULE LOADED :: USER BANNED (Current Ratio: $userratio) - (Minimum Ratio: $RATIOWARN_AMMOUNT)<br>"; ###DEBUG PURPOSES
                    }

                    DB::query("UPDATE ratiowarn SET banned='yes' WHERE userid='$userid'");
                    DB::query("UPDATE users SET enabled='no' WHERE id='$userid'");
                }

            } else { //ELSE USER MUST NOW BE WARNED
                if ($DEBUGMODE) {
                    echo "RATIOWARN MODULE LOADED :: WATCHED, USER NOT PREVIOUSLY WARNED (Current Ratio: $userratio) - (Minimum Ratio: $RATIOWARN_AMMOUNT)<br>"; ###DEBUG PURPOSES
                }

                $notificationdate = DB::fetchAssoc("SELECT ratiodate,TO_DAYS(NOW()) - TO_DAYS(ratiodate) as difference FROM ratiowarn WHERE userid='$userid'");

                if ($notificationdate["difference"] > $RATIOWARN_TIME){//if notification date is > $RATIOWARN_TIME
                    if ($DEBUGMODE) {
                        echo "RATIOWARN MODULE LOADED :: WATCHED, USER NOW WARNED (Current Ratio: $userratio) - (Minimum Ratio: $RATIOWARN_AMMOUNT)<br>"; ###DEBUG PURPOSES
                    }

                    DB::query("UPDATE ratiowarn SET warntime=NOW() WHERE userid='$userid'");    //add to warned list
                    DB::query("UPDATE ratiowarn SET warned='yes' WHERE userid='$userid'");//warn
                    DB::query("UPDATE users SET warned='yes' WHERE id='$userid'");
                    send_pm( 0, $userid, $warningmsg );
                }
            }
        } else { //ELSE USER IS IN "NOT" IN TABLE, SO LETS WATCH THEM
            if ($DEBUGMODE) {
                echo "RATIOWARN MODULE LOADED :: USER ADDED TO WATCH LIST (Current Ratio: $userratio) - (Minimum Ratio: $RATIOWARN_AMMOUNT)<br>"; ###DEBUG PURPOSES
            }

            DB::query("INSERT INTO ratiowarn (userid, ratiodate) VALUES ($userid, NOW())");
        }
    }

    // MAKE SURE USER IS NOT IN RATIOWARN TABLE, GOOD RATIO, REMOVE FROM RATIOWARN
    if ($userratio > $RATIOWARN_AMMOUNT && $userratio) {
		if ($DEBUGMODE == '1'){
			echo "RATIOWARN MODULE LOADED :: USER HAS OK RATIO (Current Ratio: $userratio) - (Minimum Ratio: $RATIOWARN_AMMOUNT)<br>"; ###DEBUG PURPOSES
		}

        $check = DB::fetchAssoc("SELECT * FROM ratiowarn WHERE userid='$userid'");
        if ($check) {
			if ($DEBUGMODE) {
				echo "RATIOWARN MODULE LOADED :: USER REMOVED FROM LIST<br>"; ###DEBUG PURPOSES
			}

            DB::query("DELETE FROM ratiowarn WHERE userid='$userid'");
            DB::query("UPDATE users SET warned='no' WHERE id='$userid'");
			DB::query("UPDATE users SET enabled='yes' WHERE id='$userid'");
        }
    }
} // END CURUSER
