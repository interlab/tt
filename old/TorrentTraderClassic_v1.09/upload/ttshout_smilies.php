<?
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
<?
dbconn(false);

$query = 'SELECT * FROM shoutbox_emoticons GROUP BY image';

$result = mysql_query($query);
$alt = false;

while ($row = mysql_fetch_assoc($result)) {

echo "
<img src='".$GLOBALS['SITEURL']."/images/shoutbox/".$row['image']."' onClick=\"InsertSmilie('".$row['text']."');\">";

}
?>
</BODY>
</HTML>