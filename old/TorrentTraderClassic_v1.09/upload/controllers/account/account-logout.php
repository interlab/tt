<?php

// Logout of site, clear cookie and return to index
require_once '../../backend/functions.php';
dbconn();
logoutcookie();
header('Location: '.$SITEURL.'/index.php');

