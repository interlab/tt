
</TD>

<TD vAlign=top width=180>

<?php require_once TT_DIR . '/columns/right-column.php'; ?>

</TD>
</TR>
</TABLE>


<footer class="tt-page-footer">
<?php

global $CURUSER;

$tend = microtime(true);
$totaltime = ($tend - TT_START_TIME);

if ($CURUSER['is_admin']) {
    echo ' PHP version: ', PHP_VERSION, '.',
    // ' MySQL ver.: '. (
    //	function_exists('mysqli_connect') ? mysqli_get_server_info($db_connection) : mysql_get_server_info()
    //  // function_exists('mysql_get_server_info') ? mysql_get_server_info() : mysqli_get_server_info($db_connection)
    //	), '. ',
    ' Memory usage: ', (memory_get_usage(true) / 1024 / 1024), ' MB';
}

echo '
<br>Page Generated in ', $totaltime, ' seconds!
<br>Powered by TorrentTrader Classic v', $GLOBALS['ttversion'], '
<br><a href="http://www.torrenttrader.org" target="_blank">www.torrenttrader.org</a>';
//
// *******************************************************************************
//			PLEASE DO NOT REMOVE THE POWERED BY LINE, SHOW SOME SUPPORT!
// *******************************************************************************
?>
</footer>

</body>
</html>