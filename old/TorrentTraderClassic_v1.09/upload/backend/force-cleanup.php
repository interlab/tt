<?
require_once("config.php");
require_once("cleanup.php");
dbconn(false);

    global $autoclean_interval;

    $now = time();
    $docleanup = 0;

    $res = mysql_query("SELECT value_u FROM avps WHERE arg = 'lastcleantime'");
    $row = mysql_fetch_array($res);

    $ts = $row[0];

   
docleanup();
 mysql_query("UPDATE avps SET value_u=$now WHERE arg='lastcleantime' AND value_u = $ts");

?>
<BR><BR><BR><BR><CENTER><FORM> 
<B>Force cleanup completed: Click <INPUT type="button" value="here" onClick="history.back()"> to return to the Staff CP</B>
</FORM> </CENTER>