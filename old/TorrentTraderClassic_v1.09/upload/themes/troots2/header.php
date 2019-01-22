<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<script language="JavaScript">
<!--
function Smilies(Smilie)
{
document.Form.body.value+=Smilie+" ";
document.Form.body.focus();
}
//-->
</script>
<title><?= $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="imagetoolbar" content="no" />
<link rel="stylesheet" type="text/css" href="themes/troots2/css/style.css" />

<script>

var myimages=new Array()
function preloadimages(){
for (i=0;i<preloadimages.arguments.length;i++){
myimages[i]=new Image()
myimages[i].src=preloadimages.arguments[i]
}
}

preloadimages("images/space.gif")

</script>

<script>

var g_nExpando=0;
// To make the cross clickable in every browser
function putItemInState(n,bState)
{
   var oItem,oGif;
      oItem=document.getElementById("descr"+n);
   oGif=document.getElementById("expandoGif"+n);
   
   if (bState=='toggle')
     bState=(oItem.style.display=='block');

   if(bState)
   {
       bState=(oItem.style.display='none');
       bState=(oGif.src='images/cross.gif');
   }
   else
   {
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

</script>

<script>

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
                  style="PADDING-RIGHT: 2px; PADDING-LEFT: 5px"><? if ($CURUSER) { print"$CURUSER[username]"; }else{?>Login <?}?></DIV></TD>
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
					<a href=index.php><span style="color: #FFFFFF"><? print("" . HOME . "\n"); ?></span></a></DIV></TD>
                <TD class=mainnavigation style="VERTICAL-ALIGN: middle" width=116 background="themes/troots2/images/tab_blue1.jpg" bgColor=#3f89c3 height=34>
				<a href=torrents-search.php><? print("" . SEARCH . "\n"); ?></a></TD>
                <TD class=mainnavigation style="VERTICAL-ALIGN: middle" 
                width=115 
                background="themes/troots2/images/tab_blue2.jpg" 
                bgColor=#3f89c3 height=34>
				<a href=torrents-upload.php><? print("" . UPLOADT . "\n"); ?></a></TD>
                <TD class=mainnavigation style="VERTICAL-ALIGN: middle" 
                width=115 
                background="themes/troots2/images/tab_blue2.jpg" 
                bgColor=#3f89c3 height=34>
				<a href=faq.php><? print("" . FAQ . "\n"); ?></a></TD></TR></TBODY></TABLE></TD>
          <TD class=topright style="VERTICAL-ALIGN: middle" width=310 
            height=34><TABLE cellSpacing=0 cellPadding=0 width="100%" 
              border=0><TBODY>
              <TR>
			<? if ($CURUSER) {?>
<TD style="VERTICAL-ALIGN: middle; TEXT-ALIGN: center">
<?
		// ger user ratio
		if ($CURUSER["downloaded"] > 0){
				$userratio = number_format($CURUSER["uploaded"] / $CURUSER["downloaded"], 2);
		}else{
				if ($CURUSER["uploaded"] > 0)
					$userratio = "Inf.";
				else
					$userratio = "NA";
		}
		//end

		// get unread messages
		$res12 = mysql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " and unread='yes'") or print(mysql_error());
		$arr12 = mysql_fetch_row($res12);
		$unreadmail = $arr12[0];
		//end
			?>

			&nbsp;&#8595;&nbsp;<font color=red><? print mksize($CURUSER[downloaded]);?></font> - <b>&#8593;&nbsp;</b><font color=green><? print mksize($CURUSER[uploaded]);?></font> - <? print("" . RATIO . "\n"); ?>: <? print $userratio; ?> &nbsp;<?if ($unread) {	print("<a href=\"account.php\"><b><font color=#FF0000>New PM" . ($messages != 1 ? "s" : "") . " ($unread)</b></a></font></TD></TR>");}?>

			<?
			}else{
				echo "<div align=center><b><a style='color: red' href=account-login.php>". LOGIN . "</a> : <a style='color: red' href=account-signup.php>" . REGISTERNEW . "</a></B></div></TD></TR>";
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
<?if ($FORUMS) {?><A style="TEXT-DECORATION: none" href=forums.php><? print("" . FORUMS . "\n"); ?></A><IMG hspace=11 src="themes/troots2/images/div_red.gif" align=absMiddle><?}?>
<?if ($IRCCHAT) {?><A style="TEXT-DECORATION: none" href=irc.php><? print("" . CHAT . "\n"); ?></A><IMG hspace=11 src="themes/troots2/images/div_red.gif" align=absMiddle><?}?>
<A style="TEXT-DECORATION: none" href="torrents-needseed.php"><? print("" . UNSEEDED . "\n"); ?></A><IMG hspace=11 src="themes/troots2/images/div_red.gif" align=absMiddle>
<A style="TEXT-DECORATION: none" href="viewrequests.php"><? print("" . REQUESTED . "\n"); ?></A><IMG hspace=11 src="themes/troots2/images/div_red.gif" align=absMiddle>
<A style="TEXT-DECORATION: none" href="today.php"><? print("" . TODAYS_TORRENTS . "\n"); ?></A><IMG hspace=11 src="themes/troots2/images/div_red.gif" align=absMiddle>
<A style="TEXT-DECORATION: none" href="formats.php"><? print("" . FILE_FORMATS . "\n"); ?></A><IMG hspace=11 src="themes/troots2/images/div_red.gif" align=absMiddle>
<A style="TEXT-DECORATION: none" href="videoformats.php"><? print("" . MOVIE_FORMATS . "\n"); ?></A><IMG hspace=11 src="themes/troots2/images/div_red.gif" align=absMiddle>
<A style="TEXT-DECORATION: none" href="rules.php"><? print("" . SITE_RULES . "\n"); ?></A>
</NOBR>
</TD>

</TR></TBODY></TABLE></TD></TR>
        <TR>
          <TD class=toplinks background="themes/troots2/images/tile_h_1.gif" bgColor=#3f89c3 colSpan=2>
            <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
              <TBODY>
              <TR>
<TD class=bluenav style="BACKGROUND-POSITION: right top; PADDING-LEFT: 13px; FONT-SIZE: 11px; VERTICAL-ALIGN: middle; COLOR: #ffffff; BACKGROUND-REPEAT: no-repeat" width="100%" background="themes/troots2/images/blue_block.gif" height=34>
<? if (!$CURUSER) { ?>
<form method=post action=account-login.php>
<input onfocus="this.value=''" type=text size=10 value="User Name" name=username style="font-family: Verdana; font-size: 8pt; font-weight: bold; border-style: solid; border-width: 0px">
<input onfocus="this.value=''" type=password size=10 value="ibfrules" name=password style="font-family: Verdana; font-size: 8pt; font-weight: bold; border-style: solid; border-width: 0px">
<input type=submit value=Verify style="font-family: Verdana; font-size: 8pt; border-style: solid; border-width: 0px">
</TD></form>
<?}else{
$ss_r = mysql_query("SELECT * from stylesheets") or die;
$ss_sa = array();
while ($ss_a = mysql_fetch_array($ss_r))
{
  $ss_id = $ss_a["id"];
  $ss_name = $ss_a["name"];
  $ss_sa[$ss_name] = $ss_id;
}
ksort($ss_sa);
reset($ss_sa);
while (list($ss_name, $ss_id) = each($ss_sa))
{
  if ($ss_id == $CURUSER["stylesheet"]) $ss = " selected"; else $ss = "";
  $stylesheets .= "<option value=$ss_id$ss>$ss_name</option>\n";
}

$lang_r = mysql_query("SELECT * from languages") or die;
$lang_sa = array();
while ($lang_a = mysql_fetch_array($lang_r))
{
  $lang_id = $lang_a["id"];
  $lang_name = $lang_a["name"];
  $lang_sa[$lang_name] = $lang_id;
}
ksort($lang_sa);
reset($lang_sa);
while (list($lang_name, $lang_id) = each($lang_sa))
{
  if ($lang_id == $CURUSER["language"]) $lang = " selected"; else $lang = "";
  $languages .= "<option value=$lang_id$lang>$lang_name</option>\n";
}?><form method="post" action="take-theme.php">
<font size=1><? print("" . THEME . ""); ?>:</font> <select name=stylesheet style="font-family: Verdana; font-size: 8pt; font-weight: bold; border-style: solid; border-width: 0px"><?=$stylesheets?></select>
<font size=1><? print("" . LANG . ""); ?>:</font> <select name=language style="font-family: Verdana; font-size: 8pt; font-weight: bold; border-style: solid; border-width: 0px"><?=$languages?></select>
<input type="submit" value="<? print("" . APPLY . ""); ?>" style="font-family: Verdana; font-size: 8pt; font-weight: bold; border-style: solid; border-width: 0px"><IMG hspace=11 src="themes/troots2/images/div_red.gif" align=absMiddle>
<A style="TEXT-DECORATION: none" href="account.php"><? print("" . ACCOUNT . "\n"); ?></a><IMG hspace=11 src="themes/troots2/images/div_red.gif" align=absMiddle>
<?if(get_user_class() > UC_VIP) {?><A style="TEXT-DECORATION: none" href="admin.php"><? print("" . STAFFCP . "\n"); ?></a><IMG hspace=11 src="themes/troots2/images/div_red.gif" align=absMiddle><?}?>
<A style="TEXT-DECORATION: none" href=account-logout.php>Logout</a>
</TD>
<?}?>
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
<?
if (!$CURUSER)

{

begin_block("" . Login . "");
?>
<table border=0 width=100% cellspacing=0 cellpadding=0>
	<tr>
		<form method=post action=account-login.php><td>
		<div align=center>
		<table border=0 cellpadding=5">
			<tr><td>
				<p align=center><font face=Verdana size=1><b><? print("" . USER . "\n"); ?>:</b></font></td><td align=left>
				<input type=text size=10 name=username style="font-family: Verdana; font-size: 8pt; font-weight: bold; border-style: solid; border-width: 1px; background-color: #C0C0C0" /></td></tr>
			<tr><td><font face=Verdana size=1><b><? print("" . PASS . "\n"); ?>:</b></font></td><td align=left>
				<input type=password size=10 name=password style="font-family: Verdana; font-size: 8pt; font-weight: bold; border-style: solid; border-width: 1px; background-color: #C0C0C0" /></td></tr>
			<tr><td>&nbsp;</td><td align=left>
				<input type=submit value=Verify style="font-family: Verdana; font-size: 8pt; font-weight: bold; border-style: solid; border-width: 1px"></td></tr>
		</table>
		</td></form>
	</tr>
	<tr>
<td align="center"><a href="account-delete.php"><?echo "" . DELETE_ACCOUNT . "";?></a><br><a href="account-recover.php"><?echo "" . RECOVER_ACCOUNT . "";?></a></td> </tr>
	</table>
<?
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
<tr><td align="center"><? print ("<img src=\"$avatar\" border=\"0\" width=\"80\" height=\"80\">");?></td></tr>
<tr><td align="center">
		<? print("" . DOWNLOADED . "\n"); ?>: <font color=red><? print mksize($CURUSER[downloaded]);?></font><br>
		<? print("" . UPLOADED . "\n"); ?>: <font color=green><? print mksize($CURUSER[uploaded]);?></font><br>
		<? print("" . RATIO . "\n"); ?>: <? print $userratio; ?><br>
</td></tr>
<tr><td align="center"></td></tr>
</table></form></td></tr>
<tr>
<td align="center"><a href="account.php"><? print("" . ACCOUNT . "\n"); ?></a> <br> <? if (get_user_class() > UC_VIP) {
print("<a href=admin.php>" . STAFFCP . "</a>");}?></font></tr>

</table>
<?
end_block();

}

// invite block
if ($CURUSER)
{
	if ($INVITEONLY){
		$invites = $CURUSER["invites"];
		begin_block("" . INVITES . "");
		?>
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr><td align="center"><? print("" . YOUHAVE . "\n"); ?> <?=$invites?> <? print("" . INVITES . "\n"); ?><br></td></tr>
		<?if ($invites > 0 ){?>
		<tr><td align="center"><a href=invite.php><? print("" . SENDANINVITE . "\n"); ?></a><br></td></tr>
		<?}?>
		</table>
		<?
		end_block();
	}
}
//end invite block

begin_block("" . NAVIGATION . "");
?>

· <a href="index.php"><? print("" . HOME . "\n"); ?></a><br />
&nbsp;&nbsp;· <a href="torrents-search.php"><? print("" . SEARCH_TITLE . "\n"); ?></a><br />
&nbsp;&nbsp;· <a href="torrents-upload.php"><? print("" . UPLOADT . "\n"); ?></a><br />
&nbsp;&nbsp;· <a href="torrents-needseed.php"><? print("" . UNSEEDED . "\n"); ?></a><br />
&nbsp;&nbsp;· <a href="viewrequests.php"><? print("" . REQUESTED . "\n"); ?></a><br />
&nbsp;&nbsp;· <a href="today.php"><? print("" . TODAYS_TORRENTS . "\n"); ?></a><br /><br />
				  <CENTER><a href="rssinfo.php"><img src="images/rss2.gif" border=0 alt="XML RSS Feed"></a></CENTER>
				  <hr>
· <a href="faq.php"><? print("" . FAQ . "\n"); ?></a><br />
· <a href="extras-stats.php"><? print("" . TRACKER_STATISTICS . "\n"); ?></a><br />
<?if ($FORUMS) {?>· <a href="forums.php"><? print("" . FORUMS . "\n"); ?></a><br /><?}?>
<?if ($IRCCHAT) {?>· <a href="irc.php"><? print("" . CHAT . "\n"); ?></a><br /><?}?>
· <a href="formats.php"><? print("" . FILE_FORMATS . "\n"); ?></a><br />
· <a href="videoformats.php"><? print("" . MOVIE_FORMATS . "\n"); ?></a><br />
· <a href="staff.php"><? print("" . STAFF . "\n"); ?></a><br />
· <a href="rules.php"><? print("" . SITE_RULES . "\n"); ?></a><br />
· <a href="extras-users.php"><? print("" . MEMBERS . "\n"); ?></a><br /><hr>
· <a href="visitorsnow.php"><? print("" . ONLINE_USERS . "\n"); ?></a><br />
· <a href="visitorstoday.php"><? print("" . VISITORS_TODAY . "\n"); ?></a><br />

<?if(get_user_class() > UC_VIP) {?><hr>
· <a href="admin.php"><? print("" . STAFFCP . "\n"); ?></a><br /><?}?>
<br />

 <?
end_block();


if ($DONATEON)
{
begin_block("" . DONATIONS . "", center);
$res9 = mysql_query("SELECT * FROM site_settings ") or sqlerr(__FILE__, __LINE__);
$arr9 = mysql_fetch_assoc($res9);
$mothlydonated = $arr9['donations'];
$requireddonations = $arr9['requireddonations'];
echo "<br><b>" . TARGET . ": </b><font color=\"red\">$" . $requireddonations . "</font><br><b>" . DONATIONS . ": </b><font color=\"green\">$" . $mothlydonated . "</font></center><br>";
print "<div align=left><B><font color=#FF6600>&#187;</font></B> <a href=\"donate.php\">" . DONATE . "</a><br>";
end_block();
}

//start side banner
echo "<br><CENTER>";
$contents = join ('', file ('sponsors.txt'));
$s_cons = split("~",$contents);
$bannerss = rand(0,(count($s_cons)-1));
echo $s_cons[$bannerss];
echo "</CENTER><br>";
//end side banner


?>
                </TD>
                <TD vAlign=top>
<!-- banner code starts here -->
<br><CENTER>
<?
$content = join ('', file ('banners.txt'));
$s_con = split("~",$content);
$banners = rand(0,(count($s_con)-1));
echo $s_con[$banners];
?>
</CENTER><br>
<!-- end banner code -->