<?php
 //********************************************************************
 // == Author:			Afrob
 // == Contact:			afrob@spam-webdatabox.de
 //						please remove the "spam-"
 // == Description: 	Login Front End for the PJIRC Java Chat-Client
 // == Version:			4.2
 //********************************************************************
 // == Contain:			This file contains the most important settings
 // == Modifying:		Read the comments, and
 //						change the code in little pieces.
 //********************************************************************
 // 					If you want to change the layout:
 // 					edit the Index.php and/or css.php
 //********************************************************************
 //						Freeware
 //********************************************************************
 // == Date:			09/29/2004
 //********************************************************************
 //edit for Torrent trader by ahbeng - http://www.torrentmalaya.com
 //u can edit to your host and channel in here
require "./backend/config.php";

 	// input vars													: "default value";
	$page    = (isset($_REQUEST['page']))    ? $_REQUEST['page']    : "";
	$nick    = "".$CURUSER["username"]."-WEB";
	$pass    = (isset($_REQUEST['pass']))    ? $_REQUEST['pass']    : "";
	$host    = (isset($_REQUEST['host']))    ? $_REQUEST['host']    : "".$IRCSERVER1."";
	$chan    = (isset($_REQUEST['chan']))    ? $_REQUEST['chan']    : "".$IRCCHANNEL."";
	$style   = (isset($_REQUEST['style']))   ? $_REQUEST['style']   : "Silver";	// available: Silver, Brown and Default
	$font    = (isset($_REQUEST['font']))    ? $_REQUEST['font']    : "SansSerif";
	$font2   = (isset($_REQUEST['font2']))   ? $_REQUEST['font2']   : "12";
	$smileys = (isset($_REQUEST['smileys'])) ? $_REQUEST['smileys'] : "";

	// not existing page chosen -> set page one
	if ($page != "advanced" && $page != "chat") $page = "";

	$nick = nick($nick);
	// replace umlauts, ß and numbers on the beginning
	function nick($nick) {
		$nick = str_replace("ä", "ae", $nick);
		$nick = str_replace("ö", "oe", $nick);
		$nick = str_replace("ü", "ue", $nick);
		$nick = str_replace("Ä", "Ae", $nick);
		$nick = str_replace("Ö", "Oe", $nick);
		$nick = str_replace("Ü", "Ue", $nick);
		$nick = str_replace("ß", "ss", $nick);
		$nick = str_replace(" ", "_", $nick);
		$nick = ereg_replace("^[0-9]*[0-9]", "", $nick);	// delete numbers on the beginning of nicknames
		return $nick;
	}

	$pass         = "/msg nickserv identify ".$pass;	// nickserv identify syntax
	$channel      = "/join ".$chan;		// join chan syntax
	$name         = "$SITENAME user";	// full name
	$quit_message = "$SITENAME forever!";

	$title    = "IRC Chat $SITENAME";	// page title
	$headline = "Welcome to $SITENAME Chat-Applet";

	$language = "english";
	$langEx   = "lng";	// language files must be for example english.lng and pixx-english.lng

	$method = "get";	// get or post: method to transfer the input vars of the Login Front

	// true or false
	$buttons = true;	// turn on/off the away/back buttons

	$imgpath = "pjirc/img";	// image path
	$imgEx   = ".gif";	// image Extension

	$logo = "admin_forum.jpg";	// logo filename
	$link = $SITEURL;	// logo link
	$alt  = "IRC CHAT";

	function logo() {
		global $imgpath, $imgEx, $logo, $link, $alt;
		$logo = $imgpath."/".$logo;
		$size = GetImageSize($logo);
		print "<a href=\"".$link."\" target=\"_blank\">\n";
		print "\t\t\t\t\t<img src=\"".$logo."\" ".$size[3]." border=0 alt=\"".$alt."\"></a>\n";
	}

 // ================================================================================== \\
 // == Smileys																		== \\
 // == Syntax: array("filename", "1st Smiley Definition", "2nd Smiley Definition")	== \\
 // == etc...																		== \\
 // == Filename must be without the extension (.gif)								== \\
 // ================================================================================== \\
	$smiley = array(
		array("sourire", ":)", ":-)"),
		array("content", ":D", ":-D"),
		array("OH-2", ":-O"),
		array("OH-1", ":o"),
		array("langue", ":P", ":-P"),
		array("clin-oeuil", ";)", ";-)"),
		array("triste", ":("),
		array("OH-3", ":|", ":-|"),
		array("pleure", ":\'("),
		array("rouge", ":$", ":-$"),
		array("cool", "(H)", "(h)"),
		array("enerve1", ":-@"),
		array("enerve2", ":@"),
		array("roll-eyes", ":s", ":-S")
	);

 // ========================================================================== \\
 // == Popupmenu															== \\
 // == Syntax: array("Command Name", "1st IRC command", "2nd IRC command")	== \\
 // == etc...																== \\
 // ========================================================================== \\
	$popupmenu = array(
		array("Whois", "/Whois %1"),
		array("Query", "/Query %1"),
		array("Ban", "/mode %2 -o %1", "/mode %2 +b %1"),
		array("Kick + Ban", "/mode %2 -o %1", "/mode %2 +b %1", "/kick %2 %1"),
		array("--"),
		array("Op", "/mode %2 +o %1"),
		array("DeOp", "/mode %2 -o %1"),
		array("HalfOp", "/mode %2 +h %1"),
		array("DeHalfOp", "/mode %2 -h %1"),
		array("Voice", "/mode %2 +v %1"),
		array("DeVoice", "/mode %2 -v %1"),
		array("--"),
		array("Ping", "/CTCP PING %1"),
		array("Version", "/CTCP VERSION %1"),
		array("Time", "/CTCP TIME %1"),
		array("Finger", "/CTCP FINGER %1"),
		array("--"),
		array("DCC Send", "/DCC SEND %1"),
		array("DCC Chat", "/DCC CHAT %1")
	);

 // ================================================================================================== \\
 // == Colorset																						== \\
 // == Syntax: array("color code of 1st colorset (silver)", "color code of 2nd colorset (brown)")	== \\
 // == etc...																						== \\
 // ================================================================================================== \\
	$colorset = array(
		//	silver [0]	brown [1]
		array("DEE3E7", "887766"),	// color 0
		array("000000", "000000"),	// color 1
		array("DEE3E7", "221100"),	// color 2
		array("DEE3E7", "221100"),	// color 3
		array("D1D7DC", "FFEEDD"),	// color 4
		array("DEE3E7", "D3CBBA"),	// color 5
		array("E5E5E5", "E4DDCC"),	// color 6
		array("D1D7DC", "FFEEDD"),	// color 7
		array("FFA34F", "FFA34F"),	// color 8
		array("000000", "D3CBBA"),	// color 9
		array("EFEFEF", "BBAA99"),	// color 10
		array("FFA34F", "D3CBBA"),	// color 11
		array("599FCB", "DD5555"),	// color 12
		array("DEE3E7", "D3CBBA"),	// color 13
		array("DEE3E7", "D3CBBA"),	// color 14
		array("DEE3E7", "EEDDCC")	// color 15
	);

	switch($style) {
		case "Silver":
			$which = 0;
			$color = $colorset[6][$which];
		break;
		case "Brown":
			$which = 1;
			$color = $colorset[6][$which];
		break;
		case "Default":
			$color = "084079";
		break;
	}

 // ================================== \\
 // == Comments (color definitions)	== \\
 // ================================== \\
	$comment = array(
		array("<!-- Button Highlight / Popup & Close Button Text & Higlight / Scrollbar Highlight -->"),
		array("<!-- Button Border & Text : ScrollBar Border & arrow : Popup & Close button Border : User List border & Text & icons -->"),
		array("<!-- Popup & Close button shadow -->"),
		array("<!-- Scrollbar shadow -->"),
		array("<!-- Scrollbar de-light (3D Dim colour) -->"),
		array("<!-- foreground : Buttons Face : Scrollbar Face -->"),
		array("<!-- background : Header : Scrollbar Track : Footer background -->"),
		array("<!-- selection : Status & Window button active colour -->"),
		array("<!-- event Color  -->"),
		array("<!-- close button -->"),
		array("<!-- voice icon  -->"),
		array("<!-- operator icon  -->"),
		array("<!-- halfoperator icon -->"),
		array("<!-- male ASL -->"),
		array("<!-- female ASL -->"),
		array("<!-- unknown ASL -->")
	);
?>