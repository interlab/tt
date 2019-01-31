<?php
//
//
//	THIS FILE IS WHERE YOU DEFINE THE BLOCKS AND FRAMES LAYOUT
//
//


//BEGIN FRAME
function begin_frame($caption = "-", $align = "justify")
{
    $ss_uri = $GLOBALS['ss_uri'];
?>
<table class="ftable" width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td>
<table class="fheader" width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td class="NB_ftl" width="28" height="34"><img src="themes/NB-Xmas/images/blank.gif" width="28" height="34"></td>
<td class="NB_ftmain" width="100%" height="31" style="white-space: nowrap;"><?= $caption ?></td>
<td class="NB_ftr" width="13" height="34"><img src="themes/NB-Xmas/images/blank.gif" width="13" height="34"></td>
</tr>
</table>
<table class="fmiddle" width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td class="NB_fml" width="6"><img src="themes/NB-Xmas/images/blank.gif" width="6" height="2"></td>
<td class="NB_fmmain" width="100%" align="<?= $align ?>">
<?php
}
//ATTACH FRAME
function attach_frame($padding = 0) {
    print("\n");
}

//END FRAME
function end_frame(){
?>
</td>
<td class="NB_fmr" width="4"><img src="themes/NB-Xmas/images/blank.gif" width="4" height="2"></td>
</tr>
</table>
<table class="ffooter" width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td class="NB_fbl" width="10" height="12"><img src="themes/NB-Xmas/images/blank.gif" width="10" height="12"></td>
<td class="NB_fbm" width="100%" height="12"><img src="themes/NB-Xmas/images/blank.gif" width="100%" height="12"></td>
<td class="NB_fbr" width="10" height="12"><img src="themes/NB-Xmas/images/blank.gif" width="10" height="12"></td>
</tr>
</table>
</td>
</tr>
</table><BR>
<?php
}

//BEGIN BLOCK
function begin_block($caption = "-", $align = "justify")
{
    $ss_uri = $GLOBALS['ss_uri'];
?>
<table class="ftable" width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td>
<table class="fheader" width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td class="NB_ftl" width="28" height="34"><img src="themes/NB-Xmas/images/blank.gif" width="28" height="34"></td>
<td class="NB_ftmain" width="100%" height="31" style="white-space: nowrap;"><?= $caption ?></td>
<td class="NB_ftr" width="13" height="34"><img src="themes/NB-Xmas/images/blank.gif" width="13" height="34"></td>
</tr>
</table>
<table class="fmiddle" width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td class="NB_fml" width="6"><img src="themes/NB-Xmas/images/blank.gif" width="6" height="2"></td>
<td class="NB_fmmain" width="100%" align="<?= $align ?>">
<?php
}

//END BLOCK
function end_block(){
?>
</td>
<td class="NB_fmr" width="4"><img src="themes/NB-Xmas/images/blank.gif" width="4" height="2"></td>
</tr>
</table>
<table class="ffooter" width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td class="NB_fbl" width="10" height="12"><img src="themes/NB-Xmas/images/blank.gif" width="10" height="12"></td>
<td class="NB_fbm" width="100%" height="12"><img src="themes/NB-Xmas/images/blank.gif" width="100%" height="12"></td>
<td class="NB_fbr" width="10" height="12"><img src="themes/NB-Xmas/images/blank.gif" width="10" height="12"></td>
</tr>
</table>
</td>
</tr>
</table><BR>
<?php
}

?>

</td>
      </tr>
    </table></td>
  </tr>
</table>