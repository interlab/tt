<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?= $title ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="imagetoolbar" content="no" />
<link rel="stylesheet" type="text/css" href="themes/NB-Leo/theme.css" />
<script src="<?= TT_JS_URL ?>/theme.js"></script>

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
        <td>
        <div class="logo-container">
            <div class="leo-logo">
                <a href="index.php"><img src="themes/NB-Leo/images/leo_logo.jpg" width="379" height="89" /></a>
            </div>
            <div class="login">
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
            </div>
        </div>
        <div class="leo-top-menu">
            <div>
                <a href=index.php><?= $txt['HOME'] ?></a><span> | </span>
                <?php if ($FORUMS) { ?>
                <a href=forums.php><?= $txt['FORUMS'] ?></a><span> | </span>
                <?php } ?>
                <a href=browse.php><?= $txt['BROWSE_TORRENTS'] ?></a><span> | </span>
                <a href=torrents-search.php><?= $txt['SEARCH'] ?></a><span> | </span>
                <a href=torrents-upload.php><?= $txt['UPLOADT'] ?></a><span> | </span>
                <a href=faq.php><?= $txt['FAQ'] ?></a>
            </div>
        </div>
</td>
      </tr>
    </table><table width="100%" border="0" cellspacing="0" cellpadding="0">
	<TBODY>
  <tr>
    <td><table class="mbody" width="100%" border="0" cellspacing="0" cellpadding="6">
	<TBODY>
      <tr>
	<TD vAlign="top" width="180">

<?php require_once TT_COLUMNS_DIR . '/left-column.php'; ?>

    </TD>
<TD align="center" vAlign="top">

<!-- banner code starts here -->
<br><CENTER><?php
$content = file_get_contents(TT_ROOT_DIR . '/banners.txt');
$s_con = preg_split('/~/', $content);
$banners = rand(0,(count($s_con)-1));
echo $s_con[$banners];
?></CENTER><br>
<!-- end banner code -->
