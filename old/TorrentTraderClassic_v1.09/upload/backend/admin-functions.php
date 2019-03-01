<?php

if (preg_match('~admin-functions\.php~', $_SERVER['REQUEST_URI']))
    die;

require_once TT_DIR.'/themes/' . $GLOBALS['ss_uri'] . '/block.php';

function autolink($al_url, $al_msg)	// create autolink
{
	echo "\n<meta http-equiv=\"refresh\" content=\"3; URL=$al_url\">\n";
	echo "<b>$al_msg</b>\n";
	echo "<p>\n<b>Redirecting ...</b>\n";
	echo "<p>\n[ <a href='$al_url'>link</a> ]\n";
	echo "</td>\n</tr>\n</table>\n</td>\n</tr>\n</table>\n</body>\n</html>\n";
	stdfoot();
	exit;
}

// create navi-menu
function adminmenu()
{
	global $sid, $PHP_SELF;

    // count pending accounts
    $pusers = DB::fetchColumn('SELECT COUNT(*) FROM users WHERE status = ?', ['pending']);

    // Get Last Cleanup
    $row = DB::fetchColumn('SELECT value_u FROM avps WHERE arg = ? LIMIT 1', ['lastcleantime']);
    if (!$row)
        $lastclean = "never done...";
    else {
        $row[0] = time()-$row[0]; $days=intval($row[0] / 86400);$row[0]-=$days*86400;
        $hours = intval($row[0] / 3600); $row[0]-=$hours*3600; $mins=intval($row[0] / 60);
        $secs = $row[0]-($mins*60);
        $lastclean = "$days days, $hours hrs, $mins minutes, $secs seconds ago.";
    }

    begin_frame("Admin Menu");
    print "<CENTER><b>Users Awaiting Validation: <a href=admin.php?act=confirmreg>".
        $pusers."</a></b><BR><a href=admin-cheats.php>Check For Possible Cheaters</a><BR><BR>";

    print "Last cleanup performed: ".$lastclean." - <a href=".$GLOBALS['SITEURL'].
        "/backend/force-cleanup.php>[FORCE CLEAN]</a><br><br>[A] Admin Only - [S] Super Moderator Only<br></CENTER>\n";

    // $file = @file_get_contents('http://www.torrenttrader.org/version.php');
    $file = '1.09';

    if ($GLOBALS['ttversion'] >= $file) {
        echo "<BR><center><b>You have the latest Version of TorrentTrader Installed: v".$file."</b></center><BR><BR>";
    } else {
        echo "<BR><center><b><font color=red>NEW Version of TorrentTrader now available: v".$file." you have v".
            $ttversion."<BR> Please visit <a href=http://www.torrenttrader.org>TorrentTrader.org</a> to upgrade.</font></b></center>
            <BR><BR>";
    }

?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<TR>
    <td width=%20 align=center>
        <a href='admin.php?act=settings'>
        <img src="images/admin/settings.png" border=0 width=32 height=32>
        <br>Main Settings</a> [A]
    </td>
    <td width=%20 align=center>
        <a href='mysql_stats.php'>
        <img src="images/admin/mysqlstats.gif" border=0 width=32 height=32>
        <br>Mysql Stats</a>
    </td>
    <td width=%20 align=center>
        <a href='admin-statistics.php'>
        <img src="images/admin/statistics.gif" border=0 width=32 height=32>
        <br>Statistics</a>
    </td>
    <td width=%20 align=center>
        <a href='admin.php?act=trackerload'>
        <img src="images/admin/trackerload.png" border=0 width=32 height=32>
        <br>Tracker Load</a>
    </td>
    <td width=%20 align=center>
        <a href='admin.php?act=donations'>
        <img src="images/admin/donations.gif" border=0 width=32 height=32>
        <br>Donations</a> [A]
    </td>
</tr>
<tr><td colspan=5></td></tr>
<TR>
    <td width=%20 align=center>
        <a href='admin.php?act=databaseadmin'>
        <img src="images/admin/database.png" border=0 width=32 height=32>
        <br>Database</a> [A]
    </td>
    <td width=%20 align=center>
        <a href='modrules.php'>
        <img src="images/admin/rules.gif" border=0 width=32 height=32>
        <br>Site Rules</a>
    </td>
    <td width=%20 align=center>
        <a href='faqmanage.php'>
        <img src="images/admin/faq.png" border=0 width=32 height=32>
        <br>FAQ</a>
    </td>
    <td width=%20 align=center>
        <a href='admin.php?act=news'>
        <img src="images/admin/news.png" border=0 width=32 height=32>
        <br>Site News</a>
    </td>
    <td width=%20 align=center>
        <a href='admin.php?act=sitetexts'>
        <img src="images/admin/disclaimer.gif" border=0 width=32 height=32>
        <br>Disclaimer</a>
    </td>
</tr>
<tr><td colspan=5></td></tr>
<TR>
    <td width=%20 align=center>
        <a href='admin.php?act=censor'>
        <img src="images/admin/censor.png" border=0 width=32 height=32>
        <br>Word Censor</a> [S]
    </td>
    <td width=%20 align=center>
        <a href='admin.php?act=lang'><img src="images/admin/langs.png" border=0 width=32 height=32>
        <br>Languages</a> [A]
    </td>
    <td width=%20 align=center>
        <a href='admin.php?act=style'><img src="images/admin/themes.gif" border=0 width=32 height=32>
        <br>Themes</a> [A]
    </td>
    <td width=%20 align=center>
        <a href='admin.php?act=view_log'><img src="images/admin/log.gif" border=0 width=32 height=32>
        <br>Site Log</a>
    </td>
    <td width=%20 align=center>
        <a href='admin.php?act=ircannounce'><img src="images/admin/ircann.png" border=0 width=32 height=32>
        <br>IRC Announcer</a>
    </td>
</tr>
<tr><td colspan=5></td></tr>
<TR>
    <td width=%20 align=center><a href='admin-pmessages.php'><img src="images/admin/massmessage.gif" border=0 width=32 height=32>
        <br>Mass Message</a> [A]</td>
    <td width=%20 align=center><a href='admin.php?act=banner'><img src="images/admin/banners.gif" border=0 width=32 height=32>
        <br>Banners / Sponsor</a> [A]</td>
    <td width=%20 align=center><a href='admin.php?act=userdonations'><img src="images/admin/donors.gif" border=0 width=32 height=32>
        <br>Donors</a> [A]</td>
    <td width=%20 align=center><a href='admin.php?act=forum'><img src="images/admin/forums.gif" border=0 width=32 height=32>
        <br>Forum Management</a> [A]</td>
    <td width=%20 align=center><a href='admin.php?act=peerg'><img src="images/admin/peerg.gif" border=0 width=32 height=32>
        <br>PeerGuardian IP Import</a><BR>[DISABLED]</td>
</tr>
<tr><td colspan=5></td></tr>
<TR>
    <td width=%20 align=center><a href='admin-search.php'><img src="images/admin/userssearch.gif" border=0 width=32 height=32><br>User Search</a></td>
    <td width=%20 align=center><a href='admin.php?act=users'><img src="images/admin/users.gif" border=0 width=32 height=32><br>Users</a></td>
    <td width=%20 align=center><a href='admin.php?act=rws-watched'><img src="images/admin/watchedusers.gif" border=0 width=32 height=32><br>Ratio Warn System<BR>Watched Users</a></td>
    <td width=%20 align=center><a href='admin.php?act=rws-warned'><img src="images/admin/warnedusers.gif" border=0 width=32 height=32><br>Ratio Warn System<BR>Warned Users</a></td>
    <td width=%20 align=center><a href='admin-ipsearch.php'><img src="images/admin/duplicate.gif" border=0 width=32 height=32><br>Duplicate IPs</a></td>
</tr>
<tr><td colspan=5></td></tr>
<!-- needs imgs -->
<TR>
    <td width=%20 align=center>
        <a href='iptest.php'><img src="images/admin/ipchecker.gif" border=0 width=32 height=32>
        <br>IP Checker</a>
    </td>
    <td width=%20 align=center>
        <a href='findnotconnectable.php'><img src="images/admin/unconnectable.gif" border=0 width=32 height=32>
        <br>Unconnectable Users</a> [A]
    </td>
    <td width=%20 align=center>
        <a href='admin.php?act=warneddaccounts'><img src="images/admin/warnedaccounts.gif" border=0 width=32 height=32>
        <br>Warned Accounts</a>
    </td>
    <td width=%20 align=center>
        <a href='admin.php?act=disabledaccounts'><img src="images/admin/disabledaccounts.gif" border=0 width=32 height=32>
        <br>Disabled Accounts</a>
    </td>
    <td width=%20 align=center><a href='admin.php?act=bans'><img src="images/admin/blocked.gif" border=0 width=32 height=32>
        <br>Blocked / Banned IPs</a>
    </td>
</tr>
<tr><td colspan=5></td></tr>
<!-- needs imgs -->
<TR>
    <td width=%20 align=center>
        <a href='admin.php?act=confirmreg'><img src="images/admin/confirmaccounts.gif" border=0 width=32 height=32>
        <br>Confirm Accounts</a>
    </td>
    <td width=%20 align=center><a href='admin-confirmall.php'><img src="images/admin/autoconfirm.gif" border=0 width=32 height=32>
        <br>Auto Confirm<br>Accounts</a> [A]
    </td>
    <td width=%20 align=center><a href='uploadapp.php'><img src="images/admin/uploadervote.gif" border=0 width=32 height=32>
        <br>Uploader Applications</a>
    </td>
    <td width=%20 align=center><a href='uploaders.php'><img src="images/admin/uploaders.gif" border=0 width=32 height=32>
        <br>Uploaders Management</a>
    </td>
    <td width=%20 align=center><a href='admin-delreq.php'><img src="images/admin/requests.gif" border=0 width=32 height=32>
        <br>Requests</a>
    </td>
</tr>
<tr><td colspan=5></td></tr>
<!-- needs imgs -->
<TR>
    <td width=%20 align=center><a href='admin-category.php'><img src="images/admin/categories.gif" border=0 width=32 height=32>
        <br>Categories</a> [A]
    </td>
    <td width=%20 align=center><a href='admin.php?act=torrents'><img src="images/admin/torrents.gif" border=0 width=32 height=32>
        <br>Torrent Management</a>
    </td>
    <td width=%20 align=center>
        <a href='admin.php?act=bannedtorrents'><img src="images/admin/bannedtorrents.gif" border=0 width=32 height=32>
        <br>Banned Torrents</a>
    </td>
    <td width=%20 align=center><a href='trackers.php'><img src="images/admin/external.gif" border=0 width=32 height=32>
        <br>External Trackers</a>
    </td>
    <td width=%20 align=center><a href='admin.php?act=msgspy'><img src="images/admin/messagespy.gif" border=0 width=32 height=32>
        <br>Message Spy</a> [A]
    </td>
</tr>
</table>

<?php

end_frame();
}

