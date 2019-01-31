<?php
// HERE IS WHERE FRAMES AND BLOCKS ARE DEFINED
//
// By Nikkbu
//

//BEGIN FRAME
function begin_frame($caption = "-", $align = "justify")
{
    $ss_uri = $GLOBALS['ss_uri'];

?>
<br>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width="31" height="27"><img src="themes/NB-Leo/images/ftl.png" width="31" height="27" /></td>
<td height="27" align="center" background="themes/NB-Leo/images/ftm.png"><font color="#FFFFFF">&nbsp;&nbsp;<b><?= $caption ?></b></font></td>
<td width="16" height="27"><img src="themes/NB-Leo/images/ftr.png" width="16" height="27" /></td>
</tr>
</table></td>
</tr>
</table><table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width="6" background="themes/NB-Leo/images/fml.jpg"><img src="themes/NB-Leo/images/blank.gif" width="6" height="1" /></td>
<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td class="cont" width="100%" align="<?= $align ?>" background="themes/NB-Leo/images/fmm.jpg">
<?php
}
//ATTACH FRAME
function attach_frame($padding = 0)
{
    print("\n");
}

//END FRAME
function end_frame(){
?>
</td>
</tr>
</table>
</td>
<td width="6" background="themes/NB-Leo/images/fmr.jpg"><img src="themes/NB-Leo/images/blank.gif" width="6" height="1" /></td>
</tr>
</table>
</td>
</tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width="6" height="6"><img src="themes/NB-Leo/images/fbl.jpg" width="6" height="6" /></td>
<td background="themes/NB-Leo/images/fbm.jpg"><img src="themes/NB-Leo/images/blank.gif" width="1" height="6" /></td>
<td width="6" height="6"><img src="themes/NB-Leo/images/fbr.jpg" width="6" height="6" /></td>
</tr>
</table></td>
</tr>
</table>
</td>
</tr>
</table>
<?php
}

//BEGIN BLOCK
function begin_block($caption = "-", $align = "justify")
{
    $ss_uri = $GLOBALS['ss_uri'];
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width="31" height="27"><img src="themes/NB-Leo/images/ftl.png" width="31" height="27" /></td>
<td height="27" align="center" background="themes/NB-Leo/images/ftm.png"><font color="#FFFFFF">&nbsp;&nbsp;<b><?= $caption ?></b></font></td>
<td width="16" height="27"><img src="themes/NB-Leo/images/ftr.png" width="16" height="27" /></td>
</tr>
</table></td>
</tr>
</table><table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width="6" background="themes/NB-Leo/images/fml.jpg"><img src="themes/NB-Leo/images/blank.gif" width="6" height="1" /></td>
<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td class="cont" width="100%" align="<?= $align ?>" background="themes/NB-Leo/images/fmm.jpg">
<?php
}

//END BLOCK
function end_block()
{
?>
</td>
</tr>
</table>
</td>
<td width="6" background="themes/NB-Leo/images/fmr.jpg"><img src="themes/NB-Leo/images/blank.gif" width="6" height="1" /></td>
</tr>
</table>
</td>
</tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width="6" height="6"><img src="themes/NB-Leo/images/fbl.jpg" width="6" height="6" /></td>
<td background="themes/NB-Leo/images/fbm.jpg"><img src="themes/NB-Leo/images/blank.gif" width="1" height="6" /></td>
<td width="6" height="6"><img src="themes/NB-Leo/images/fbr.jpg" width="6" height="6" /></td>
</tr>
</table></td>
</tr>
</table>
</td>
</tr>
</table>
<br>
<?php
}

?>