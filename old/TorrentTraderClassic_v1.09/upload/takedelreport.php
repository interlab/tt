<?php

require_once('backend/functions.php'); 

dbconn(); 
loggedinorreturn(); 

jmodonly();

$_POST['delreport'] = array_map('intval', $_POST['delreport']);

$res = DB::query('SELECT id FROM reports WHERE dealtwith = 0 AND id IN (' . implode(', ', $_POST['delreport']) . ')');

while ($arr = $res->fetch()) {
    DB::executeUpdate('UPDATE reports SET dealtwith = 1, dealtby = ' . $CURUSER['id'] . ' WHERE id = ' . $arr['id']);
}

ob_end_clean();
header('Location: ' . $GLOBALS['SITEURL'] . '/admin.php');

die('');
