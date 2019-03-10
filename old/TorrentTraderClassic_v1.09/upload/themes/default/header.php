<!DOCTYPE HTML>
<html>
<head>
<title><?= $title ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="imagetoolbar" content="no" />
<link rel="stylesheet" type="text/css" href="themes/default/theme.css" />
<script src="<?= TT_JS_URL ?>/theme.js"></script>
<?php
global $tt;

echo isset($tt['css_files']) ? $tt['css_files'] : '';
echo isset($tt['js_files']) ? $tt['js_files'] : '';
?>
</head>

<body>

<div class="tt-header-page">
    <div style="width: 497px; height: 132px; float: left;">
        <div style="height: 80px; width: 497px">
            <a href="index.php"><img src="themes/default/images/template1_01.jpg" border=0 alt="Logo" ></a>
        </div>
        <div class="tt-top-user-menu">
        &nbsp;
            <?php if ($CURUSER) {
                echo $CURUSER['username'], '(<a href=account-logout.php><font color=#ffffff><b>Logout</b></font></a>)';

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
        </div>
        <div class="tt-top-menu">
            &nbsp; <a href=index.php><?= $txt['HOME'] ?></a> <span>•</span> 
            <?php if ($FORUMS) { ?><a href=forums.php><?= $txt['FORUMS'] ?></a> <span>•</span> <?php } ?>
            <a href=browse.php><?= $txt['BROWSE_TORRENTS'] ?></a> <span>•</span> 
            <a href=torrents-search.php><?= $txt['SEARCH'] ?></a> <span>•</span> 
            <a href=torrents-upload.php><?= $txt['UPLOADT'] ?></a> <span>•</span> 
            <a href=faq.php><?= $txt['FAQ'] ?></a>
        </div>
    </div>
    <div class="tt-top-right"></div>
</div>




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
$content = file_get_contents(TT_ROOT_DIR . '/banners.txt');
$s_con = preg_split('/~/', $content);
$banners = rand(0,(count($s_con)-1));
echo $s_con[$banners];
?></CENTER><br>
<!-- end banner code -->

