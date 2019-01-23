<?php
//
// - Theme And Language Updated 25.Nov.05
//
require "backend/functions.php";

dbconn(false);
stdhead("Donate");

begin_frame($txt['DONATE']);

$res = DB::fetchAssoc("SELECT * FROM site_settings");
$mothlydonated = $res['donations'];
$requireddonations = $res['requireddonations'];
$donatepagecontents = $res['donatepage'];

echo "<br><b>" . $txt['TARGET'] . ": </b><font color=\"red\">$" . $requireddonations . "</font><br><b>" . $txt['DONATIONS'] 
    . ": </b><font color=\"green\">$" . $mothlydonated . "</font></center><br>
    <br><br><br>";

echo stripslashes($donatepagecontents);

end_frame();
stdfoot();
