<?php

require_once '../../backend/functions.php';

dbconn(false);

loggedinorreturn();
modonly();

$id = (int) $_GET["id"];

DB::query('DELETE FROM reports WHERE id = ' . $id);

ob_end_clean();

header('Location: ' . $GLOBALS['SITEURL'] . '/admin.php');

die('');
