</td>
	</tr>
	<tr>
		<td colspan="2">
		<TABLE cellSpacing=0 cellPadding=4 width=770 bgColor=#d2d2d2 border=0>
<TBODY>
<TR>
<TD align=middle width="100%">
<a class=navi0 href=extras-stats.php>Statistics</a> | 
<a class=navi0 href=formats.php>File Formats</a> | 
<a class=navi0 href=videoformats.php>Movie Format</a> | 
<a class=navi0 href=staff.php>Staff</a> | 
<a class=navi0 href=rules.php>Site Rules</a> | 
<a class=navi0 href=extras-users.php>Member List</a> | 
<a class=navi0 href=visitorsnow.php>Online Users</a> | 
<a class=navi0 href=visitorstoday.php>Visitors Today</a>
</TD>
</TR>
</TBODY>
</TABLE>
		</td>
	</tr>
	<tr>
		<td colspan="2" align=center>Powered by <a href=http://www.torrenttrader.org target=_blank>TorrentTrader Classic v<?=$GLOBALS['ttversion']?></a></td>
	</tr>
</table></div>
</td>
	</tr>
</table>
<!-- FIN DE LA PAGE -->
</CENTER>
<br>
<center>
<?
// Variables for Start Time 
$mtime = microtime(); // Get Current Time 
$mtime = explode (" ", $mtime); // Split Seconds and Microseconds   
$mtime = $mtime[1] + $mtime[0];  // Create a single value for start time 
$tstart = $mtime; // Start time 

// Variables for Start Time 
$mtime = microtime(); 
$mtime = explode (" ", $mtime); 
$mtime = $mtime[1] + $mtime[0]; 
$tend = $mtime; // End time 
$totaltime = ($tend - $tstart); 
printf ("Page Generated in %f seconds!", $totaltime);
?></center>
</BODY>
</HTML>