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
<link rel="stylesheet" type="text/css" href="themes/default/theme.css" />

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

<BODY LEFTMARGIN="0" TOPMARGIN="0" MARGINWIDTH="0" MARGINHEIGHT="0" align="center">
<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
<!-- THIS IS THE TOP LOGO AREA 3 CELLS -->
		<TD WIDTH=497><a href="index.php"><IMG SRC="themes/default/images/template1_01.jpg" WIDTH=497 HEIGHT=80 border=0 ALT="<? echo $SITENAME  ?> logo" ></a></TD>
		<TD ROWSPAN=3 WIDTH=141><IMG SRC="themes/default/images/template1_02.jpg" WIDTH=141 HEIGHT=132 ALT=""></TD>
		<TD ROWSPAN=3 WIDTH=100%><IMG SRC="themes/default/images/template1_03.jpg" WIDTH=100% HEIGHT=132 ALT=""></TD>
<!-- END TOP LOGO AREA -->	
	</TR>
	<TR>
<!-- TOP NAV MENU AND USER RATIO AREA -->
		<TD background="themes/default/images/template1_04.jpg" WIDTH=497 HEIGHT=24>
			<FONT COLOR=#FFFFFF>&nbsp;
			<? if ($CURUSER) {
				print $CURUSER[username]; 
				echo "(<a href=account-logout.php><font color=#ffffff><b>Logout</b></font></a>)";

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

			&nbsp;&#8595;&nbsp;<font color=red><? print mksize($CURUSER[downloaded]);?></font> - <b>&#8593;&nbsp;</b><font color=green><? print mksize($CURUSER[uploaded]);?></font> - <? print("" . RATIO . "\n"); ?>: <? print $userratio; ?> &nbsp;<?if ($unread) {	print("<a href=\"account.php\"><b><font color=#FF0000>New PM" . ($messages != 1 ? "s" : "") . " ($unread)</b></a></font>");}?>

			<?
			}else{
				echo "<a href=account-login.php><font color=#FF0000>". LOGIN . "</font></a> <B>:</B> <a href=account-signup.php><font color=#FF0000>" . REGISTERNEW . "</font></a>";
			}
			?>
			</FONT></TD>
	</TR>
	<TR>
		<TD background="themes/default/images/template1_05.jpg" WIDTH=497 HEIGHT=28>
			&nbsp; <a href=index.php><? print("" . HOME . "\n"); ?></a> � 
			<?if ($FORUMS) {?><a href=forums.php><? print("" . FORUMS . "\n"); ?></a> � <?}?>
			<?if ($IRCCHAT) {?><a href=irc.php><? print("" . CHAT . "\n"); ?></a> � <?}?>
			<a href=browse.php><? print("" . BROWSE_TORRENTS . "\n"); ?></a> � 
			<a href=torrents-search.php><? print("" . SEARCH . "\n"); ?></a> � 
			<a href=torrents-upload.php><? print("" . UPLOADT . "\n"); ?></a> � 
			<a href=faq.php><? print("" . FAQ . "\n"); ?></a> </TD>
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
}

begin_block("$CURUSER[username]");
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><form method="post" action="take-theme.php"><td>
<table border=0 cellspacing=0 cellpadding="6" width=100%>
<tr><td align="center"><B><? print("" . THEME . ""); ?> </B>
	<select name=stylesheet style="font-family: Verdana; font-size: 8pt; color: #000000; border: 1px solid #808080; background-color: #C0C0C0" size="1"><?=$stylesheets?></select></td></tr>
<tr><td align="center"><B><? print("" . LANG . ""); ?> </B>
	<select name=language style="font-family: Verdana; font-size: 8pt; color: #000000; border: 1px solid #808080; background-color: #C0C0C0" size="1"><?=$languages?></select></td></tr>
<tr><td align="center">
	<input type="submit" value="<? print("" . APPLY . ""); ?>" style="font-family: Verdana; font-size: 8pt; color: #000000; border: 1px solid #808080; background-color: #C0C0C0"></td></tr>
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

� <a href="index.php"><? print("" . HOME . "\n"); ?></a><br />
&nbsp;&nbsp;� <a href="torrents-search.php"><? print("" . SEARCH_TITLE . "\n"); ?></a><br />
&nbsp;&nbsp;� <a href="torrents-upload.php"><? print("" . UPLOADT . "\n"); ?></a><br />
&nbsp;&nbsp;� <a href="torrents-needseed.php"><? print("" . UNSEEDED . "\n"); ?></a><br />
&nbsp;&nbsp;� <a href="viewrequests.php"><? print("" . REQUESTED . "\n"); ?></a><br />
&nbsp;&nbsp;� <a href="today.php"><? print("" . TODAYS_TORRENTS . "\n"); ?></a><br /><br />
				  <CENTER><a href="rssinfo.php"><img src="images/rss2.gif" border=0 alt="XML RSS Feed"></a></CENTER>
				  <hr>
� <a href="faq.php"><? print("" . FAQ . "\n"); ?></a><br />
� <a href="extras-stats.php"><? print("" . TRACKER_STATISTICS . "\n"); ?></a><br />
<?if ($FORUMS) {?>� <a href="forums.php"><? print("" . FORUMS . "\n"); ?></a><br /><?}?>
<?if ($IRCCHAT) {?>� <a href="irc.php"><? print("" . CHAT . "\n"); ?></a><br /><?}?>
� <a href="formats.php"><? print("" . FILE_FORMATS . "\n"); ?></a><br />
� <a href="videoformats.php"><? print("" . MOVIE_FORMATS . "\n"); ?></a><br />
� <a href="staff.php"><? print("" . STAFF . "\n"); ?></a><br />
� <a href="rules.php"><? print("" . SITE_RULES . "\n"); ?></a><br />
� <a href="extras-users.php"><? print("" . MEMBERS . "\n"); ?></a><br /><hr>
� <a href="visitorsnow.php"><? print("" . ONLINE_USERS . "\n"); ?></a><br />
� <a href="visitorstoday.php"><? print("" . VISITORS_TODAY . "\n"); ?></a><br />

<?if(get_user_class() > UC_VIP) {?><hr>
� <a href="admin.php"><? print("" . STAFFCP . "\n"); ?></a><br /><?}?>
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
		   <br><CENTER><?
$content = join ('', file ('banners.txt'));
$s_con = split("~",$content);

$banners = rand(0,(count($s_con)-1));
echo $s_con[$banners];
?></CENTER><br>
<!-- end banner code -->