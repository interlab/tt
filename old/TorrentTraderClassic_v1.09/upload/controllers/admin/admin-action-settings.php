<?php

adminonly();
adminmenu();
// show menu
// output
begin_frame("Site Settings", 'center');

// page submitted, update
if ($do == 'save') {
    if ($CENSORWORDS_new == "1")
        $CENSORWORDS_temp = "true";
    else
        $CENSORWORDS_temp = "false";
    if ($WELCOMEPMON_new == "1")
        $WELCOMEPMON_temp = "true";
    else
        $WELCOMEPMON_temp = "false";
    if ($MEMBERSONLY_new == "1")
        $MEMBERSONLY_temp = "true";
    else
        $MEMBERSONLY_temp = "false";

    if ($MEMBERSONLY_WAIT_new == "1")
        $MEMBERSONLY_WAIT_temp = "true";
    else
        $MEMBERSONLY_WAIT_temp = "false";
        
    if ($RATIO_WARNINGON_new == "1")
        $RATIO_WARNINGON_temp = "true";
    else
        $RATIO_WARNINGON_temp = "false";

    if ($LOGGEDINONLY_new == "1")
        $LOGGEDINONLY_temp = "true";
    else
        $LOGGEDINONLY_temp = "false";

    if ($SITENOTICEON_new == "1")
        $SITENOTICEON_temp = "true";
    else
        $SITENOTICEON_temp = "false";

    if ($REMOVALSON_new == "1")
        $REMOVALSON_temp = "true";
    else
        $REMOVALSON_temp = "false";

    if ($NEWSON_new == "1")
        $NEWSON_temp = "true";
    else
        $NEWSON_temp = "false";

    if ($UPLOADERSONLY_new == "1")
        $UPLOADERSONLY_temp = "true";
    else
        $UPLOADERSONLY_temp = "false";

    if ($INVITEONLY_new == "1")
        $INVITEONLY_temp = "true";
    else
        $INVITEONLY_temp = "false";
    
    if ($ACONFIRM_new == "1")
        $ACONFIRM_temp = "true";
    else
        $ACONFIRM_temp = "false";

    if ($DONATEON_new == "1")
        $DONATEON_temp = "true";
    else
        $DONATEON_temp = "false";

    if ($DISCLAIMERON_new == "1")
        $DISCLAIMERON_temp = "true";
    else
        $DISCLAIMERON_temp = "false";

    if ($SHOUTBOX_new == "1")
        $SHOUTBOX_temp = "true";
    else
        $SHOUTBOX_temp = "false";

    if ($FORUMS_new == "1")
        $FORUMS_temp = "true";
    else
        $FORUMS_temp = "false";

    if ($DHT_new == "1")
        $DHT_temp = "true";
    else
        $DHT_temp = "false";
        
    if ($POLLON_new == "1")
        $POLLON_temp = "true";
    else
        $POLLON_temp = "false";

    if ($REQUESTSON_new == "1")
        $REQUESTSON_temp = "true";
    else
        $REQUESTSON_temp = "false";

    if ($IRCCHAT_new == "1")
        $IRCCHAT_temp = "true";
    else
        $IRCCHAT_temp = "false";

    if ($IRCANNOUNCE_new == "1")
        $IRCANNOUNCE_temp = "true";
    else
        $IRCANNOUNCE_temp = "false";

    if ($GLOBALBAN)
        $GLOBALBAN_temp = "true";
    else
        $GLOBALBAN_temp = "false";

    $config_settings_data = <<<EOD
<?php 

// MySQL Settings (please change these to reflect your MYSQL settings, all other settings can be changed via adminCP)
\$mysql_host = "$mysql_host_new";
\$mysql_user = "$mysql_user_new";
\$mysql_pass = "$mysql_pass_new";
\$mysql_db = "$mysql_db_new";

// Default Language / Theme Settings (These are currently set via the database, NOT THE ADMIN CP)
\$language = "$language";
\$theme = "$theme";

// Site Settings
\$SITENAME = "$SITENAME_new";
\$SITEEMAIL = "$SITEEMAIL_new";
\$SITEURL = "$SITEURL_new";
\$SITE_ONLINE = $SITE_ONLINE_new;
\$OFFLINEMSG = "$OFFLINEMSG_new";
\$UPLOADERSONLY = $UPLOADERSONLY_temp;
\$LOGGEDINONLY = $LOGGEDINONLY_temp;
\$INVITEONLY = $INVITEONLY_temp;
\$ACONFIRM = $ACONFIRM_temp;
\$WELCOMEPMON = $WELCOMEPMON_temp;
\$CENSORWORDS = $CENSORWORDS_temp;
\$MAXDISPLAYLENGTH = "$MAXDISPLAYLENGTH_new";
\$WELCOMEPMMSG = "$WELCOMEPMMSG_new";
\$DHT = $DHT_temp;
\$POLLON = $POLLON_temp;

//Setup Site Blocks
\$SITENOTICEON = $SITENOTICEON_temp;
\$REMOVALSON = $REMOVALSON_temp;
\$NEWSON = $NEWSON_temp;
\$DONATEON = $DONATEON_temp;
\$DISCLAIMERON = $DISCLAIMERON_temp;
\$SITENOTICE = <<<EOD\r\n$SITENOTICE_new\r\nEOD;
\$SHOUTBOX = $SHOUTBOX_temp;
\$FORUMS = $FORUMS_temp;
\$REQUESTSON = $REQUESTSON_temp;

//setup IRC Chat
\$IRCCHAT = $IRCCHAT_temp;
\$IRCCHANNEL = "$IRCCHANNEL_new";
\$IRCSERVER1 = "$IRCSERVER1_new";
\$IRCSERVER2 = "$IRCSERVER2_new";
\$IRCSERVER3 = "$IRCSERVER3_new";

//Setup IRC Announcer
\$IRCANNOUNCE = $IRCANNOUNCE_temp;
\$ANNOUNCEIP = "$ANNOUNCEIP_new";
\$ANNOUNCEPORT= "$ANNOUNCEPORT_new";

//WAIT TIME VARS
\$GIGSA= "$GIGSA_new";
\$RATIOA= "$RATIOA_new";
\$WAITA= "$WAITA_new";
\$GIGSB= "$GIGSB_new";
\$RATIOB= "$RATIOB_new";
\$WAITB= "$WAITB_new";
\$GIGSC= "$GIGSC_new";
\$RATIOC= "$RATIOC_new";
\$WAITC= "$WAITC_new";
\$GIGSD= "$GIGSD_new";
\$RATIOD= "$RATIOD_new";
\$WAITD= "$WAITD_new";

//RATIO WARNING VARS
\$RATIO_WARNINGON = "$RATIO_WARNINGON_temp";    //ratiowarn on/off
\$RATIOWARN_AMMOUNT = "$RATIOWARN_AMMOUNT_new"; //user warned if this ratio is held
\$RATIOWARN_TIME = "$RATIOWARN_TIME_new";   //ammount of time for user have have poor ratio before warning
\$RATIOWARN_BAN = "$RATIOWARN_BAN_new";     //ammount of time after warning to auto-ban user.

// Tracker Settings
\$torrent_dir = "$torrent_dir_new";
\$nfo_dir = "$nfo_dir_new";
\$image_dir = "$image_dir_new";
\$announce_urls = array();
\$announce_urls[] = "$announce_urls_new";
\$GLOBALBAN = $GLOBALBAN_temp;
\$MEMBERSONLY = $MEMBERSONLY_temp;
\$MEMBERSONLY_WAIT = $MEMBERSONLY_WAIT_temp;
\$RATIO_WARNINGON = $RATIO_WARNINGON_temp;
\$PEERLIMIT = "$PEERLIMIT_new";

// Advanced Settings for announce and cleanup
\$autoclean_interval = "$autoclean_interval_new";
\$max_torrent_size = "$max_torrent_size_new";
\$max_nfo_size = "$max_nfo_size_new";
\$max_image_size = "$max_image_size_new";
\$announce_interval = "$announce_interval_new";
\$signup_timeout = "$signup_timeout_new";
\$minvotes = "$minvotes_new";
\$maxsiteusers = "$maxsiteusers_new";
\$max_dead_torrent_time = "$max_dead_torrent_time_new";


?>
EOD;

    // create backup of config.php file first
    $old_config_file_read_handle = fopen("backend/config.php", "r");
    $old_config_file_write_handle = fopen("backend/oldconfig.php", "w");

    $old_config_file_contents = fread($old_config_file_read_handle, filesize("backend/config.php"));
    fwrite ($old_config_file_write_handle, $old_config_file_contents);

    fclose ($old_config_file_read_handle);
    fclose ($old_config_file_write_handle);

    // write onto current config file
    $new_config_file_handle = fopen("backend/config.php", "w");
    fwrite ($new_config_file_handle, $config_settings_data);
    fclose ($new_config_file_handle);
    //begin_frame("","center");
    print("<table border=0 cellspacing=0 cellpadding=5><td><center>");
    autolink("admin.php?act=settings", "Your Settings Were Updated");
    print("</center></td></tr></table>");
    //end_frame();
}

?>

		<form action='admin.php?act=settings&do=save' method='post'>
		<input type='hidden' name='sid' value='<?=$sid?>'>
		<input type='hidden' name='act' value='settings'>
		<input type='hidden' name='do'  value='save'>
		<div align="center">
		<table width='100%' cellspacing='3' cellpadding='3'>
		<tr>
		<td colspan="2"><b>Database Settings<br>&#9492;</b> Only modify these settings if you have changed the location of your database.</td>
		</tr>
		<tr>
		<td>MYSQL Host:</td>
		<td align='left'><input type='text' name='mysql_host_new' value='<?=$mysql_host?>' maxlength='50' size='50'></td>
		</tr>
		<tr>
		<td>MYSQL User:</td>
		<td align='left'>
		<input type='text' name='mysql_user_new' value='<?=$mysql_user?>' maxlength='50' size='50'></td>
		</tr>
		<tr>
		<td>MYSQL Pass:</td>
		<td align='left'>
		<input type='text' name='mysql_pass_new' value='<?=$mysql_pass?>' maxlength='50' size='50'></td>
		</tr>
		<tr>
		<td>MYSQL Database</td>
		<td align='left'>
		<input type='text' name='mysql_db_new' value='<?=$mysql_db?>' maxlength='50' size='50'></td>
		</tr>
		<tr>
		<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
		<td colspan="2"><b>File Storage Paths<br>&#9492;</b> Must be CHMOD 777 and absolute paths. See <a href="phpinfo.php" target="_blank">[php info]</a> for more info.</td>
		</tr>
		<tr>
		<td>Path to directory where .torrents will be stored:</td>
		<td align='left'>
		<input type='text' name='torrent_dir_new' value='<?=$torrent_dir?>' size='50'></td>
		</tr>
		<tr>
		<td>Path to directory where NFO files will be stored:</td>
		<td align='left'>
		<input type='text' name='nfo_dir_new' value='<?=$nfo_dir?>' size='50'></td>
		</tr>
		<tr>
		<td>Path to directory where image files will be stored:</td>
		<td align='left'>
		<input type='text' name='image_dir_new' value='<?=$image_dir?>' size='50'></td>
		</tr>
		<tr>
		<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
		<td colspan="2"><b>Tracker Configuration<br>&#9492;</b>Here you can configure your trackers main settings</td>
		</tr>
		<tr>
		<td>Site Name:</td>
		<td align='left'><input type='text' name='SITENAME_new' value='<?=$SITENAME?>' size='50'></td>
		</tr>
		<tr>
		<td>Tracker URL:</td>
		<td align='left'><input type='text' name='SITEURL_new' value='<?=$SITEURL?>' size='50'></td>
		</tr>
		<tr>
		<td>Announce Url:</td>
		<td align='left'>
		<input type='text' name='announce_urls_new' value='<?=$announce_urls[0]?>' size='50'></td>
		</tr>
		<tr>
		<td>Maximum Users Accounts:</td>
		<td align='left'>
		<input type='text' name='maxsiteusers_new' value='<?= $maxsiteusers ?>' size='50'></td>
		</tr>
		<tr>
		<td>Maximum Peers:</td>
		<td align='left'>
		<input type='text' name='PEERLIMIT_new' value='<?= $PEERLIMIT ?>' size='50'></td>
		</tr>
		<tr>
		<td>Email: (Signup emails etc will be sent from this address)</td>
		<td align='left'>
		<input type='text' name='SITEEMAIL_new' value='<?= $SITEEMAIL ?>' size='50'></td>
		</tr>
		<tr>
		<td>Tracker Status:</td>
		<td align='left'>
		<select name='SITE_ONLINE_new'>
		<option value='true' <?= $SITE_ONLINE ? "selected" : '' ?>>ONLINE
		<option value='false' <?= $SITE_ONLINE ? "selected" : '' ?>>OFFLINE
		</select></td>
		</tr>
		<tr>
		<td valign="top">Site Offline Message:<br><i> (HTML Allowed)</i></td>
		<td align='left'>
		<textarea name='OFFLINEMSG_new' cols="38" rows="8"><?= $OFFLINEMSG ?></textarea></td>
		</tr>
		<tr>
		<td>Require Members To Register:</td>
		<td align='left'>
		<b>YES</b>
		<input type='radio' name='MEMBERSONLY_new' value='1'<?= $MEMBERSONLY ? " checked" : '' ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				   <b>NO</b>
		<input type='radio' name='MEMBERSONLY_new' value='0'<?= !$MEMBERSONLY ? " checked" : '' ?>>
		</td>
		</tr>
		<tr>
		<td>Invite ONLY:<br><i> (Make it only possible to register via a invite, members only also needs to be ON)</i></td>
		<td align='left'>
		<b>YES</b>
		<input type='radio' name='INVITEONLY_new' value='1'<?= $INVITEONLY ? " checked" : '' ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <b>NO</b>
		<input type='radio' name='INVITEONLY_new' value='0'<?= !$INVITEONLY ? " checked" : '' ?>>
		</td>
		</tr>
		 <tr>
 <td>Admin ONLY Confirm Registration:<br><i> (Make it only possible for an admin to confirm each new account)</i></td>
 <td align='left'>
 <b>YES</b>
 <input type='radio' name='ACONFIRM_new' value='1'<?= $ACONFIRM ? " checked" : '' ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <b>NO</b>
 <input type='radio' name='ACONFIRM_new' value='0'<?= !$ACONFIRM ? " checked" : '' ?>>
</td>
 </tr>
<tr>
<td>Send Welcome PM to New Users?</td>
<td align='left'>
<b>YES</b>
<input type='radio' name='WELCOMEPMON_new' value='1'<?= $WELCOMEPMON ? " checked" : '' ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
           <b>NO</b>
<input type='radio' name='WELCOMEPMON_new' value='0'<?= !$WELCOMEPMON ? " checked" : '' ?>>
</td>
</tr>
<tr>
<td valign="top">Welcome PM to New Users:</td>
<td align='left'>
<textarea name='WELCOMEPMMSG_new' cols="38" rows="8"><?= $WELCOMEPMMSG ?></textarea></td>
</tr>
<tr>
<td>Only Logged In Memebers Can View/Download Torrents:<br></td>
<td align='left'>
<b>YES
<input type='radio' name='LOGGEDINONLY_new' value='1'<?= $LOGGEDINONLY ? " checked" : '' ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
           <b>NO</b>
<input type='radio' name='LOGGEDINONLY_new' value='0'<?= !$LOGGEDINONLY ? " checked" : '' ?>>
</td>
</tr>
<tr>
 <td>Word Censor Enabled?<br></td>
 <td align='left'>
 <b>YES</b>
 <input type='radio' name='CENSORWORDS_new' value='1'<?= $CENSORWORDS ? " checked" : '' ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <b>NO</b>
 <input type='radio' name='CENSORWORDS_new' value='0'<?= !$CENSORWORDS ? " checked" : '' ?>>
</td>
 </tr>
<tr>
<td>Wait Times Enabled?<br><i>(See Below For Full Details)</i></td>
<td align='left'>
<b>YES</b>
<input type='radio' name='MEMBERSONLY_WAIT_new' value='1'<?= $MEMBERSONLY_WAIT ? " checked" : '' ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
           <b>NO</b>
<input type='radio' name='MEMBERSONLY_WAIT_new' value='0'<?= !$MEMBERSONLY_WAIT ? " checked" : '' ?>>
</td>
</tr>

<tr>
<td>Wait Times<br></td>
<td align='left'>
<b>A) </b>RATIO: <<input type='text' name='RATIOA_new' value='<?= $RATIOA ?>' maxlength='4' size='4'>GIGS: <<input type='text' name='GIGSA_new' value='<?= $GIGSA ?>' maxlength='4' size='4'> WAIT: <input type='text' name='WAITA_new' value='<?=$WAITA?>' maxlength='2' size='3'>Hrs
<BR><BR>
<b>B) </b>RATIO: <<input type='text' name='RATIOB_new' value='<?= $RATIOB ?>' maxlength='4' size='4'>GIGS: <<input type='text' name='GIGSB_new' value='<?= $GIGSB ?>' maxlength='4' size='4'> WAIT: <input type='text' name='WAITB_new' value='<?=$WAITB?>' maxlength='2' size='3'>Hrs
<BR><BR>
<b>C) </b>RATIO: <<input type='text' name='RATIOC_new' value='<?= $RATIOC ?>' maxlength='4' size='4'>GIGS: <<input type='text' name='GIGSC_new' value='<?= $GIGSC ?>' maxlength='4' size='4'> WAIT: <input type='text' name='WAITC_new' value='<?=$WAITC?>' maxlength='2' size='3'>Hrs
<BR><BR>
<b>D) </b>RATIO: <<input type='text' name='RATIOD_new' value='<?= $RATIOD ?>' maxlength='4' size='4'>GIGS: <<input type='text' name='GIGSD_new' value='<?= $GIGSD ?>' maxlength='4' size='4'> WAIT: <input type='text' name='WAITD_new' value='<?=$WAITD?>' maxlength='2' size='3'>Hrs
<BR><BR>
</td>
</tr>

<tr>
<td>Ratio Warning Enabled?<br><i>(See Below For Full Details)</i></td>
<td align='left'>
<b>YES</b>
<input type='radio' name='RATIO_WARNINGON_new' value='1'<?= $RATIO_WARNINGON ? " checked" : '' ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
           <b>NO</b>
<input type='radio' name='RATIO_WARNINGON_new' value='0'<?= !$RATIO_WARNINGON ? " checked" : '' ?>>
</td>
</tr>

<tr>
<td>Warning Schedule<br></td>
<td align='left'>
Warn after RATIO is less than <input type='text' name='RATIOWARN_AMMOUNT_new' value='<?=$RATIOWARN_AMMOUNT?>' maxlength='4' size='4'> for <input type='text' name='RATIOWARN_TIME_new' value='<?=$RATIOWARN_TIME?>' maxlength='4' size='4'> days.
<BR><BR>
Ban after <input type='text' name='RATIOWARN_BAN_new' value='<?=$RATIOWARN_BAN?>' maxlength='4' size='4'> day(s) after warning ignored by user.
<BR><BR>
</td>
</tr>

<tr>
<td>Auto Add DHT Flag?<br><i>(Automatic addition of the Private flag to all uploaded torrents, please not the seeder will need to re-download from the site.)</i></td>
<td align='left'>
<b>YES</b>
<input type='radio' name='DHT_new' value='1'<?= $DHT ? " checked" : '' ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
           <b>NO</b>
<input type='radio' name='DHT_new' value='0'<?= !$DHT ? " checked" : '' ?>>
</td>
</tr>

<tr>
<td>Turn Site Poll On?<br><i>Create a poll at <a href=makepoll.php>makepoll.php</a></i></td>
<td align='left'>
<b>YES</b>
<input type='radio' name='POLLON_new' value='1'<?= $POLLON ? " checked" : '' ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
           <b>NO</b>
<input type='radio' name='POLLON_new' value='0'<?= !$POLLON ? " checked" : '' ?>>
</td>
</tr>

<tr>
<td>Forums Enabled?</td>
<td align='left'>
<b>YES</b>
<input type='radio' name='FORUMS_new' value='1'<?= $FORUMS ? " checked" : '' ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
           <b>NO</b>
<input type='radio' name='FORUMS_new' value='0'<?= !$FORUMS ? " checked" : '' ?>>
</td>
</tr>
<tr>
<td>Restrict Uploads To Uploader Class?</td>
<td align='left'>
<b>YES</b>
<input type='radio' name='UPLOADERSONLY_new' value='1'<?= $UPLOADERSONLY ? " checked" : '' ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
           <b>NO</b>
<input type='radio' name='UPLOADERSONLY_new' value='0'<?= !$UPLOADERSONLY ? " checked" : '' ?>>
</td>
</tr>


<tr>
<td colspan="2"><b>Blocks Management<br>&#9492;</b> Here you can configure the blocks on the site</td>
</tr>

<tr>
<td>Torrent Name Max Length Before Cut-Off:<br><i>(if name is higher it will be shortend with ... added)</i><font color=red>REQUIRED</font></td>
<td align='left'>
<input type='text' name='MAXDISPLAYLENGTH_new' value='<?=$MAXDISPLAYLENGTH?>' maxlength='3' size='5'></td>
</tr>

<tr>
<td>Welcome / Notice Block Enabled?</td>
<td align='left'>
<b>YES</b>
<input type='radio' name='SITENOTICEON_new' value='1'<?= $SITENOTICEON ? " checked" : '' ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
           <b>NO</b>
<input type='radio' name='SITENOTICEON_new' value='0'<?= !$SITENOTICEON ? " checked" : ''; ?>>
</td>
</tr>
<tr>
<td valign="top">Welcome / Notic Text:<br><i>(html allowed)</i></td>
<td align='left'>
<textarea name='SITENOTICE_new' cols="38" rows="8"><?= $SITENOTICE ?></textarea></td>
</tr>
<tr>
<td>Shoutbox Enabled?</td>
<td align='left'>
<b>YES</b>
<input type='radio' name='SHOUTBOX_new' value='1'<?= $SHOUTBOX ? " checked" : '' ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
           <b>NO</b>
<input type='radio' name='SHOUTBOX_new' value='0'<?= !$SHOUTBOX ? " checked" : '' ?>>
</td>
</tr>

<tr>
<td>Removals / Copyrights Block Enabled?</td>
<td align='left'>
<b>YES</b>
<input type='radio' name='REMOVALSON_new' value='1'<?= $REMOVALSON ? " checked" : '' ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
           <b>NO</b>
<input type='radio' name='REMOVALSON_new' value='0'<?= !$REMOVALSON ? " checked" : '' ?>>
</td>
</tr>

<tr>
<td>Site News Block Enabled?</td>
<td align='left'>
<b>YES</b><input type='radio' name='NEWSON_new' value='1'<?= $NEWSON ? " checked" : '' ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<b>NO</b><input type='radio' name='NEWSON_new' value='0'<?= !$NEWSON ? " checked" : '' ?>>
</td>
</tr>

<tr>
<td>Donate Block Enabled?</td>
<td align='left'>
<b>YES</b>
<input type='radio' name='DONATEON_new' value='1'<?= $DONATEON ? " checked" : '' ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
           <b>NO</b>
<input type='radio' name='DONATEON_new' value='0'<?= !$DONATEON ? " checked" : '' ?>>
</td>
</tr>
<tr>
<td>Disclaimer Block Enabled?</td>
<td align='left'>
<b>YES</b>
<input type='radio' name='DISCLAIMERON_new' value='1'<?= $DISCLAIMERON ? " checked" : '' ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
           <b>NO</b>
<input type='radio' name='DISCLAIMERON_new' value='0'<?= !$DISCLAIMERON ? " checked" : '' ?>>
</td>
</tr>
<tr>
<td>Request Area On?</td>
<td align='left'>
<b>YES</b>
<input type='radio' name='REQUESTSON_new' value='1'<?= $REQUESTSON ? " checked" : '' ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
           <b>NO</b>
<input type='radio' name='REQUESTSON_new' value='0'<?= !$REQUESTSON ? " checked" : '' ?>>
</td>
</tr>

<tr>
<td colspan="2">&nbsp;</td>
</tr>


<tr>
<td colspan="2"><b>Advanced Settings
<br>&#9492;</b>These settings are for advanced users only. If you do not understand what they do, do
<font color="#CC0000">NOTmodify them.</font></td>
</tr>
<tr>
<td>Maximum Torrent Size (in bytes)</td>
<td align='left'>
<input type='text' name='max_torrent_size_new' value='<?= $max_torrent_size ?>' maxlength='50' size='50'></td>
</tr>
<tr>
<td>Maximum NFO Size (in bytes)</td>
<td align='left'>
<input type='text' name='max_nfo_size_new' value='<?= $max_nfo_size ?>' maxlength='50' size='50'></td>
</tr>
<tr>
<td>Maximum Image (in bytes)</td>
<td align='left'>
<input type='text' name='max_image_size_new' value='<?= $max_image_size ?>' maxlength='50' size='50'></td>
</tr>
<tr>
<td>Torrent Auto-Clean Interval (in seconds)</td>
<td align='left'>
<input type='text' name='autoclean_interval_new' value='<?= $autoclean_interval ?>' maxlength='50' size='50'></td>
</tr>
<tr>
<td>Torrent Announce Interval (in seconds)</td>
<td align='left'>
<input type='text' name='announce_interval_new' value='<?= $announce_interval ?>' maxlength='50' size='50'></td>
</tr>
<tr>
<td>Torrent Minimum
Votes</td>
<td align='left'>
<input type='text' name='minvotes_new' value='<?= $minvotes ?>' maxlength='50' size='50'></td>
</tr>
<tr>
<td>Inactivity Timeout (in seconds)</td>
<td align='left'>
<input type='text' name='signup_timeout_new' value='<?= $signup_timeout ?>' maxlength='50' size='50'></td>
</tr>
<tr>
<td>Maximum Torrent Dead Time (in seconds)</td>
<td align='left'>
<input type='text' name='max_dead_torrent_time_new' value='<?= $max_dead_torrent_time ?>' maxlength='50' size='50'></td>
</tr>
<tr>
<td colspan="2">&nbsp;</td>
</tr>
<tr>
<td align='left'></td><td align='left'>
    <input type='submit' value='Update Settings'>&nbsp;
    <input type='reset' value='Reset'></td>
</tr>

</table></div>

<?php 
end_frame();
print("</form>");
