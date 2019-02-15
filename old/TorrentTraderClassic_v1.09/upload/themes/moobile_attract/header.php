<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?= $title ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="imagetoolbar" content="no" />
<link rel="stylesheet" type="text/css" href="themes/moobile_attract/theme.css" />
<script src="<?= TT_JS_URL ?>/theme.js"></script>

</head>
<BODY class=pastyla style="MARGIN-TOP: 0px; SCROLLBAR-FACE-COLOR: #9ba87b; SCROLLBAR-HIGHLIGHT-COLOR: #cccc99; SCROLLBAR-SHADOW-COLOR: #666666; SCROLLBAR-ARROW-COLOR: #f5ffd5; SCROLLBAR-TRACK-COLOR: #f5ffd5; SCROLLBAR-BASE-COLOR: #ffffff; scrollbar-dark-shadow-color: #3333ff; scrollbar-3d-light-color: #CCCC99" text=#000000 vLink=#999999 link=#999999 bgColor=#ffffff>
<CENTER>
<!-- DEBUT DE LA PAGE -->
<table cellSpacing=0 cellPadding=0 width=770 border=0>
<tr>
<td>
<div align="center">
	<table border=0 width="40%" cellspacing="0">
<tr>
<td colspan="2">

<TABLE cellSpacing=0 cellPadding=0 width=770 border=0 background=themes/moobile_attract/images/bg_logo.jpg>
<TBODY>
<TR>
<TD class=bb2 vAlign=center noWrap>
<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
<TBODY>
<TR height=41>
<TD vAlign=top align=left>&nbsp;</TD>
</TR></TBODY></TABLE><BR>&nbsp;</TD>
</TR>
</TBODY>
</TABLE>
<!-- DEBUT MENU PRINCIPAL -->
<TABLE class=b2 cellSpacing=0 cellPadding=4 width=770 bgColor=#d2d2d2 border=0>
<TBODY>
<TR>
<TD align=middle width="100%">
    <A class=navi0 href=index.php><?= $txt['HOME'] ?></a> | 
    <?php if ($FORUMS) {?><a class=navi0 href=forums.php><?= $txt['FORUMS'] ?></a> | <?php } ?>
    <a class=navi0 href=torrents-search.php><?= $txt['SEARCH'] ?></a> | 
    <a class=navi0 href=torrents-upload.php><?= $txt['UPLOADT'] ?></a> | 
    <a class=navi0 href=faq.php><?= $txt['FAQ'] ?></a>
</TD>
</TR>
</TBODY>
</TABLE>
<!-- FIN MENU PRINCIPAL -->
      <TABLE class=b2 cellSpacing=0 cellPadding=4 width=770 bgColor=#eeeeee 
      border=0>
        <TBODY>
        <TR>
          <TD align=middle width="45%">
			<p align="left">
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
			&nbsp;&#8595;&nbsp;<font color=red><?= mksize($CURUSER['downloaded']) ?></font> - <b>&#8593;&nbsp;</b>
            <font color=green><?= mksize($CURUSER['uploaded']) ?></font> - <?= $txt['RATIO'] ?>: <?= $userratio ?> 
            &nbsp;<?php if ($unread) {
                print("<a href=\"account.php\"><b><font color=#FF0000>New PM" . ($nmessages != 1 ? "s" : "") . " ($unread)</b></a></font>");
            }?>

			<?php 
    } else {
        echo "<a href=account-login.php><font color=#FF0000>". $txt['LOGIN'] . "</font></a> <B>:</B> " .
            "<a href=account-signup.php><font color=#FF0000>" . $txt['REGISTERNEW'] . "</font></a>";
    }
			?>
			</TD>
			<form method="get" action="torrents-search.php">
			<TD><?= $txt['SEARCH'] ?>: </STRONG><br>
			<input type="text" name="search" size="30" value="" />
<select name="cat">
<option value="0">(All Categories)</option>
<?php 
$cats = genrelist();
$catdropdown = "";
foreach ($cats as $cat) {
    $catdropdown .= "<option value=\"" . $cat["id"] . "\"";
    if ($cat["id"] == $_GET["cat"])
        $catdropdown .= " selected=\"selected\"";
    $catdropdown .= ">" . h($cat["name"]) . "</option>\n";
}
?>
<?= $catdropdown ?>
</select> <input type=submit border=0 name=Search value=Search /></TD>
			</TR></FORM>
			
			</TBODY></TABLE></td>
	</tr>
	<tr>
		<td colspan="2" align=center>
<!-- banner code starts here -->
		   <CENTER><?php
$content = file_get_contents(TT_ROOT_DIR . '/banners.txt');
$s_con = preg_split('/~/', $content);
$banners = rand(0,(count($s_con)-1));
echo $s_con[$banners];
?></CENTER>
<!-- end banner code -->
		</td>
	</tr>
	<tr>
		<td width="21%" valign=top align=center>

<?php require_once TT_DIR . '/columns/left-column.php'; ?>

		</td>
		<td width="77%" valign=top align=center>