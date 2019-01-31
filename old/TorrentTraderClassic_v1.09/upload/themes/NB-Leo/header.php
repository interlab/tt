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
<title><?= $title ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="imagetoolbar" content="no" />
<link rel="stylesheet" type="text/css" href="themes/NB-Leo/theme.css" />

<script>

var myimages = new Array();
function preloadimages() {
    for (i = 0; i < preloadimages.arguments.length; i++) {
        myimages[i] = new Image();
        myimages[i].src=preloadimages.arguments[i]
    }
}

preloadimages("images/space.gif");

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

<?php
global $st;

echo isset($st['js_files']) ? $st['js_files'] : '';
?>

</head>

<BODY LEFTMARGIN="0" TOPMARGIN="0" MARGINWIDTH="0" MARGINHEIGHT="0" align="center">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="379" height="89" background="themes/NB-Leo/images/leo_01.jpg"><img src="themes/NB-Leo/images/leo_logo.jpg" width="379" height="89" /></td>
            <td align="right" valign="bottom" background="themes/NB-Leo/images/leo_01.jpg" class="login">
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
&nbsp;<b>&#8595;</b>&nbsp;<font color=red><?= mksize($CURUSER['downloaded']);?></font> -
 <b>&#8593;&nbsp;</b><font color=#03db03><?= mksize($CURUSER['uploaded']);?></font> - <?= $txt['RATIO'] ?>: <?= $userratio ?> &nbsp;
<?php
    if ($unread) {
        print("<a href=\"account.php\"><b><font color=#FF0000>New PM" . ($nmessages != 1 ? "s" : "") . " ($unread)</b></a></font>");
    }

} else {
    echo "<a href=account-login.php><font color=#FF0000>". $txt['LOGIN'] . "</font></a> <B>:</B> <a href=account-signup.php><font color=#FF0000>" .
        $txt['REGISTERNEW'] . "</font></a>";
}
?>
      </td>
          </tr>
        </table>
          <table width="100%" border="0" cellpadding="0" cellspacing="0" background="themes/NB-Leo/images/leo_02.jpg">
  <tr>
    <td width="1" height="35"><img src="themes/NB-Leo/images/blank.gif" width="1" height="35" /></td>
    <td align="center" valign="middle" class="subnav"><a href=index.php><?= $txt['HOME'] ?></a><span class="subnav">|</span>
      <?php if ($FORUMS) { ?>
        <a href=forums.php><?= $txt['FORUMS'] ?></a><span class="subnav">|</span>
        <?php } ?>
        <?php if ($IRCCHAT) { ?>
        <a href=irc.php><?= $txt['CHAT'] ?></a><span class="subnav">|</span>
        <?php } ?>
        <a href=browse.php><?= $txt['BROWSE_TORRENTS'] ?></a><span class="subnav">|
        </span><a href=torrents-search.php><?= $txt['SEARCH'] ?></a><span class="subnav">|
        </span><a href=torrents-upload.php><?= $txt['UPLOADT'] ?></a><span class="subnav">|
        </span><a href=faq.php><?= $txt['FAQ'] ?></a></td>
  </tr>
</table>
</td>
      </tr>
    </table><table width="100%" border="0" cellspacing="0" cellpadding="0">
	<TBODY>
  <tr>
    <td><table class="mbody" width="100%" border="0" cellspacing="0" cellpadding="6">
	<TBODY>
      <tr>
	<TD vAlign="top" width="180">
	<br>
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
<td align="center"><a href="account-delete.php"><?= $txt['DELETE_ACCOUNT'] ?></a>
<br><a href="account-recover.php"><?= $txt['RECOVER_ACCOUNT'] ?></a></td> </tr>
	</table>
<?php
end_block();

} else {
    $styles = Helper::getStylesheets();
    $langs = Helper::getLanguages();

begin_block("$CURUSER[username]");
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
		begin_block("" . $txt['INVITES'] . "");
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

begin_block("" . $txt['NAVIGATION'] . "");
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
    echo "<br><b>". $txt['TARGET'] .": </b><font color=\"red\">$" . $row['requireddonations'] . "</font><br><b>".
        $txt['DONATIONS'] . ": </b><font color=\"green\">$" . $row['donations'] . "</font></center><br>
        <div align=left><B><font color=#FF6600>&#187;</font></B> <a href=\"donate.php\">". $txt['DONATE'] ."</a><br>";
    end_block();
}

/*
//Here we decide if the block is on or off
if ($NEWSON)
{
begin_block("". SITENEWS . "", center);
//-----------------------------News------------------------//
$query = 'SELECT max_display, scrolling, titles, subc, subs, comment, sspeed FROM news_options';
$nopt = mysql_query($query) or die(mysql_error());
while ($arow = mysql_fetch_array($nopt)){
if ($arow['scrolling'] == 'on'){
     //  begin_frame("SCROLLING NEWS");
       ?>
       <script language="JavaScript1.2">

//  Distributed by http://www.hypergurl.com

var scrollerwidth="150px"

var scrollerheight="180px"

var scrollerspeed=<?='' . $arow['sspeed'] . '';?>

var scrollercontent='<div align="center"><? $query = 'SELECT id, title, user, date, text FROM news ORDER BY id DESC LIMIT ' . $arow['max_display'] . '';
 $resu = mysql_query($query) or die(mysql_error()); 
 while ($row = mysql_fetch_array($resu)){ echo'<font face="Arial" size="' . $arow['titles'] . '"><a href="./show-archived.php?id=' . $row['id'] . '">'
 . $row['title'] . '</a><br></font><font face="Arial" color="' . $arow['subc'] . '" size="' . $arow['subs'] . '"><I>posted by ' . $row['user'] . ' On '
 . $row['date'] . '</i><br><br>'; } ?></font></div>'

var pauseit=1

scrollerspeed=(document.all)? scrollerspeed : Math.max(1, scrollerspeed-1) //slow speed down by 1 for NS
var copyspeed=scrollerspeed
var iedom=document.all||document.getElementById
var actualheight=''
var cross_scroller, ns_scroller
var pausespeed=(pauseit==0)? copyspeed: 0

function populate(){
if (iedom){
cross_scroller=document.getElementById? document.getElementById("iescroller") : document.all.iescroller
cross_scroller.style.top=parseInt(scrollerheight)+8+"px"
cross_scroller.innerHTML=scrollercontent
actualheight=cross_scroller.offsetHeight
}
else if (document.layers){
ns_scroller=document.ns_scroller.document.ns_scroller2
ns_scroller.top=parseInt(scrollerheight)+8
ns_scroller.document.write(scrollercontent)
ns_scroller.document.close()
actualheight=ns_scroller.document.height
}
lefttime=setInterval("scrollscroller()",20)
}
window.onload=populate

function scrollscroller(){

if (iedom){
if (parseInt(cross_scroller.style.top)>(actualheight*(-1)+8))
cross_scroller.style.top=parseInt(cross_scroller.style.top)-copyspeed+"px"
else
cross_scroller.style.top=parseInt(scrollerheight)+8+"px"
}
else if (document.layers){
if (ns_scroller.top>(actualheight*(-1)+8))
ns_scroller.top-=copyspeed
else
ns_scroller.top=parseInt(scrollerheight)+8
}
}

if (iedom||document.layers){
with (document){
if (iedom){
write('<div style="position:relative;width:'+scrollerwidth+';height:'+scrollerheight+';overflow:hidden" onMouseover="copyspeed=pausespeed" onMouseout="copyspeed=scrollerspeed">')
write('<div id="iescroller" style="position:absolute;left:0px;top:0px;width:100%;">')
write('</div></div>')
}
else if (document.layers){
write('<ilayer width='+scrollerwidth+' height='+scrollerheight+' name="ns_scroller">')
write('<layer name="ns_scroller2" width='+scrollerwidth+' height='+scrollerheight+' left=0 top=0 onMouseover="copyspeed=pausespeed" onMouseout="copyspeed=scrollerspeed"></layer>')
write('</ilayer>')
}
}
}

</script>
<?php
    //   end_frame();
} else {
       begin_frame("NEWS ITEMS");
$query = 'SELECT id, title, user, date, text, comments FROM news ORDER BY id DESC LIMIT ' . $arow['max_display'] . '';
$resu = mysql_query($query) or die(mysql_error());
while ($row = mysql_fetch_array($resu)){

begin_frame("" . $row['title'] . "");
print("" . $row['text'] . " <br><br><I>Posted By " . $row['user'] . "</i> On " . $row['date'] . "\n");
if ($arow['comment'] == 'on'){
       echo'<br><a class=menu_blink href="./show-archived.php?id=' . $row['id'] . '">Comment on this article. (' . $row['comments'] . ') Replys so far';
       }
       //}
end_frame();
}

end_frame();
}
}
echo '<a class=menu_blink href=news-archive.php>View Archive</a>';
end_block();
}
//---------------------End News-----------------------------//
*/

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
<TD align="center" vAlign="top">

<!-- banner code starts here -->
<br><CENTER><?php
$content = file_get_contents(ST_ROOT_DIR . '/banners.txt');
$s_con = preg_split('/~/', $content);
$banners = rand(0,(count($s_con)-1));
echo $s_con[$banners];
?></CENTER><br>
<!-- end banner code -->
