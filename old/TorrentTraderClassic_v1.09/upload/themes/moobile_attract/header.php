<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?= $title ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="imagetoolbar" content="no" />
<link rel="stylesheet" type="text/css" href="themes/moobile_attract/theme.css" />
<script src="<?= TT_JS_URL ?>/theme.js"></script>

</head>
<BODY class=pastyla style="MARGIN-TOP: 0px; SCROLLBAR-FACE-COLOR: #9ba87b; SCROLLBAR-HIGHLIGHT-COLOR: #cccc99; SCROLLBAR-SHADOW-COLOR: #666666; SCROLLBAR-ARROW-COLOR: #f5ffd5; SCROLLBAR-TRACK-COLOR: #f5ffd5; SCROLLBAR-BASE-COLOR: #ffffff; scrollbar-dark-shadow-color: #3333ff; scrollbar-3d-light-color: #CCCC99" text=#000000 vLink=#999999 link=#999999 bgColor=#ffffff>
<CENTER>
<!-- DEBUT DE LA PAGE -->
<table cellSpacing=0 cellPadding=0 width=770 border=0>
<tr>
<td>
<div align="center">
	<table border=0 width="40%" cellspacing="0">
<tr>
<td colspan="2">

<TABLE cellSpacing=0 cellPadding=0 width=770 border=0 background=themes/moobile_attract/images/bg_logo.jpg>
<TBODY>
<TR>
<TD class=bb2 vAlign=center noWrap>
<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
<TBODY>
<TR height=41>
<TD vAlign=top align=left>&nbsp;</TD>
</TR></TBODY></TABLE><BR>&nbsp;</TD>
</TR>
</TBODY>
</TABLE>
<!-- DEBUT MENU PRINCIPAL -->
<TABLE class=b2 cellSpacing=0 cellPadding=4 width=770 bgColor=#d2d2d2 border=0>
<TBODY>
<TR>
<TD align=middle width="100%">
          <A class=navi0 href=index.php><?= $txt['HOME'] ?></a> | 
          <?php if ($FORUMS) {?><a class=navi0 href=forums.php><?= $txt['FORUMS'] ?></a> | <?php } ?>
          <?php if ($IRCCHAT) {?><a class=navi0 href=irc.php><?= $txt['CHAT'] ?></a> | <?php }?>
          <a class=navi0 href=torrents-search.php><?= $txt['SEARCH'] ?></a> | 
          <a class=navi0 href=torrents-upload.php><?= $txt['UPLOADT'] ?></a> | 
          <a class=navi0 href=faq.php><?= $txt['FAQ'] ?></a>
</TD>
</TR>
</TBODY>
</TABLE>
<!-- FIN MENU PRINCIPAL -->
      <TABLE class=b2 cellSpacing=0 cellPadding=4 width=770 bgColor=#eeeeee 
      border=0>
        <TBODY>
        <TR>
          <TD align=middle width="45%">
			<p align="left">
			<?php
    if ($CURUSER) {
		print $CURUSER['username']; 
		echo "(<a href=account-logout.php><font color=#ffffff><b>Logout</b></font></a>)";

		// ger user ratio
		if ($CURUSER["downloaded"] > 0){
			$userratio = number_format($CURUSER["uploaded"] / $CURUSER["downloaded"], 2);
		} else {
            $userratio = $CURUSER["uploaded"] > 0 ? "Inf." : "NA";
		}
		//end

		// get unread messages
		$nmessages = numUserMsg();
		$unread = numUnreadUserMsg();
		//end
			?>
			&nbsp;&#8595;&nbsp;<font color=red><?= mksize($CURUSER['downloaded']) ?></font> - <b>&#8593;&nbsp;</b>
            <font color=green><?= mksize($CURUSER['uploaded']) ?></font> - <?= $txt['RATIO'] ?>: <?= $userratio ?> 
            &nbsp;<?php if ($unread) {
                print("<a href=\"account.php\"><b><font color=#FF0000>New PM" . ($nmessages != 1 ? "s" : "") . " ($unread)</b></a></font>");
            }?>

			<?php 
    } else {
        echo "<a href=account-login.php><font color=#FF0000>". $txt['LOGIN'] . "</font></a> <B>:</B> " .
            "<a href=account-signup.php><font color=#FF0000>" . $txt['REGISTERNEW'] . "</font></a>";
    }
			?>
			</TD>
			<form method="get" action="torrents-search.php">
			<TD><?= $txt['SEARCH'] ?>: </STRONG><br>
			<input type="text" name="search" size="30" value="<?= h($searchstr) ?>" />
<select name="cat">
<option value="0">(All Categories)</option>
<?php 
$cats = genrelist();
$catdropdown = "";
foreach ($cats as $cat) {
    $catdropdown .= "<option value=\"" . $cat["id"] . "\"";
    if ($cat["id"] == $_GET["cat"])
        $catdropdown .= " selected=\"selected\"";
    $catdropdown .= ">" . h($cat["name"]) . "</option>\n";
}
?>
<?= $catdropdown ?>
</select> <input type=submit border=0 name=Search value=Search /></TD>
			</TR></FORM>
			
			</TBODY></TABLE></td>
	</tr>
	<tr>
		<td colspan="2" align=center>
<!-- banner code starts here -->
		   <CENTER><?php 
$content = file_get_contents(TT_ROOT_DIR . '/banners.txt');
$s_con = preg_split('/~/', $content);
$banners = rand(0,(count($s_con)-1));
echo $s_con[$banners];
?></CENTER>
<!-- end banner code -->
		</td>
	</tr>
	<tr>
		<td width="21%" valign=top align=center>
<?php
if (!$CURUSER)

{

begin_block($txt['LOGIN']);
?>
<table border=0 width=100% cellspacing=0 cellpadding=0>
	<tr>
		<form method=post action=account-login.php><td>
		<div align=center>
		<table border=0 cellpadding=5">
			<tr><td>
				<p align=center><font face=Verdana size=1><b><?= $txt['USER'] ?>:</b></font></td><td align=left>
				<input type=text size=10 name=username style="font-family: Verdana; font-size: 8pt; font-weight: bold; border-style: solid; border-width: 1px; background-color: #C0C0C0" /></td></tr>
			<tr><td><font face=Verdana size=1><b><?= $txt['PASS'] ?>:</b></font></td><td align=left>
				<input type=password size=10 name=password style="font-family: Verdana; font-size: 8pt; font-weight: bold; border-style: solid; border-width: 1px; background-color: #C0C0C0" /></td></tr>
			<tr><td>&nbsp;</td><td align=left>
				<input type=submit value=Verify style="font-family: Verdana; font-size: 8pt; font-weight: bold; border-style: solid; border-width: 1px"></td></tr>
		</table>
		</td></form>
	</tr>
	<tr>
<td align="center"><a href="account-delete.php"><?= $txt['DELETE_ACCOUNT'] ?></a><br><a href="account-recover.php"><?= $txt['RECOVER_ACCOUNT'] ?></a></td></tr>
	</table>
<?php
end_block();

} else {

    $styles = Helper::getStylesheets();
    $langs = Helper::getLanguages();

begin_block($CURUSER['username']);
?>
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
<td align="center"><a href="account.php"><?= $txt['ACCOUNT'] ?></a> <br> <?php if (get_user_class() > UC_VIP) {
print("<a href=admin.php>" . $txt['STAFFCP'] . "</a>"); }?></font></tr>

</table>
<?php
end_block();

}

// invite block
if ($CURUSER)
{
    if ($INVITEONLY) {
        $invites = $CURUSER["invites"];
        begin_block($txt['INVITES']);
        ?>
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr><td align="center"><?= $txt['YOUHAVE'] ?> <?= $invites ?> <?= $txt['INVITES'] ?><br></td></tr>
        <?php if ($invites > 0 ){ ?>
        <tr><td align="center"><a href=invite.php><?= $txt['SENDANINVITE'] ?></a><br></td></tr>
        <?php } ?>
        </table>
        <?php
        end_block();
    }
}
// end invite block

begin_block($txt['NAVIGATION']);
?>

· <a href="index.php"><?= $txt['HOME'] ?></a><br />
&nbsp;&nbsp;· <a href="torrents-search.php"><?= $txt['SEARCH_TITLE'] ?></a><br />
&nbsp;&nbsp;· <a href="torrents-upload.php"><?= $txt['UPLOADT'] ?></a><br />
&nbsp;&nbsp;· <a href="torrents-needseed.php"><?= $txt['UNSEEDED'] ?></a><br />
&nbsp;&nbsp;· <a href="viewrequests.php"><?= $txt['REQUESTED'] ?></a><br />
&nbsp;&nbsp;· <a href="torrents-today.php"><?= $txt['TODAYS_TORRENTS'] ?></a><br /><br />
				  <CENTER><a href="rssinfo.php"><img src="images/rss2.gif" border=0 alt="XML RSS Feed"></a></CENTER>
				  <hr>
· <a href="faq.php"><?= $txt['FAQ'] ?></a><br />
· <a href="extras-stats.php"><?= $txt['TRACKER_STATISTICS'] ?></a><br />
<?php if ($FORUMS) {?>· <a href="forums.php"><?= $txt['FORUMS'] ?></a><br /><?php }?>
<?php if ($IRCCHAT) {?>· <a href="irc.php"><?= $txt['CHAT'] ?></a><br /><?php }?>
· <a href="formats.php"><?= $txt['FILE_FORMATS'] ?></a><br />
· <a href="videoformats.php"><?= $txt['MOVIE_FORMATS'] ?></a><br />
· <a href="staff.php"><?= $txt['STAFF'] ?></a><br />
· <a href="rules.php"><?= $txt['SITE_RULES'] ?></a><br />
· <a href="extras-users.php"><?= $txt['MEMBERS'] ?></a><br /><hr>
· <a href="visitorsnow.php"><?= $txt['ONLINE_USERS'] ?></a><br />
· <a href="visitorstoday.php"><?= $txt['VISITORS_TODAY'] ?></a><br />

<?php if(get_user_class() > UC_VIP) {?><hr>
· <a href="admin.php"><?= $txt['STAFFCP'] ?></a><br /><?php }?>
<br />

<?php 
end_block();


if ($DONATEON) {
    begin_block($txt['DONATIONS'], 'center');
    $row = getDonations();
    echo "<br><b>" . $txt['TARGET'] . ": </b><font color=\"red\">$" . $row['requireddonations'] . "</font><br><b>" .
        $txt['DONATIONS'] . ": </b><font color=\"green\">$" . $row['donations'] . "</font></center><br>
        <div align=left><B><font color=#FF6600>&#187;</font></B> <a href=\"donate.php\">" . $txt['DONATE'] . "</a><br>";
    end_block();
}


//start side banner
echo "<br><CENTER>";
$contents = file_get_contents(TT_ROOT_DIR . '/sponsors.txt');
$s_cons = preg_split('/~/', $contents);
$bannerss = rand(0,(count($s_cons)-1));
echo $s_cons[$bannerss], '
    </CENTER><br>';
//end side banner


?>
		</td>
		<td width="77%" valign=top align=center>