
</TD>

<TD vAlign=top width=180>

<?php require_once TT_DIR . '/columns/right-column.php'; ?>

</TD>
</TR>
</TABLE>


<footer class="tt-page-footer">
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
</footer>


</body>
</html>