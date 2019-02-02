</TD>
	<TD vAlign="top" width="180">

    <?php require_once TT_COLUMNS_DIR . '/right-column.php'; ?>

    </td>
  </tr>
</table>

<div class="leo-footer">
    <div>
<?php
// Variables for Start Time 
$tend = microtime(true);
$totaltime = ($tend - TT_START_TIME);
printf ("Page Generated in %f seconds!", $totaltime);
echo "&nbsp;&nbsp;Powered by TorrentTrader Classic v". $GLOBALS['ttversion'] .
    " &nbsp; <a href=http://www.torrenttrader.org target=_blank>www.torrenttrader.org</a>";
echo "&nbsp;&nbsp; Theme By <a href=http://www.nikkbu.com target=_blank>Nikkbu</a>";
//
// *******************************************************************************
//                    Do Not Remove The "Theme By Nikkbu"
// *******************************************************************************
//			PLEASE DO NOT REMOVE THE POWERED BY LINE, SHOW SOME SUPPORT!
// *******************************************************************************
//
?>
    </div>
</div>


</BODY>
</HTML>