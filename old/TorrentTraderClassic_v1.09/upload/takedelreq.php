<?
require_once("backend/functions.php");

dbconn();
loggedinorreturn();


global $CURUSER;

stdhead("Delete");
begin_frame("Delete");

$_POST["delreq"] = array_map("intval", $_POST["delreq"]);

if (get_user_class() > UC_JMODERATOR){
if (empty($_POST["delreq"])){
print("<CENTER>You must select at least one request to delete.</CENTER>");
end_frame();
stdfoot();
die;
}
$do="DELETE FROM requests WHERE id IN (" . implode(", ", $_POST[delreq]) . ")";
$do2="DELETE FROM addedrequests WHERE requestid IN (" . implode(", ", $_POST[delreq]) . ")";
$res2=mysql_query($do2);
$res=mysql_query($do);
print("<CENTER>Request Deleted OK</CENTER>");

echo "<BR><BR>";
} else {
foreach ($_POST[delreq] as $del_req){
$delete_ok = checkRequestOwnership($CURUSER[id],$del_req);
if ($delete_ok){
$do="DELETE FROM requests WHERE id IN ($del_req)";
$do2="DELETE FROM addedrequests WHERE requestid IN ($del_req)";
$res2=mysql_query($do2);
$res=mysql_query($do);
print("<CENTER>Request ID $del_req Deleted</CENTER>");
} else {
print("<CENTER>No Permission to delete Request ID $del_req</CENTER>");
}
}
}

end_frame();
stdfoot();



function checkRequestOwnership ($user, $delete_req){
$query = mysql_query("SELECT * FROM requests WHERE userid=$user AND id = $delete_req") or sqlerr();
$num = mysql_num_rows($query);
if ($num > 0)
return(true);
else
return(false);
}


?>