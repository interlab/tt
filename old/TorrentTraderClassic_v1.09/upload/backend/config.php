<?php

// MySQL Settings (please change these to reflect your MYSQL settings, all other settings can be changed via adminCP)
$mysql_host = "localhost";
$mysql_user = 'smf';
$mysql_pass = 'smf2';
$mysql_db = 'ttpmr';

// Default Language / Theme Settings (These are currently set via the databases DEFAULT values, NOT THE ADMIN CP)
$language = "1";
$theme = "1";

// Site Settings
$SITENAME = "TorrentTrader Classic";
$SITEEMAIL = "email@address.com";
$SITEURL = "http://yoursite.com";
$SITE_ONLINE = true;
$OFFLINEMSG = "Site is down for a little while";
$UPLOADERSONLY = false;
$LOGGEDINONLY = false;
$INVITEONLY = false;
$ACONFIRM = false;
$WELCOMEPMON = true;
$CENSORWORDS = true;
$MAXDISPLAYLENGTH = "45";
$WELCOMEPMMSG = "Thank you for registering at our tracker!

Please remember to keep your ratio at 1.00 or greater :)";
$DHT = false;
$POLLON = false;

//Setup Site Blocks
$SITENOTICEON = true;
$REMOVALSON = true;
$NEWSON = true;
$DONATEON = true;
$DISCLAIMERON = true;
$SITENOTICE = <<<EOD
Welcome To TorrentTrader
EOD;
$SHOUTBOX = true;
$FORUMS = true;
$REQUESTSON = true;

//setup IRC Chat
$IRCCHAT = false;
$IRCCHANNEL = "#torrenttrader";
$IRCSERVER1 = "irc.p2p-irc.net";
$IRCSERVER2 = "";
$IRCSERVER3 = "";

//Setup IRC Announcer
$IRCANNOUNCE = false;
$ANNOUNCEIP = "x.x.x.x";
$ANNOUNCEPORT= "2500";

//WAIT TIME VARS
$GIGSA= "1";
$RATIOA= "0.50";
$WAITA= "24";
$GIGSB= "3";
$RATIOB= "0.65";
$WAITB= "12";
$GIGSC= "5";
$RATIOC= "0.80";
$WAITC= "6";
$GIGSD= "7";
$RATIOD= "0.95";
$WAITD= "2";

//RATIO WARNING VARS
$RATIO_WARNINGON = "true";    //ratiowarn on/off
$RATIOWARN_AMMOUNT = "0.50"; //user warned if this ratio is held
$RATIOWARN_TIME = "10";   //ammount of time for user have have poor ratio before warning
$RATIOWARN_BAN = "5";     //ammount of time after warning to auto-ban user.

// Tracker Settings
$torrent_dir = str_replace("\\","/",getcwd()."/uploads/");
$nfo_dir = str_replace("\\","/",getcwd()."/uploads/");
$image_dir = str_replace("\\","/",getcwd()."/uploads/");
$announce_urls = array();
$announce_urls[] = "http://yoursite.com/announce.php";
$GLOBALBAN = false;
$MEMBERSONLY = true;
$MEMBERSONLY_WAIT = true;
$RATIO_WARNINGON = true;
$PEERLIMIT = "10000";

// Advanced Settings for announce and cleanup
$autoclean_interval = "600";
$max_torrent_size = "1000000";
$max_nfo_size = "1000000";
$max_image_size = "1000000000000";
$announce_interval = "1800";
$signup_timeout = "259200";
$minvotes = "1";
$maxsiteusers = "10000";
$max_dead_torrent_time = "21600";
