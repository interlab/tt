<?
dbconn(true);
$DEBUGMODE = '0';//debug on or off   

if ($CURUSER){

	if ($DEBUGMODE == '1'){
		echo "RATIOWARN MODULE LOADED<br>"; ###DEBUG PURPOSES
	}

	// CALC USERS RATIO
    if ($CURUSER["downloaded"] != "0"){
        $userratio = number_format($CURUSER["uploaded"] / $CURUSER["downloaded"], 2);
    }
	
    $userid = $CURUSER["id"];

    $warningmsg ="This is a warning. Your ratio is too low and has been low for $RATIOWARN_TIME days. You have $RATIOWARN_BAN days to get your ratio above $RATIOWARN_AMMOUNT or you will be banned!";
    
  if ($userratio <= $RATIOWARN_AMMOUNT && $userratio && (get_user_class() == UC_USER) && !$CURUSER["donated"]){
  
	if ($DEBUGMODE == '1'){
		echo "RATIOWARN MODULE LOADED :: POOR RATIO (Current Ratio: $userratio) - (Minimum Ratio: $RATIOWARN_AMMOUNT)<br>"; ###DEBUG PURPOSES
	}

    //CHECK TO SEE IF USER IS ALREADY IN RATIOWARN TABLE
    $check = mysql_query("SELECT * FROM ratiowarn WHERE userid='$userid'"); 
    
    //USER IS IN TABLE
	if ((mysql_num_rows($check) != "0")){
		$status = mysql_fetch_assoc($check);
		
		//CHECK WHEN WARNED USER WAS WARNED
		if($status["warned"] == "yes"){
              
			if ($DEBUGMODE == '1'){
				echo "RATIOWARN MODULE LOADED :: USER WARNED PREVIOUSLY (Current Ratio: $userratio) - (Minimum Ratio: $RATIOWARN_AMMOUNT)<br>"; ###DEBUG PURPOSES
			}

			$sql = mysql_query("SELECT warntime,TO_DAYS(NOW()) - TO_DAYS(warntime) as difference FROM ratiowarn WHERE userid='$userid'");
			$warnedate = mysql_fetch_assoc($sql);
			
			if ($warnedate["difference"] > $RATIOWARN_BAN){ //BAN IF USER HAS BEEN WARNED MORE THAN WARNDAYS

				if ($DEBUGMODE == '1'){
					echo "RATIOWARN MODULE LOADED :: USER BANNED (Current Ratio: $userratio) - (Minimum Ratio: $RATIOWARN_AMMOUNT)<br>"; ###DEBUG PURPOSES
				}
                     
				mysql_query("UPDATE ratiowarn SET banned='yes' WHERE userid='$userid'");
				mysql_query("UPDATE users SET enabled='no' WHERE id='$userid'");
                        

			}

		}else{ //ELSE USER MUST NOW BE WARNED

			if ($DEBUGMODE == '1'){
				echo "RATIOWARN MODULE LOADED :: WATCHED, USER NOT PREVIOUSLY WARNED (Current Ratio: $userratio) - (Minimum Ratio: $RATIOWARN_AMMOUNT)<br>"; ###DEBUG PURPOSES
			}     
			
			$sql = mysql_query("SELECT ratiodate,TO_DAYS(NOW()) - TO_DAYS(ratiodate) as difference FROM ratiowarn WHERE userid='$userid'");
            
			$notificationdate = mysql_fetch_assoc($sql);
            
			if ($notificationdate["difference"] > $RATIOWARN_TIME){//if notification date is > $RATIOWARN_TIME
						
				if ($DEBUGMODE == '1'){
					echo "RATIOWARN MODULE LOADED :: WATCHED, USER NOW WARNED (Current Ratio: $userratio) - (Minimum Ratio: $RATIOWARN_AMMOUNT)<br>"; ###DEBUG PURPOSES
				}
				
				mysql_query("UPDATE ratiowarn SET warntime=NOW() WHERE userid='$userid'");    //add to warned list
				mysql_query("UPDATE ratiowarn SET warned='yes' WHERE userid='$userid'");//warn
				mysql_query("UPDATE users SET warned='yes' WHERE id='$userid'");
				mysql_query("INSERT INTO messages (poster, sender, receiver, added, msg) VALUES ('0', '0', $userid, NOW(), " . sqlesc($warningmsg) . ")");   //send pm

			}
		}

   }else{ //ELSE USER IS IN "NOT" IN TABLE, SO LETS WATCH THEM
		
		if ($DEBUGMODE == '1'){
			echo "RATIOWARN MODULE LOADED :: USER ADDED TO WATCH LIST (Current Ratio: $userratio) - (Minimum Ratio: $RATIOWARN_AMMOUNT)<br>"; ###DEBUG PURPOSES
		}

        mysql_query("INSERT INTO ratiowarn (userid, ratiodate) VALUES ($userid, NOW())");
   }
  }


    
    //MAKE SURE USER IS NOT IN RATIOWARN TABLE, GOOD RATIO, REMOVE FROM RATIOWARN
    if ($userratio > $RATIOWARN_AMMOUNT && $userratio){
		
		if ($DEBUGMODE == '1'){
			echo "RATIOWARN MODULE LOADED :: USER HAS OK RATIO (Current Ratio: $userratio) - (Minimum Ratio: $RATIOWARN_AMMOUNT)<br>"; ###DEBUG PURPOSES
		}

        $check = mysql_query("SELECT * FROM ratiowarn WHERE userid='$userid'");
        
        if(mysql_num_rows($check) != "0"){

			if ($DEBUGMODE == '1'){
				echo "RATIOWARN MODULE LOADED :: USER REMOVED FROM LIST<br>"; ###DEBUG PURPOSES
			}

            mysql_query("DELETE FROM ratiowarn WHERE userid='$userid'");
            mysql_query("UPDATE users SET warned='no' WHERE id='$userid'");
			mysql_query("UPDATE users SET enabled='yes' WHERE id='$userid'");
        }

    }

}// END CURUSER

?>