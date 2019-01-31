<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<script language="JavaScript">
<!--
function Smilies(Smilie)
{
    document.Form.body.value += Smilie + " ";
    document.Form.body.focus();
}
//-->
</script>
<title><?= $title ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="imagetoolbar" content="no" />
<link rel="stylesheet" type="text/css" href="themes/default/theme.css" />

<script>

var myimages = new Array();
function preloadimages() {
    for (i = 0; i < preloadimages.arguments.length; i++) {
        myimages[i] = new Image();
        myimages[i].src=preloadimages.arguments[i]
    }
}

preloadimages("images/space.gif");


var g_nExpando = 0;
// To make the cross clickable in every browser
function putItemInState(n,bState)
{
   var oItem,oGif;
        oItem = document.getElementById("descr"+n);
        oGif = document.getElementById("expandoGif"+n);
   
   if (bState == 'toggle')
        bState=(oItem.style.display=='block');

   if(bState)
   {
       bState=(oItem.style.display='none');
       bState=(oGif.src='images/cross.gif');
   }
   else {
       bState=(oItem.style.display='block');
       bState=(oGif.src='images/noncross.gif');
   }
}

function expand(nItem)
{
    putItemInState(nItem,'toggle');
}

function expandAll()
{
    if (!g_nExpando)
    {
        document.all.chkFlag.checked=false;
        return;
    }
    var bState=!document.all.chkFlag.checked;
    for(var i=0; i<g_nExpando; i++)
        putItemInState(i,bState);
}

var tns6=document.getElementById&&!document.all
var ie=document.all

function show_text(thetext, whichdiv){
if (ie) {eval("document.all."+whichdiv).innerHTML=thetext;}
else if (tns6) {document.getElementById(whichdiv).innerHTML=thetext;}
}

function resetit(whichdiv){
if (ie) eval("document.all."+whichdiv).innerHTML=''
else if (tns6) document.getElementById(whichdiv).innerHTML=''
}

</script>

<?php
global $st;

echo isset($st['js_files']) ? $st['js_files'] : '';
?>

</head>

<BODY LEFTMARGIN="0" TOPMARGIN="0" MARGINWIDTH="0" MARGINHEIGHT="0" align="center">


<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
<!-- THIS IS THE TOP LOGO AREA 3 CELLS -->
		<TD WIDTH=497><a href="index.php"><IMG SRC="themes/default/images/template1_01.jpg" WIDTH=497 HEIGHT=80 border=0 ALT="<?= $SITENAME  ?> logo" ></a></TD>
		<TD ROWSPAN=3 WIDTH=141><IMG SRC="themes/default/images/template1_02.jpg" WIDTH=141 HEIGHT=132 ALT=""></TD>
		<TD ROWSPAN=3 WIDTH=100%><IMG SRC="themes/default/images/template1_03.jpg" WIDTH=100% HEIGHT=132 ALT=""></TD>
<!-- END TOP LOGO AREA -->	
	</TR>
	<TR>
<!-- TOP NAV MENU AND USER RATIO AREA -->
		<TD background="themes/default/images/template1_04.jpg" WIDTH=497 HEIGHT=24>
			<FONT COLOR=#FFFFFF>&nbsp;
			<?php if ($CURUSER) {
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
		// end
			?>

			&nbsp;&#8595;&nbsp;<font color=red><?= mksize($CURUSER['downloaded']) ?></font> - <b>&#8593;&nbsp;</b>
            <font color=green><?= mksize($CURUSER['uploaded']) ?></font> - <?= $txt['RATIO'] ?>: <?= $userratio ?> &nbsp;
            <?php

            if ($unread) {
                echo '<a href="account-messages.php"><b><font color=#FF0000>New PM' . ($nmessages != 1 ? 's' : '') . ' (' . $unread . ')</b></a></font>';
            } 
            } else {
				echo "<a href=account-login.php><font color=#FF0000>". $txt['LOGIN'] .
                    "</font></a> <B>:</B> <a href=account-signup.php><font color=#FF0000>". $txt['REGISTERNEW'] ."</font></a>";
			}
			?>
			</FONT></TD>
	</TR>
	<TR>
		<TD background="themes/default/images/template1_05.jpg" WIDTH=497 HEIGHT=28>
			&nbsp; <a href=index.php><?= $txt['HOME'] ?></a> • 
			<?php if ($FORUMS) { ?><a href=forums.php><?= $txt['FORUMS'] ?></a> • <?php } ?>
			<?php if ($IRCCHAT) {?><a href=irc.php><?= $txt['CHAT'] ?></a> • <?php } ?>
			<a href=browse.php><?= $txt['BROWSE_TORRENTS'] ?></a> • 
			<a href=torrents-search.php><?= $txt['SEARCH'] ?></a> • 
			<a href=torrents-upload.php><?= $txt['UPLOADT'] ?></a> • 
			<a href=faq.php><?= $txt['FAQ'] ?></a> </TD>
	</TR>
</TABLE>




<TABLE height="100%" cellSpacing=0 cellPadding=0 width="100%" border=0 align="center">
  <TBODY>
  <TR>
    <TD vAlign=top height="100%">
      <TABLE cellSpacing=5 cellPadding=0 width="100%" border=0>
        <TBODY>
        <TR>
          <TD vAlign=top width=170>
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
				<input type=text size=10 name=username style="font-family: Verdana; font-size: 8pt; font-weight: bold; border-style: solid; border-width: 1px; background-color: #C0C0C0" /></td></tr>
			<tr><td><font face=Verdana size=1><b><?= $txt['PASS'] ?>:</b></font></td><td align=left>
				<input type=password size=10 name=password style="font-family: Verdana; font-size: 8pt; font-weight: bold; border-style: solid; border-width: 1px; background-color: #C0C0C0" /></td></tr>
			<tr><td>&nbsp;</td><td align=left>
				<input type=submit value=Verify style="font-family: Verdana; font-size: 8pt; font-weight: bold; border-style: solid; border-width: 1px"></td></tr>
		</table>
		</td></form>
	</tr>
	<tr>
<td align="center">
    <a href="account-delete.php"><?= $txt['DELETE_ACCOUNT'] ?></a><br>
    <a href="account-recover.php"><?= $txt['RECOVER_ACCOUNT'] ?></a>
</td></tr>
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
    print("<a href=admin.php>". $txt['STAFFCP'] ."</a>");
} ?></tr>

</table>

<?php

end_block();

}

// invite block
if ($CURUSER)
{
	if ($INVITEONLY){
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

· <a href="index.php"><?= $txt['HOME'] ?></a><br />
&nbsp;&nbsp;· <a href="torrents-search.php"><?= $txt['SEARCH_TITLE'] ?></a><br />
&nbsp;&nbsp;· <a href="torrents-upload.php"><?= $txt['UPLOADT'] ?></a><br />
&nbsp;&nbsp;· <a href="torrents-needseed.php"><?= $txt['UNSEEDED'] ?></a><br />
&nbsp;&nbsp;· <a href="viewrequests.php"><?= $txt['REQUESTED'] ?></a><br />
&nbsp;&nbsp;· <a href="today.php"><?= $txt['TODAYS_TORRENTS'] ?></a><br /><br />
				  <CENTER><a href="rssinfo.php"><img src="images/rss2.gif" border=0 alt="XML RSS Feed"></a></CENTER>
				  <hr>
· <a href="faq.php"><?= $txt['FAQ'] ?></a><br />
· <a href="extras-stats.php"><?= $txt['TRACKER_STATISTICS'] ?></a><br />
<?php if ($FORUMS) {?>· <a href="forums.php"><?= $txt['FORUMS'] ?></a><br /><?php } ?>
<?php if ($IRCCHAT) {?>· <a href="irc.php"><?= $txt['CHAT'] ?></a><br /><?php } ?>
· <a href="formats.php"><?= $txt['FILE_FORMATS'] ?></a><br />
· <a href="videoformats.php"><?= $txt['MOVIE_FORMATS'] ?></a><br />
· <a href="staff.php"><?= $txt['STAFF'] ?></a><br />
· <a href="rules.php"><?= $txt['SITE_RULES'] ?></a><br />
· <a href="extras-users.php"><?= $txt['MEMBERS'] ?></a><br /><hr>
· <a href="visitorsnow.php"><?= $txt['ONLINE_USERS'] ?></a><br />
· <a href="visitorstoday.php"><?= $txt['VISITORS_TODAY'] ?></a><br />

<?php if(get_user_class() > UC_VIP) { ?><hr>
· <a href="admin.php"><?= $txt['STAFFCP'] ?></a><br /><?php } ?>
<br />

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
            </TD>
          <TD vAlign=top>

<!-- banner code starts here -->
<br><CENTER><?php
$content = file_get_contents(ST_ROOT_DIR . '/banners.txt');
$s_con = preg_split('/~/', $content);
$banners = rand(0,(count($s_con)-1));
echo $s_con[$banners];
?></CENTER><br>
<!-- end banner code -->

