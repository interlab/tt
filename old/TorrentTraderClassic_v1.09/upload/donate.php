<?php

require_once 'backend/functions.php';

dbconn(false);
stdhead('Donate');

begin_frame($txt['DONATE']);

$res = DB::fetchAssoc("SELECT * FROM site_settings");
$mothlydonated = $res['donations'];
$requireddonations = $res['requireddonations'];
$donatepagecontents = $res['donatepage'];

echo '<br><b>' . $txt['TARGET'] . ': </b><span style="color: red;">$' . $requireddonations . '</span>
    <br><b>' . $txt['DONATIONS'] . ': </b><span style="color: green;">$' . $mothlydonated . '</span>
    <br><br><br><br>';

echo $donatepagecontents;

end_frame();
stdfoot();
