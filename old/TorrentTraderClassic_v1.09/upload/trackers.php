<?php
require "backend/functions.php";

dbconn(false);
stdhead("External Trackers");

begin_frame("External Trackers");

?>
<BR><BR><center>
<B>TorrentTrader as default can only track torrents with your sites announce URL<BR><BR>
To add the ability to scrape/index externally tracked torrent files please visit <a href=http://www.torrentrader.org>torrenttrader.org</a> and update to a premium account.  Here you will find many modifications not included as default, including a external stats hack.
</center><BR><BR>

<?

end_frame();
stdfoot();
?>