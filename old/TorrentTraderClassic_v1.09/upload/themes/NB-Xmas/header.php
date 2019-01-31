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
<link rel="stylesheet" type="text/css" href="themes/NB-Xmas/theme.css" />

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
<!--[if lt IE 7]>
<![if gte IE 5.5]>
<script type="text/javascript" src="themes/NB-Xmas/fixpng.js"></script><style type="text/css"> 
.logo, IMG { filter:expression(fixPNG(this)); } 
.logo A { position: relative; }
</style>
<![endif]>
<![endif]-->

<script type="text/javascript" src="js/ncode_imageresizer.js"></script>
<script type="text/javascript">
<!--
NcodeImageResizer.MODE = 'newwindow';
NcodeImageResizer.MAXWIDTH = 480;
NcodeImageResizer.MAXHEIGHT = 0;

NcodeImageResizer.Msg1 = 'Ќажмите дл¤ просмотра полноразмерного изображени¤.';
NcodeImageResizer.Msg2 = 'Ќажмите дл¤ просмотра полноразмерного изображени¤.';
NcodeImageResizer.Msg3 = 'Ќажмите дл¤ просмотра полноразмерного изображени¤.';
NcodeImageResizer.Msg4 = 'Ќажмите  чтобы рассмотреть маленькое изображение.';
//-->
</script>

</head>

<BODY LEFTMARGIN="0" TOPMARGIN="0" MARGINWIDTH="0" MARGINHEIGHT="0" align="center">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="84" height="26"><img src="themes/NB-Xmas/images/NB_tb-l.jpg" width="84" height="26"></td>
    <td class="infobar" height="26" background="themes/NB-Xmas/images/NB_tb-m.jpg"><FONT COLOR=#FFFFFF>&nbsp;
			<?php if ($CURUSER) {
				print $CURUSER['username']; 
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
		$nmessages = numUserMsg();
		$unread = numUnreadUserMsg();
		//end
			?>

			&nbsp;&#8595;&nbsp;<font color=red><?php print mksize($CURUSER['downloaded']);?></font> - <b>&#8593;&nbsp;</b><font color=green><?php
            print mksize($CURUSER['uploaded']);?></font> - <?= $txt['RATIO'] ?>: <?php print $userratio; ?> &nbsp;<?php
            if ($unread) {	print("<a href=\"account.php\"><b><font color=#FF0000>New PM" . ($nmessages != 1 ? "s" : "") . " ($unread)</b></a></font>");}?>

			<?php
			}else{
				echo "<a href=account-login.php><font color=#FF0000>". $txt['LOGIN'] . "</font></a> <B>:</B> <a href=account-signup.php><font color=#FF0000>" .
                $txt['REGISTERNEW'] . "</font></a>";
			}
			?>
			</FONT></td>
    <td width="72" height="26"><img src="themes/NB-Xmas/images/NB_tb-r.jpg" width="72" height="26"></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="309" height="97"><img src="themes/NB-Xmas/images/NB_header-l.jpg" width="309" height="97"></td>
    <td height="97" align="center" background="themes/NB-Xmas/images/NB_header-bkg.jpg"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="logo" height="97">&nbsp;</td>
      </tr>
    </table></td>
    <td width="72" height="97"><img src="themes/NB-Xmas/images/NB_header-r.jpg" width="72" height="97"></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="28" class="subnav"><a href=index.php class="link2"><?= $txt['HOME'] ?></a>  • 
			<?php if ($FORUMS) {?><a href=forums.php class="link2"><?= $txt['FORUMS'] ?></a>  • <?php }?>
			<?php if ($IRCCHAT) {?><a href=irc.php class="link2"><?= $txt['CHAT'] ?></a>  • <?php }?>
			<a href=browse.php class="link2"><?= $txt['BROWSE_TORRENTS'] ?></a>  • 
			<a href=torrents-search.php class="link2"><?= $txt['SEARCH'] ?></a>  • 
			<a href=torrents-upload.php class="link2"><?= $txt['UPLOADT'] ?></a>  • 
			<a href=faq.php class="link2"><?= $txt['FAQ'] ?></a>  • <a href=last_comments.php class="link2">Комментарии</a>  • <a href=top.php class="link2">Топ</a> </td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="9"><img src="themes/NB-Xmas/images/NB_nav-shadow.png" width="100%" height="9"></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<TBODY>
  <tr>
    <td><table width="100%" border="0" cellspacing="10" cellpadding="0">
      <tr>
        <td width="180" valign="top">
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

    $styles = Helper::getStylesheets();
    $langs = Helper::getLanguages();

begin_block("$CURUSER[username]");
?>
<form method="post" action="take-theme.php">
<div>
  <div align="center" class="avat_m">
                <?php
                $avatar = $CURUSER["avatar"];
                if (!$avatar)
                {
                $avatar = "images/default_avatar.gif";
                }
                print ('<img src="' . $avatar . '" alt="' . $CURUSER['username'] . '" name="'
                . $CURUSER['username'] . '" title="' . $CURUSER['username'] . '" border="0" />');
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

<div align="left"> • <a href="index.php">√лавна¤</a><br>
 • <a href="torrents-search.php">ѕоиск торрентов</a><br>
 • <a href="torrents-upload.php">«акачать</a><br>
 • <a href="torrents-needseed.php">“орренты без раздающих</a><br>
 • <a href="viewrequests.php">«апрос торрентов</a><br>
 • <a href="today.php">“орренты за сегодн¤</a><br>
 • <a href="last_comments.php"> омментарии</a><br>
 • <a href="top.php">ѕопул¤рные торренты</a><br>
</div>
<br>
<CENTER><a href="rssinfo.php"><img src="images/rss2.gif" border=0 alt="XML RSS Feed"></a></CENTER>
<hr>
 • <a href="faq.php"><?= $txt['FAQ'] ?></a><br>
 • <a href="extras-stats.php"><?= $txt['TRACKER_STATISTICS'] ?></a><br>
 • <a href="forums.php"><?= $txt['FORUMS'] ?></a><br>
<?php if ($IRCCHAT) {?> • <a href="irc.php"><?= $txt['CHAT'] ?></a><br><?php }?>
 • <a href="formats.php">‘орматы файлов</a><br>
 • <a href="videoformats.php">¬идеоформаты</a><br>
 • <a href="staff.php">–уководство</a><br>
 • <a href="rules.php">ѕравила</a><br>
 • <a href="extras-users.php">—писок участников</a><br><hr>
 • <a href="visitorsnow.php"> то онлайн</a><br>
 • <a href="visitorstoday.php">ѕосетители за сегодн¤</a><br>

<?php if (get_user_class() > UC_VIP) {?><hr>
 • <a href="admin.php"><?= $txt['STAFFCP'] ?></a><br><?php }?>
<br>

 <?php
end_block();

/*
begin_block($txt['ONLINE_USERS']);
$res = DB::query("
    SELECT id, username, class, donated, warned
    FROM users
    WHERE UNIX_TIMESTAMP(" . get_dt_num() . ") - UNIX_TIMESTAMP(last_access) < 900
    ORDER BY username
    LIMIT 1000");

while ($arr = $res->fetch())
{
	if ($activepeople)
		$activepeople .= ", ";
	switch ($arr["class"])
	{
	case UC_ADMINISTRATOR:
	  $arr["username"] = "<font color=#FF0000>" . $arr["username"] . "</font>";
	  break;
	case UC_MODERATOR:
	  $arr["username"] = "<font color=#A83838>" . $arr["username"] . "</font>";
	  break;
	case UC_VIP:
	  $arr["username"] = "<font class=vipuser title=VIP>" . $arr["username"] . "</font>";
	  break;
	 case UC_UPLOADER:
	  $arr["username"] = "<font color=#C0C0C0>" . $arr["username"] . "</font>";
	  break;
	   case UC_JMODERATOR:
	  $arr["username"] = "<font color=#000000>" . $arr["username"] . "</font>";
	  break;
	}

	$donator = $arr["donated"] > 0;
	if ($CURUSER) {
		$activepeople .= "<a href=account-details.php?id=" . $arr["id"] . ">" . $arr["username"] . "</a></a>";
	} else {
		$activepeople .= "<a href=account-details.php?id=" . $arr["id"] . ">" . $arr["username"] . "</a></a>";
	}
	if ($donator) {
		$activepeople .= "<img src=\"images/star.gif\" alt=\"Donator\">";
	}
	$warned = $arr["warned"] == "yes";
	if ($warned) {
		$activepeople .= "<img src=\"images/warned.gif\" alt=\"Warning\">";
	}
	$usersactive++;
}
//end visited today

echo "<div align='left'>" . $activepeople . "</div>";


//Gosti

$file = "".$site_config["cache_dir"]."/cache_usersonlineblock.txt";
$expire = 600; // time in seconds
$guests = number_format(getguests());
$members = number_format(get_row_count("users", "WHERE UNIX_TIMESTAMP('" . get_date_time() . "') - UNIX_TIMESTAMP(users.last_access) < 900"));
echo "<hr>ѕользователей  - <B> " . $members . "</b> <br> √остей - <B> " . $guests . "</b>  <br><hr>";
$a = @mysql_fetch_assoc(@mysql_query("SELECT id,username FROM users WHERE status='confirmed' ORDER BY id DESC LIMIT 1"));
if ($CURUSER)
$latestuser = "<a href=account-details.php?id=" . $a["id"] . ">" . $a["username"] . "</a>";
else
$latestuser = "<b>$a[username]</b>";
echo "ѕриветствуем нового пользовател¤: $latestuser<br>";


//ONLINERECORD

$res = mysql_query("SELECT COUNT(*) FROM users WHERE UNIX_TIMESTAMP(" . get_dt_num() . ") - UNIX_TIMESTAMP(last_access) < 900");
$arr4 = mysql_fetch_row($res);
$totalnow = $arr4[0];

//record
$rec = @mysql_fetch_array(@mysql_query("select * from onlinerec"));
if ($rec[users] < $totalnow) mysql_query("update onlinerec set users=$totalnow, date='".get_date_time()."' where users=$rec[users]");

echo "<hr><font class=stats>–екорд online <b>" . $rec[users] . "</b>,</font>";
echo "<br><font class=stats> это было " . $rec[date] . "</font>";

end_block();
*/

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
