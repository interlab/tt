<?php

require_once("backend/functions.php");

dbconn(false);

loggedinorreturn();
modonly();

stdhead("Delete Report");

begin_frame();

$id = (int)$_GET["id"];

$res = mysql_query("DELETE FROM reports WHERE id =$id") or sqlerr();

end_frame();
stdfoot();
?>
<script LANGUAGE="JavaScript">
 self.location='admin.php';
</SCRIPT>