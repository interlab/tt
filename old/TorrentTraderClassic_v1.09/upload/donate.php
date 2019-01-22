<?php
//
// - Theme And Language Updated 25.Nov.05
//
require "backend/functions.php";

dbconn(false);
stdhead("Donate");

begin_frame("" . DONATE . "");

$arr = mysql_query("SELECT * FROM site_settings ") or sqlerr(__FILE__, __LINE__);
$res = mysql_fetch_assoc($arr);
$mothlydonated = $res['donations'];
$requireddonations = $res['requireddonations'];
$donatepagecontents = $res['donatepage'];

echo "<br><b>" . TARGET . ": </b><font color=\"red\">$" . $requireddonations . "</font><br><b>" . DONATIONS . ": </b><font color=\"green\">$" . $mothlydonated . "</font></center><br>";
echo "<br><br><br>";

echo stripslashes($donatepagecontents);

end_frame();
stdfoot();
?>