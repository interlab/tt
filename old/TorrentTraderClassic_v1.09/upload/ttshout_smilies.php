<?php

require('backend/functions.php');
require('backend/config.php');
?>

<HTML>
<HEAD>
<TITLE>Insert Smilie</TITLE>
<SCRIPT Language="JavaScript">
<!--

function InsertSmilie(texttoins)
{
    window.opener.document.ttshoutform.message.value = window.opener.document.ttshoutform.message.value+' '+texttoins+' ';
    window.opener.document.ttshoutform.message.focus();
    window.close();
}

//-->
</SCRIPT>

</HEAD>

<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#FF0000" VLINK="#800000" ALINK="#FF00FF">
<?php
dbconn(false);

$query = 'SELECT * FROM shoutbox_emoticons GROUP BY image';
$res = DB::query($query);

while ($row = $res->fetch()) {
    echo '
        <img src="'.$GLOBALS['SITEURL'].'/images/shoutbox/'.$row['image'].'" onClick="InsertSmilie(\''.$row['text'].'\');">';
}
?>

</BODY>
</HTML>