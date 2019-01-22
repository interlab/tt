<?php
require "backend/functions.php";
dbconn(false);
loggedinorreturn();
adminonly();

if($page == "sent")
{
	stdhead("", false);
bark2("Success", "Message Sent OK", Success);
	stdfoot();

}else{

stdhead("", false);
require_once("backend/admin-functions.php");
adminmenu();
begin_frame("To send message to the staff and others");
?>

<table class=main width=750 border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>
<div align=center>
<form method=post action=takestaffmess.php>
<?

if ($_GET["returnto"] || $_SERVER["HTTP_REFERER"])
{
?>
<input type=hidden name=returnto value=<?=$_GET["returnto"] ? $_GET["returnto"] : $_SERVER["HTTP_REFERER"]?>>
<?
}
?>
<table cellspacing=0 cellpadding=5>
<tr>
<td>Select groups to send message to:<br>
  <table style="border: 0" width="100%" cellpadding="0" cellspacing="0">
    <tr>
             <td style="border: 0" width="20"><input type="checkbox" name="clases[]" value="0">
             </td>
             <td style="border: 0">User</td>

             <td style="border: 0" width="20"><input type="checkbox" name="clases[]" value="1">
             </td>
             <td style="border: 0">Uploader</td>
             <td style="border: 0" width="20"><input type="checkbox" name="clases[]" value="2">
             </td>
             <td style="border: 0">VIP</td>
      </tr>
    <tr>
             <td style="border: 0" width="20"><input type="checkbox" name="clases[]" value="3">
             </td>
             <td style="border: 0">Moderator</td>
             <td style="border: 0" width="20"><input type="checkbox" name="clases[]" value="4">
             </td>
             <td style="border: 0">Super Moderator</td>
             <td style="border: 0" width="20"><input type="checkbox" name="clases[]" value="5">
             </td>
             <td style="border: 0">Administrator</td>
       <td style="border: 0">&nbsp;</td>
       <td style="border: 0">&nbsp;</td>
      </tr>
    </table>
  </td>
</tr>
<tr><td><textarea name=msg cols=80 rows=15><?=$body?></textarea>
<br>
NOTE: Remember that BB can be used (NO HTML)</td></tr>
<tr>
<td colspan=2><div align="center"><b>Sender:&nbsp;&nbsp;</b>
<?=$CURUSER['username']?>
<input name="sender" type="radio" value="self" checked>
&nbsp; System
<input name="sender" type="radio" value="system">
</div></td></tr>
<tr><td colspan=2 align=center><input type=submit value="Send" class=btn></td></tr>
</table>
<input type=hidden name=receiver value=<?=$receiver?>>
</form>

 </div></td></tr></table>

<?
end_frame();
stdfoot();
}
?>
