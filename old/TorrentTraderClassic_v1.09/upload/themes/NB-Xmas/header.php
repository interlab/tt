<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?= $title ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="imagetoolbar" content="no" />
<link rel="stylesheet" type="text/css" href="themes/NB-Xmas/theme.css" />
<script src="<?= TT_JS_URL ?>/theme.js"></script>

<script type="text/javascript" src="js/ncode_imageresizer.js"></script>
<script type="text/javascript">
<!--
NcodeImageResizer.MODE = 'newwindow';
NcodeImageResizer.MAXWIDTH = 480;
NcodeImageResizer.MAXHEIGHT = 0;

NcodeImageResizer.Msg1 = 'Нажмите для просмотра полноразмерного изображения.';
NcodeImageResizer.Msg2 = 'Нажмите для просмотра полноразмерного изображения.';
NcodeImageResizer.Msg3 = 'Нажмите для просмотра полноразмерного изображения.';
NcodeImageResizer.Msg4 = 'Нажмите, чтобы рассмотреть маленькое изображение.';
//-->
</script>

<?php
global $st;

echo isset($st['js_files']) ? $st['js_files'] : '';
?>
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

			&nbsp;&#8595;&nbsp;<font color=red><?= mksize($CURUSER['downloaded']) ?></font> - 
            <b>&#8593;&nbsp;</b><font color=green><?= mksize($CURUSER['uploaded']) ?></font> - 
            <?= $txt['RATIO'] ?>: <?= $userratio ?> &nbsp;<?php
            if ($unread) {	print("<a href=\"account.php\"><b><font color=#FF0000>New PM" . ($nmessages != 1 ? "s" : "") . " ($unread)</b></a></font>"); }?>

			<?php
			} else {
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

<?php require_once TT_DIR . '/columns/left-column.php'; ?>

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
