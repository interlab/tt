<?php
require_once("backend/functions.php");
dbconn();
loggedinorreturn();

stdhead("Reset Request");

begin_frame("Reset");

$requestid = (int)$_GET["requestid"];

$res = mysql_query("SELECT userid, filledby FROM requests WHERE id =$requestid") or sqlerr();
 $arr = mysql_fetch_assoc($res);


if (($CURUSER[id] == $arr[userid]) || (get_user_class() >= 4) || ($CURUSER[id] == $arr[filledby]))
{

 @mysql_query("UPDATE requests SET filled='', filledby=0 WHERE id =$requestid") or sqlerr();
 
 print("Request $requestid successfully reset.");
}
else
 print("Sorry, cannot reset a request when you are not the owner");

end_frame();

stdfoot();
?>