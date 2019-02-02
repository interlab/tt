
<?php
if (!$CURUSER) {

begin_block($txt['LOGIN']);

?>
<table border=0 width=100% cellspacing=0 cellpadding=0>
	<tr>
		<form method=post action=account-login.php><td>
		<div align=center>
		<table border=0 cellpadding=5">
			<tr><td>
				<p align=center><font face=Verdana size=1><b><?= $txt['USER'] ?>:</b></font></td><td align=left>
				<input type=text size=10 name=username style="font-family: Verdana; font-size: 8pt; font-weight: bold; border-style: solid; border-width: 1px; background-color: #C0C0C0" /></td>
            </tr>
			<tr>
            <td><font face=Verdana size=1><b><?= $txt['PASS'] ?>:</b></font></td>
            <td align=left>
				<input type=password size=10 name=password style="font-family: Verdana; font-size: 8pt; font-weight: bold; border-style: solid; border-width: 1px; background-color: #C0C0C0" /></td>
                </tr>
			<tr>
            <td>&nbsp;</td><td align=left>
				<input type=submit value=Verify style="font-family: Verdana; font-size: 8pt; font-weight: bold; border-style: solid; border-width: 1px"></td>
            </tr>
		</table>
		</td>
        </form>
	</tr>
	<tr>
        <td align="center">
            <a href="account-delete.php"><?= $txt['DELETE_ACCOUNT'] ?></a><br>
            <a href="account-recover.php"><?= $txt['RECOVER_ACCOUNT'] ?></a>
        </td>
    </tr>
	</table>
<?php
end_block();

} else {
    $styles = Helper::getStylesheets();
    $langs = Helper::getLanguages();

begin_block($CURUSER['username']);
?>

<div align="center" class="avat_m">
<?php
$avatar = $CURUSER["avatar"];
$uname = $CURUSER['username'];
if (!$avatar) {
    $avatar = 'images/default_avatar.gif';
}
echo '<img src="' . $avatar . '" alt="' . $uname . '" name="' . $uname . '" title="' . $uname . '" border="0" />';
?>
</div>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><form method="post" action="take-theme.php"><td>
<table border=0 cellspacing=0 cellpadding="6" width=100%>
<tr><td align="center"><B><?= $txt['THEME'] ?> </B>
	<select name=stylesheet style="font-family: Verdana; font-size: 8pt; color: #000000; border: 1px solid #808080; background-color: #C0C0C0" size="1"><?= $styles ?></select></td></tr>
<tr><td align="center"><B><?= $txt['LANG'] ?> </B>
	<select name=language style="font-family: Verdana; font-size: 8pt; color: #000000; border: 1px solid #808080; background-color: #C0C0C0" size="1"><?= $langs ?></select></td></tr>
<tr><td align="center">
	<input type="submit" value="<?= $txt['APPLY'] ?>" style="font-family: Verdana; font-size: 8pt; color: #000000; border: 1px solid #808080; background-color: #C0C0C0"></td></tr>
</table></form></td></tr>
<tr>
<td align="center"><a href="account.php"><img src="images/110/account_icon.gif" border="0" height="10" hspace="5" width="10"><?= $txt['ACCOUNT'] ?></a><br>
<a href="account-details.php?id=<?= $CURUSER['id'] ?>"><img src="images/110/profile_icon.gif" border=0 height=10 hspace=5 width=10><?= $txt['PROFILE'] ?></a><br>
<a href="account-messages.php"><img src="images/110/mail_icon.gif" border=0 height=10 hspace=5 width=10><?= $txt['PM'] ?>: <?= $nmessages ?></a><br>
<?php if (get_user_class() > UC_VIP) {
    echo '<a href="admin.php">'. $txt['STAFFCP'] .'</a>';
} ?></tr>

</table>

<?php

end_block();

}

// invite block
if ($CURUSER) {
	if ($INVITEONLY) {
		$invites = $CURUSER["invites"];
		begin_block("". $txt['INVITES'] ."");
		?>
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr><td align="center"><?= $txt['YOUHAVE'] ?> <?=$invites?> <?= $txt['INVITES'] ?><br></td></tr>
		<?php if ($invites > 0 ){?>
		<tr><td align="center"><a href=invite.php><?= $txt['SENDANINVITE'] ?></a><br></td></tr>
		<?php }?>
		</table>
		<?php
		end_block();
	}
}
//end invite block

begin_block($txt['NAVIGATION']);
?>

· <a href="index.php"><?= $txt['HOME'] ?></a><br>
&nbsp;&nbsp;· <a href="torrents-search.php"><?= $txt['SEARCH_TITLE'] ?></a><br>
&nbsp;&nbsp;· <a href="torrents-upload.php"><?= $txt['UPLOADT'] ?></a><br>
&nbsp;&nbsp;· <a href="torrents-needseed.php"><?= $txt['UNSEEDED'] ?></a><br>
&nbsp;&nbsp;· <a href="viewrequests.php"><?= $txt['REQUESTED'] ?></a><br>
&nbsp;&nbsp;· <a href="torrents-today.php"><?= $txt['TODAYS_TORRENTS'] ?></a><br><br>
<CENTER><a href="rssinfo.php"><img src="images/rss2.gif" border=0 alt="XML RSS Feed"></a></CENTER>
<hr>
· <a href="faq.php"><?= $txt['FAQ'] ?></a><br>
· <a href="extras-stats.php"><?= $txt['TRACKER_STATISTICS'] ?></a><br>
<?php if ($FORUMS) {?>· <a href="forums.php"><?= $txt['FORUMS'] ?></a><br><?php } ?>
<?php if ($IRCCHAT) {?>· <a href="irc.php"><?= $txt['CHAT'] ?></a><br><?php } ?>
· <a href="formats.php"><?= $txt['FILE_FORMATS'] ?></a><br>
· <a href="videoformats.php"><?= $txt['MOVIE_FORMATS'] ?></a><br>
· <a href="staff.php"><?= $txt['STAFF'] ?></a><br>
· <a href="rules.php"><?= $txt['SITE_RULES'] ?></a><br>
· <a href="extras-users.php"><?= $txt['MEMBERS'] ?></a><br><hr>
· <a href="visitorsnow.php"><?= $txt['ONLINE_USERS'] ?></a><br>
· <a href="visitorstoday.php"><?= $txt['VISITORS_TODAY'] ?></a><br>

<?php if (get_user_class() > UC_VIP) { ?><hr>
· <a href="admin.php"><?= $txt['STAFFCP'] ?></a><br><?php } ?>
<br>

 <?php
end_block();

if ($DONATEON) {
    begin_block($txt['DONATIONS'], 'center');
    $row = getDonations();
    echo "<br><b>". $txt['TARGET'] .": </b><font color=\"red\">$" . $row['requireddonations'] . "</font><br><b>".
        $txt['DONATIONS'] . ": </b><font color=\"green\">$" . $row['donations'] . "</font></center><br>
        <div align=left><B><font color=#FF6600>&#187;</font></B> <a href=\"donate.php\">". $txt['DONATE'] ."</a><br>";
    end_block();
}

// start side banner
echo "<br><CENTER>";
$contents = file_get_contents(ST_ROOT_DIR . '/sponsors.txt');
$s_cons = preg_split('/~/', $contents);
$bannerss = rand(0,(count($s_cons)-1));
echo $s_cons[$bannerss], '
    </CENTER><br>';
// end side banner
?>
