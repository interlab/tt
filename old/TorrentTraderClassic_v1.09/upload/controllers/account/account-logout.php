<?php

// Logout of site, clear cookie and return to index

dbconn();
logoutcookie();
header('Location: '.$SITEURL.'/index.php');

