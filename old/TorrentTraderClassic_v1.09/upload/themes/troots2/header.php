<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?= $title ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="imagetoolbar" content="no" />
<link rel="stylesheet" type="text/css" href="themes/troots2/css/style.css" />
<script src="<?= TT_JS_URL ?>/theme.js"></script>

</head>

<BODY bgColor=#ffffff leftMargin=0 topMargin=0 MARGINHEIGHT="0" MARGINWIDTH="0">
<DIV id=altDiv style="DISPLAY: none; POSITION: absolute"></DIV>
<DIV align=center><A name=top></A>
<!-- Network Bar Flash Section -->
<TABLE cellSpacing=0 cellPadding=0 width=780 border=0>
  <TBODY>
  <TR>
    <TD colSpan=3><!-- /Network Bar Div Section --></TD></TR>
  <TR>
    <TD colSpan=3 height=10></TD></TR>
  <TR>
    <TD colSpan=3>
      <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
        <TBODY>
        <TR>
          <TD width=77 rowSpan=2>
            &nbsp;</TD>
          <TD class=topright style="PADDING-TOP: 2px" align=right colSpan=2>
            <TABLE cellSpacing=0 cellPadding=0 width=165 border=0>
              <TBODY>
              <TR>
                <TD colSpan=6><IMG height=12
                  src="themes/troots2/images/spacer.gif" 
                  width=7></TD></TR>
              <TR>
                <TD>&nbsp;</TD>
                <TD 
                style="PADDING-RIGHT: 10px; PADDING-LEFT: 0px; PADDING-TOP: 1px">&nbsp;</TD>
                <TD style="PADDING-LEFT: 2px; PADDING-TOP: 3px">&nbsp;</TD>
                <TD 
                style="PADDING-RIGHT: 10px; PADDING-LEFT: 4px; PADDING-TOP: 1px">&nbsp;</TD>
                <TD style="PADDING-LEFT: 2px; PADDING-TOP: 3px">&nbsp;</TD>
                <TD 
                style="PADDING-RIGHT: 10px; PADDING-LEFT: 4px; PADDING-TOP: 1px">&nbsp;</TD></TR></TBODY></TABLE></TD></TR>
        <TR>
          <TD width=393>
            <DIV style="MARGIN-BOTTOM: 5px; MARGIN-LEFT: 3px">
				<a href="index.php">
				<IMG alt="" src="themes/troots2/images/logo.gif" border=0></a></DIV></TD>
          <TD class=topright width=310><BR>
            <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
              <TBODY>
              <TR>
                <TD style="PADDING-TOP: 10px">
                  <TABLE height=8 cellSpacing=0 cellPadding=0 width=10>
                    <TBODY>
                    <TR>
                      <TD bgColor=#4b6892 colSpan=2><IMG height=1 
                        src="themes/troots2/images/spacer.gif"></TD></TR>
                    <TR>
                      <TD bgColor=#4b6892><IMG 
                        src="themes/troots2/images/spacer.gif" 
                        width=1></TD>
                      <TD width="100%" height="100%"></TD></TR></TBODY></TABLE></TD>
                <TD style="PADDING-TOP: 2px">
                  <DIV class=blueheader 
                  style="PADDING-RIGHT: 2px; PADDING-LEFT: 5px"><?php  if ($CURUSER) { print "$CURUSER[username]"; } else { ?>Login <?php }?></DIV></TD>
                <TD style="PADDING-TOP: 10px" width="100%">
                  <TABLE height=8 cellSpacing=0 cellPadding=0 width="100%">
                    <TBODY>
                    <TR>
                      <TD bgColor=#4b6892 colSpan=4><IMG height=1 
                        src="themes/troots2/images/spacer.gif"></TD></TR>
                    <TR>
                      <TD height="100%"><IMG height=8 
                        src="themes/troots2/images/spacer.gif" 
                        width=30></TD>
                      <TD width="100%" height="100%"></TD>
                      <TD bgColor=#4b6892><IMG 
                        src="themes/troots2/images/spacer.gif" 
                        width=1></TD></TR></TBODY></TABLE></TD></TR></TBODY></TABLE></TD></TR></TBODY></TABLE></TD></TR>
  <TR>
    <TD class=mainHeader>
      <TABLE height="100%" cellSpacing=0 cellPadding=0 width="100%" border=0>
        <TBODY>
        <TR>
          <TD style="VERTICAL-ALIGN: bottom" width=471>
            <TABLE height=34 cellSpacing=0 cellPadding=0 width=471 
            bgColor=#3f89c3 border=0>
              <TBODY>
              <TR>
                <TD class=mainnavigation style="VERTICAL-ALIGN: middle" 
                width=125 
                background="themes/troots2/images/tab_red1.jpg" 
                bgColor=#dd1212 height=34>
                  <DIV style="PADDING-LEFT: 20px">
					<a href=index.php><span style="color: #FFFFFF"><?= $txt['HOME'] ?></span></a></DIV></TD>
                <TD class=mainnavigation style="VERTICAL-ALIGN: middle" width=116 background="themes/troots2/images/tab_blue1.jpg" bgColor=#3f89c3 height=34>
				<a href=torrents-search.php><?= $txt['SEARCH'] ?></a></TD>
                <TD class=mainnavigation style="VERTICAL-ALIGN: middle" 
                width=115 
                background="themes/troots2/images/tab_blue2.jpg" 
                bgColor=#3f89c3 height=34>
				<a href=torrents-upload.php><?= $txt['UPLOADT'] ?></a></TD>
                <TD class=mainnavigation style="VERTICAL-ALIGN: middle" 
                width=115 
                background="themes/troots2/images/tab_blue2.jpg" 
                bgColor=#3f89c3 height=34>
				<a href=faq.php><?= $txt['FAQ'] ?></a></TD></TR></TBODY></TABLE></TD>
          <TD class=topright style="VERTICAL-ALIGN: middle" width=310 
            height=34><TABLE cellSpacing=0 cellPadding=0 width="100%" 
              border=0><TBODY>
              <TR>
			<?php if ($CURUSER) {?>
<TD style="VERTICAL-ALIGN: middle; TEXT-ALIGN: center">
<?php
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

			&nbsp;&#8595;&nbsp;<font color=red><?= mksize($CURUSER['downloaded']) ?></font> - <b>&#8593;&nbsp;</b><font color=green><?php 
                print mksize($CURUSER['uploaded']); ?></font> - <?= $txt['RATIO'] ?>: <?php
                print $userratio; ?> &nbsp;<?php if ($unread) {	print("<a href=\"account.php\"><b><font color=#FF0000>New PM" .
                ($nmessages != 1 ? "s" : "") . " ($unread)</b></a></font></TD></TR>");
                }?>

			<?php
			} else {
				echo "<div align=center><b><a style='color: red' href=account-login.php>". $txt['LOGIN'] .
                    "</a> : <a style='color: red' href=account-signup.php>" . $txt['REGISTERNEW'] . "</a></B></div></TD></TR>";
			}
			?>
</TBODY></TABLE></TD></TR>
        <TR>
          <TD class=toplinks style="VERTICAL-ALIGN: middle" width=780 
          background="themes/troots2/images/tile_h.gif" 
          bgColor=#d70d0d colSpan=2 height=39>
            <TABLE cellSpacing=0 cellPadding=0 width=780 border=0>
              <TBODY>
              <TR>
                <TD width=28 height=39><IMG height=39 src="themes/troots2/images/red_block.gif" width=28></TD>
                <TD style="VERTICAL-ALIGN: middle" width=752>
<NOBR>
<?php if ($FORUMS) {?><A style="TEXT-DECORATION: none" href=forums.php><?= $txt['FORUMS'] ?></A><IMG hspace=11 src="themes/troots2/images/div_red.gif" align=absMiddle><?php }?>
<A style="TEXT-DECORATION: none" href="torrents-needseed.php"><?= $txt['UNSEEDED'] ?></A><IMG hspace=11 src="themes/troots2/images/div_red.gif" align=absMiddle>
<A style="TEXT-DECORATION: none" href="viewrequests.php"><?= $txt['REQUESTED'] ?></A><IMG hspace=11 src="themes/troots2/images/div_red.gif" align=absMiddle>
<A style="TEXT-DECORATION: none" href="torrents-today.php"><?= $txt['TODAYS_TORRENTS'] ?></A><IMG hspace=11 src="themes/troots2/images/div_red.gif" align=absMiddle>
<A style="TEXT-DECORATION: none" href="formats.php"><?= $txt['FILE_FORMATS'] ?></A><IMG hspace=11 src="themes/troots2/images/div_red.gif" align=absMiddle>
<A style="TEXT-DECORATION: none" href="videoformats.php"><?= $txt['MOVIE_FORMATS'] ?></A><IMG hspace=11 src="themes/troots2/images/div_red.gif" align=absMiddle>
<A style="TEXT-DECORATION: none" href="rules.php"><?= $txt['SITE_RULES'] ?></A>
</NOBR>
</TD>

</TR></TBODY></TABLE></TD></TR>
        <TR>
          <TD class=toplinks background="themes/troots2/images/tile_h_1.gif" bgColor=#3f89c3 colSpan=2>
            <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
              <TBODY>
              <TR>
<TD class=bluenav style="BACKGROUND-POSITION: right top; PADDING-LEFT: 13px; FONT-SIZE: 11px; VERTICAL-ALIGN: middle; COLOR: #ffffff; BACKGROUND-REPEAT: no-repeat" width="100%" background="themes/troots2/images/blue_block.gif" height=34>
<?php  if (!$CURUSER) { ?>
<form method=post action=account-login.php>
<input onfocus="this.value=''" type=text size=10 value="User Name" name=username style="font-family: Verdana; font-size: 8pt; font-weight: bold; border-style: solid; border-width: 0px">
<input onfocus="this.value=''" type=password size=10 value="ibfrules" name=password style="font-family: Verdana; font-size: 8pt; font-weight: bold; border-style: solid; border-width: 0px">
<input type=submit value=Verify style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 0px">
</TD></form>
<?php } else {

$styles = Helper::getStylesheets();
$langs = Helper::getLanguages();

?><form method="post" action="take-theme.php">
<font size=1><?= $txt['THEME'] ?>:</font> <select name=stylesheet style="font-family: Verdana; font-size: 8pt; font-weight: bold; border-style: solid; border-width: 0px"><?= $styles ?></select>
<font size=1><?= $txt['LANG'] ?>:</font> <select name=language style="font-family: Verdana; font-size: 8pt; font-weight: bold; border-style: solid; border-width: 0px"><?= $langs ?></select>
<input type="submit" value="<?= $txt['APPLY'] ?>" style="font-family: Verdana; font-size: 8pt; font-weight: bold; border-style: solid; border-width: 0px"><IMG hspace=11 src="themes/troots2/images/div_red.gif" align=absMiddle>
<A style="TEXT-DECORATION: none" href="account.php"><?= $txt['ACCOUNT'] ?></a><IMG hspace=11 src="themes/troots2/images/div_red.gif" align=absMiddle>
<?php if (get_user_class() > UC_VIP) {?><a style="TEXT-DECORATION: none;" href="admin.php"><?= $txt['STAFFCP'] ?></a><IMG hspace=11 src="themes/troots2/images/div_red.gif" align=absMiddle><?php }?>
<A style="TEXT-DECORATION: none" href=account-logout.php>Logout</a>
</TD>
<?php }?>
</TR></TBODY></TABLE></TD></TR>
        </TBODY></TABLE></TD></TR>
  <TR>
    <TD>
      <TABLE style="MARGIN: 0px 0px" width="100%">
        <TBODY>
        <TR>
<TD style="COLOR: rgb(55,128,185)" align=left>&nbsp;</TD>
<TD id=ShowHideFlash2 vAlign=top align=right width=250>&nbsp;</TD></TR></TBODY></TABLE>
      <TABLE 
      style="BORDER-RIGHT: #31659c 1px solid; BORDER-TOP: #31659c 1px solid; BORDER-LEFT: #31659c 1px solid; BORDER-BOTTOM: #31659c 1px solid" 
      cellSpacing=0 cellPadding=0 width="100%">
        <TBODY>
        <TR>
          <TD>
            <TABLE width="100%" border=0>
              <TBODY>
              <TR>
                <TD style="PADDING-LEFT: 5px" vAlign=top width="170">
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
<td align="center"><a href="account-delete.php"><?= $txt['DELETE_ACCOUNT'] ?></a>
    <br><a href="account-recover.php"><?= $txt['RECOVER_ACCOUNT'] ?></a></td></tr>
	</table>
<?php
end_block();

} else {

begin_block("$CURUSER[username]");
	$avatar = $CURUSER["avatar"];
			if (!$avatar) {
		$avatar = "$SITEURL/images/default_avatar.gif";
			}
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td>
<table border=0 cellspacing=0 cellpadding="6" width=100%>
<tr><td align="center"><?= '<img src="'.$avatar.'" border="0" width="80" height="80">' ?></td></tr>
<tr><td align="center">
		<?= $txt['DOWNLOADED'] ?>: <font color=red><?= mksize($CURUSER['downloaded']) ?></font><br>
		<?= $txt['UPLOADED'] ?>: <font color=green><?= mksize($CURUSER['uploaded']) ?></font><br>
		<?= $txt['RATIO'] ?>: <?= $userratio ?><br>
</td></tr>
<tr><td align="center"></td></tr>
</table></form></td></tr>
<tr>
<td align="center"><a href="account.php"><?= $txt['ACCOUNT'] ?></a> <br> <?php if (get_user_class() > UC_VIP) {
print("<a href=admin.php>" . $txt['STAFFCP'] . "</a>");}?></font></tr>

</table>
<?php
end_block();

}

// invite block
if ($CURUSER)
{
	if ($INVITEONLY){
		$invites = $CURUSER["invites"];
		begin_block($txt['INVITES']);
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
<?php if ($FORUMS) {?>· <a href="forums.php"><?= $txt['FORUMS'] ?></a><br><?php }?>
· <a href="formats.php"><?= $txt['FILE_FORMATS'] ?></a><br>
· <a href="videoformats.php"><?= $txt['MOVIE_FORMATS'] ?></a><br>
· <a href="staff.php"><?= $txt['STAFF'] ?></a><br>
· <a href="rules.php"><?= $txt['SITE_RULES'] ?></a><br>
· <a href="extras-users.php"><?= $txt['MEMBERS'] ?></a><br><hr>
· <a href="visitorsnow.php"><?= $txt['ONLINE_USERS'] ?></a><br>
· <a href="visitorstoday.php"><?= $txt['VISITORS_TODAY'] ?></a><br>

<?php if(get_user_class() > UC_VIP) {?><hr>
· <a href="admin.php"><?= $txt['STAFFCP'] ?></a><br><?php }?>
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

//start side banner
echo "<br><CENTER>";
$contents = file_get_contents(TT_ROOT_DIR . '/sponsors.txt');
$s_cons = preg_split('/~/', $contents);
$bannerss = rand(0,(count($s_cons)-1));
echo $s_cons[$bannerss], '
    </CENTER><br>';
//end side banner


?>
                </TD>
                <TD vAlign=top>
<!-- banner code starts here -->
<br><CENTER><?php
$content = file_get_contents(TT_ROOT_DIR . '/banners.txt');
$s_con = preg_split('/~/', $content);
$banners = rand(0,(count($s_con)-1));
echo $s_con[$banners];
?></CENTER><br>
<!-- end banner code -->