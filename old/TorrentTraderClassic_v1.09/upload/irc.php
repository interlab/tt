<?php 

require "backend/functions.php";
dbconn(false);
loggedinorreturn();
stdhead();
include("pjirc/config.php");
$self = $_SERVER['PHP_SELF'];

begin_frame(" Chat ", 'center');
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
<title><?php print $title; ?></title>
<link rel="stylesheet" type="text/css" href="pjirc/css.php<?php if ($page == "chat") print "?color=".$color; ?>">
<script language="JavaScript" type="text/javascript" src="pjirc/chat.js"></script>
</head>
<?php

// ===============================================================================
// == Index Normal ==========================================================
// ===============================================================================

if($page == ""): ?>
<body onLoad="document.login.nick.focus();">

<div align=center>
<form name="login" action="<?php print $self; ?>" method="<?php print $method; ?>" onSubmit="return RandomNick();">
	<input type="hidden" name="page" value="chat">
	<table width="45%" class="border"><tr><td align=center>
		<table width="100%" cellpadding=18>
			<tr>
				<td align=center valign=top class="tall">
					<hr><?php print $headline; ?><hr><br>
					<?php print logo(); ?>
				</td>
			</tr>
		</table>
		<table width="80%">
			<tr>
				<td align=right class="medium">Nickname:&nbsp;</td>
				<td><?php echo $CURUSER["username"]?>-WEB</td>
			</tr><tr>
				<td align=right class="medium">Smileys:&nbsp;</td>
				<td>
					<input name="smileys" type="radio" value="1">Yes&nbsp;
					<input name="smileys" type="radio" checked="checked" value="0">No&nbsp;
				</td>
			</tr>
		</table>
		<table>
			<tr>
				<td align=center height=20><input type="submit" value="Login"></td>
			</tr><tr>
				<td align=center height=20 class="small">
				
				</td>
			</tr>
		</table>
	</td></tr></table>
</form>
<?php endif;

// ===============================================================================
// == mainpage ==============================================================
// ===============================================================================

if($page == "chat"): ?>
<body class="back">

<div align=center>
	<table width="70%">
		<tr>
			<td class="small">Back to <a href="<?php print $self; ?>">Login</a></td>
		</tr><tr>
			<td align="center" valign="top" class="color">
				<applet name="pjirc" codebase=pjirc code=IRCApplet.class archive="irc.jar, pixx.jar"  width=600 height=400>
					<param name="CABINETS" value="irc.cab, securedirc.cab, pixx.cab">
					<param name="nick" value="<?php print $nick; ?>">
					<param name="alternatenick" value="<?php print $nick; ?>??">
					<param name="host" value="<?php print $host; ?>">
					<param name="name" value="<?php print $name; ?>">
					<param name="userid" value="<?php print $nick; ?>">
					<param name="style:backgroundimage" value="true">
                  <param name="style:backgroundimage1" value="all all 0">
				   <param name="style:bitmapsmileys" value="true">
					<param name="command1" value="<?php print $pass; ?>">
					<param name="command2" value="<?php print $channel; ?>">
					<param name="quitmessage" value="<?php print $quit_message; ?>">
					<param name="soundword1" value="<?php print strtolower($nick); ?> snd/ding.au">
					<param name="language" value="<?php print $language; ?>">
					<param name="lngextension" value="<?php print $langEx; ?>">
					<param name="soundbeep" value="snd/bell2.au">
					<param name="soundquery" value="snd/ding.au">
					<param name="highlight" value="true">
					<param name="gui" value="pixx">
					

					<param name="style:sourcefontrule1" value="Status+Channel+Query all <?php print $font." ".$font2; ?>">
<?php
	if($smileys) {
		print "\t\t\t\t\t<param name=\"style:bitmapsmileys\" value=\"true\">\n";
		$i=0; $x=1;
		while(isset($smiley[$i][0])) {
			$k=1;
			while(isset($smiley[$i][$k])) {
				$img = $imgpath."/".$smiley[$i][0].$imgEx;
				$smiley[$i][$k] = str_replace("\\", "", $smiley[$i][$k]);
				print "\t\t\t\t\t<param name=\"style:smiley".$x."\" value=\"".$smiley[$i][$k]." ".$img."\">\n";
				$x++; $k++;
			}
			$i++;
		}
	}

	print "\n";
	if($style == "Default") print "\t\t\t\t\t<param name=\"pixx:nickfield\" value=\"true\">\n";
?>
					<param name="pixx:language" value="pixx-<?php print $language; ?>">
					<param name="pixx:highlightnick" value="true">
					<param name="pixx:styleselector" value="true">
					<param name="pixx:setfontonstyle" value="true">
					<param name="pixx:timestamp" value="true">
					<param name="pixx:mouseurlopen" value="1 2">
					<param name="pixx:mousechanneljoin" value="1 2">
					<param name="pixx:nickfield" value="false">
<?php
	print "\t\t\t\t\t<param name=\"pixx:configurepopup\" value=\"true\">\n";

	$i = 0;
	while(isset($popupmenu[$i][0])) {
		print "\t\t\t\t\t<param name=\"pixx:popupmenustring".($i+1)."\" value=\"".$popupmenu[$i][0]."\">\n";
		$i++;
	}

	$i = 0;
	while(isset($popupmenu[$i][0])) {
		$k = 1;
		while(isset($popupmenu[$i][$k])) {
			print "\t\t\t\t\t<param name=\"pixx:popupmenucommand".($i+1)."_".$k."\" value=\"".$popupmenu[$i][$k]."\">\n";
			$k++;
		}
		$i++;
	}

	if(isset($which)) {
		$i=0;
		while(isset($colorset[$i][$which])) {
			print "\t\t\t\t\t<param name=\"pixx:color".$i."\" value=\"".$colorset[$i][$which]."\"> ";
			print $comment[$i][0]."\n";
			$i++;
		}
	}

	print "\t\t\t\t</applet>\n";

// ===============================================================================
// == Smileys ===============================================================
// ===============================================================================

if($smileys): ?>
				<table width="100%" cellspacing=0 cellpadding=0><tr><td>
					<table cellpadding=3><tr>
						<td valign=top><img src="img/blank.gif" width=50 height=1 alt="">
<?php
	$i = 0;
	while(isset($smiley[$i][0])) {
		$img = $imgpath."/".$smiley[$i][0].$imgEx;
		print "\t\t\t\t\t\t</td><td valign=top>\n";
		if (is_readable($img)) {
			$size  = GetImageSize($img);
			$smiley[$i][1] = str_replace("'", "\'", $smiley[$i][1]);
			print "\t\t\t\t\t\t\t<a href=\"javascript:smiley('".$smiley[$i][1]."')\">\n";
			print "\t\t\t\t\t\t\t<img src=\"".$img."\" ".$size[3]." border=0 alt=\"".$smiley[$i][1]."\"></a>&nbsp;\n";
		}
		else {
			$smiley[$i][1] = str_replace("'", "\'", $smiley[$i][1]);
			print "\t\t\t\t\t\t\t<a href=\"javascript:smiley('".$smiley[$i][1]."')\" title=\"/".$img." not found\">";
			$smiley[$i][1] = str_replace("\\", "", $smiley[$i][1]);
			print $smiley[$i][1]."</a>&nbsp;\n";
			$warning_smiley = true;
		}
		$i++;
	}
?>
						</td>
					</tr></table>
				</td></tr></table>
<?php endif; ?>
			</td>
		</tr>
	</table><br>
<?php

// ===============================================================================
// == Away/Back Buttons/Input field =========================================
// ===============================================================================

if($buttons): ?>
	<form name="form" action="<?php print $self; ?>">
	<table width="70%"><tr><td>
		<span class="medium">Away&nbsp;message:&nbsp;</span>
		<input name="away_reason" type="text" size=30>
		<input type="button" value="Set Away" onClick="maway(document.form.away_reason.value,'<?php print $nick; ?>')">
		<input type="button" value="Set Back" onClick="mback(document.form.away_reason.value,'<?php print $nick; ?>')">
	</td></tr></table>
	</form>
<?php endif;

// ===============================================================================
// == Warnings ==============================================================
// ===============================================================================

if(isset($warning_smiley) && $warning_smiley): ?>
	<table width="70%" cellpadding=0 cellspacing=0 class="border"><tr><td>
		<table style="background-color: white;"><tr>
			<td valign=top class="small"><b>Warning:&nbsp;</b></td>
			<td valign=top class="small">
				One ore more Smileys were not found. You should put them below the
				<b><?php print $imgpath; ?></b> path, or change the settings in the config.php!<br>
				Default settings are the standard smileys, which are included in the PJIRC Zip Archive.
			</td>
		</tr></table>
	</td></tr></table>
<?php endif; endif; ?>
</div>

</body></html>

<?php 
end_frame();

stdfoot();

