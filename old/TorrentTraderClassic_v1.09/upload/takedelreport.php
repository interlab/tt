<?php

require_once("backend/functions.php"); 

dbconn(); 
loggedinorreturn(); 

jmodonly();

$_POST["delreport"] = array_map("intval", $_POST["delreport"]);

$res = mysql_query ("SELECT id FROM reports WHERE dealtwith=0 AND id IN (" . implode(", ", $_POST[delreport]) . ")");

while ($arr = mysql_fetch_assoc($res))
mysql_query ("UPDATE reports SET dealtwith=1, dealtby = $CURUSER[id] WHERE id = $arr[id]") or sqlerr();

?>
<script LANGUAGE="JavaScript">
 self.location='admin.php';
</SCRIPT>

