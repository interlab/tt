<?php

if ($_GET["changechmod"] == 1){
	dochmod();
}
	
	
function dochmod () {
error_reporting(0);
echo "<font face=arial>";
$conf = chmod($_SERVER['DOCUMENT_ROOT'] . "/backend/config.php", 0666);
	if(!$conf){ echo "/backend/config.php - Error setting permissions<br>"; } else { echo "/backend/config.php - Success! CHMOD CHANGED<br>"; } 

$oconf = chmod($_SERVER['DOCUMENT_ROOT'] . "/backend/oldconfig.php", 0666);
	if(!$oconf){ echo "/backend/oldconfig.php - Error setting permissions<br>"; } else { echo "/backend/oldconfig.php - Success! CHMOD CHANGED<br>"; } 

$banners = chmod($_SERVER['DOCUMENT_ROOT'] . "/banners.txt", 0666);
	if(!$banners){ echo "banners.txt - Error setting permissions<br>"; } else { echo "banners.txt - Success! CHMOD CHANGED<br>"; } 

$sponsors = chmod($_SERVER['DOCUMENT_ROOT'] . "/sponsors.txt", 0666);
	if(!$banners){ echo "sponsors.txt - Error setting permissions<br>"; } else { echo "sponsors.txt - Success! CHMOD CHANGED<br>"; } 

$disclaimer = chmod($_SERVER['DOCUMENT_ROOT'] . "/disclaimer.txt", 0666);
	if(!$disclaimer){ echo "disclaimer.txt - Error setting permissions<br>"; } else { echo "disclaimer.txt - Success! CHMOD CHANGED<br>"; } 

$backups = chmod($_SERVER['DOCUMENT_ROOT'] . "/backups/", 0666);
	if(!$backups){ echo "backups/ - Error setting permissions<br>"; } else { echo "backups/ - Success! CHMOD CHANGED<br>"; } 

$uploads = chmod($_SERVER['DOCUMENT_ROOT'] . "/uploads/", 0666);
	if(!$backups){ echo "uploads/ - Error setting permissions<br>"; } else { echo "uploads/ - Success! CHMOD CHANGED<br>"; } 

echo "<CENTER><br>If you receive 'Error setting permissions' then please use your FTP client or SSH to change CHMODS<br><br><a href=check.php>Back To Check Page</a></CENTER></font>";
die;
}


function get_php_setting($val) {
	$r =  (ini_get($val) == '1' ? 1 : 0);
	return $r ? 'ON' : 'OFF';
}

function writableCell( $folder, $relative=1, $text='' ) {
	$writeable 		= '<b><font color="green">Writeable</font></b>';
	$unwriteable 	= '<b><font color="red">Unwriteable</font></b>';
	
	echo '<tr>';
	echo '<td>' . $folder . '/</td>';
	echo '<td align="right">';
	if ( $relative ) {
		echo is_writable( "./$folder" ) 	? $writeable : $unwriteable;
	} else {
		echo is_writable( "$folder" ) 		? $writeable : $unwriteable;
	}
	echo '</tr>';
}


view();


function view() {	
?>
<html><head><title>TorrentTrader Check</title></head>
<body>
<font face="arial">
<CENTER><BR><font face=arial size=2><b>TorrentTrader Classic Config Check<br>v1.3 - FLASH<br><br></b></CENTER>

<CENTER><input type="button" class="button" value="Check Again" onclick="window.location=window.location" />

</CENTER><BR>

<B>Required Settings Check:</B><BR>
If any of these items are highlighted in red then please take actions to correct them. <BR>
Failure to do so could lead to your TorrentTrader! installation not functioning correctly.<BR>
<BR>
This system check is designed for unix based servers, windows based servers may not give desired results<BR>
<BR>
<BR>	
<table cellpadding=3 cellspacing=1 style='border-collapse: collapse' border=1>
<tr>
<td>PHP version >= 4.3.0</td>
<td align="left">
								<?php
				  define(_PHP_VERSION, phpversion());
				if (phpversion() < '5'){
					echo phpversion() < '4.3' ? '<b><font color="red">No</font> 4.3 or above required</b>' : '<b><font color="green">Yes</font></b>';
					echo " - Your PHP version is " . _PHP_VERSION ."";
				}
				if (phpversion() > '5'){
					echo "<font color=red>PHP 5 not officially supported</font>";
					echo " - Your PHP version is " . _PHP_VERSION ."";
				}
					
					?>
</td>
</tr><tr>
	<td>&nbsp; - zlib compression support</td>
	<td align="left"><?php echo extension_loaded('zlib') ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>';?></td>
</tr><tr>
	<td>&nbsp; - XML support</td>
	<td align="left"><?php echo extension_loaded('xml') ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>';?></td>
</tr><tr>
<td>&nbsp; - MySQL support</td>
	<td align="left"><?php echo function_exists( 'mysql_connect' ) ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>';?></td>
</tr><tr>
<td valign="top">backend/config.php</td>
	<td align="left">
									<?php
									if (@file_exists('backend/config.php') &&  @is_writable( 'backend/config.php' )){
										echo '<b><font color="green">Writeable</font></b>';
									} else {
										echo '<b><font color="red">Unwriteable</font></b><br><B>Please ensure you CHMOD backend/config.php to 777</B>';
									} 
									?>
</td>
</tr><tr>
<td valign="top">backend/oldconfig.php</td>
	<td align="left">
									<?php
									if (@file_exists('backend/oldconfig.php') &&  @is_writable( 'backend/oldconfig.php' )){
										echo '<b><font color="green">Writeable</font></b>';
									} else {
										echo '<b><font color="red">Unwriteable</font></b><br><B>Please ensure you CHMOD backend/oldconfig.php to 777</B>';
									} 
									?>
</td>
</tr><tr>
<td>Document Root<br><I><font size=1>(Use this for your PATHS in config.php)</font></I></td>
	<td align="left" valign="top"><?echo $_SERVER['DOCUMENT_ROOT'];?></td>
</tr>

</table>

			
<p>These settings are recommended for PHP in order to ensure full compatibility with TorrentTrader!.</p>					
<p>However, TorrentTrader! will still operate if your settings do not quite match the recommended</p>

<table cellpadding=3 cellspacing=1 style='border-collapse: collapse' border=1 >
<tr><td width="500px">Directive</td><td>Recommended</td><td>Actual</td></tr>

<?php
$php_recommended_settings = array(array ('Safe Mode','safe_mode','OFF'),
							array ('Display Errors','display_errors','ON'),
							array ('File Uploads','file_uploads','ON'),
							array ('Magic Quotes GPC','magic_quotes_gpc','ON'),
							array ('Magic Quotes Runtime','magic_quotes_runtime','OFF'),
							array ('Register Globals - If OFF, TorrentTrader will emulate them as ON','register_globals','ON'),
							array ('Output Buffering','output_buffering','OFF'),
							array ('Session auto start','session.auto_start','OFF'),
);
						
foreach ($php_recommended_settings as $phprec) {
?>
<tr>
<td><?php echo $phprec[0]; ?>:</td>
<td><?php echo $phprec[2]; ?>:</td>
<td><b>
		<?php
			if ( get_php_setting($phprec[1]) == $phprec[2] ) {
			?>
				<font color="green">
			<?php
				} else {
			?>
				<font color="red">
			<?php
				}
				echo get_php_setting($phprec[1]);
			?>
			</font></b>
</td></tr>
	<?php
	}
	?>
</table>
				
<BR><b>Directory and File Permissions Check:</b><BR>
<BR>			
In order for TorrentTrader! to function correctly it needs to be able to access or write to certain files or directories.<BR> 
<BR>
If you see "Unwriteable" you need to change the permissions on the file or directory to 777 or 666 so that  TorrentTrader to write to it.<BR> <B>SECURITY INFO: We advise that banners.txt and sponsors.txt are left "Unwriteable" and you only edit these via FTP</B><BR>
<BR>

<a href=check.php?changechmod=1>Click here to attempt to change CHMODS auto</a><BR><BR>

<table cellpadding=3 cellspacing=1 style='border-collapse: collapse' border=1 >
<?php
	writableCell( 'backend/config.php' );
	writableCell( 'backend/oldconfig.php' );
	writableCell( 'banners.txt' );
	writableCell( 'sponsors.txt' );
	writableCell( 'disclaimer.txt' );
	writableCell( 'backups' );
	writableCell( 'uploads' );
?>
</table>
<BR><BR><BR>

</body>
</html>
	<?php
}//end func

?>