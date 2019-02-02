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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
          <TD vAlign=top width=180>

<?php require_once TT_DIR . '/columns/left-column.php'; ?>

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

