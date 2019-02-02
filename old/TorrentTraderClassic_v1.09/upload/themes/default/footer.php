	<BR><BR><BR>
	<CENTER>
<?php

$tend = microtime(true);
$totaltime = ($tend - TT_START_TIME); 
printf ("Page Generated in %f seconds!", $totaltime); 
echo "<BR>Powered by TorrentTrader Classic v{$GLOBALS['ttversion']}<br><a href=http://www.torrenttrader.org target=_blank>www.torrenttrader.org</a>";
//
// *******************************************************************************
//			PLEASE DO NOT REMOVE THE POWERED BY LINE, SHOW SOME SUPPORT!
// *******************************************************************************
?>
</CENTER>

</TD>
	</TR>
	</TABLE><br><br>
	<br><br>

</body>
</html>