<?php
require_once("backend/functions.php");
dbconn();
loggedinorreturn();

stdhead("Fill Request");

begin_frame("Request Filled");

$filledurl = $_GET["filledurl"];
$requestid = (int)$_GET["requestid"];

$res = mysql_query("SELECT users.username, requests.userid, requests.request FROM requests inner join users on requests.userid = users.id where requests.id = $requestid") or sqlerr();
 $arr = mysql_fetch_assoc($res);

$res2 = mysql_query("SELECT username FROM users where id =" . $CURUSER[id]) or sqlerr();
 $arr2 = mysql_fetch_assoc($res2);


$msg = "Your request, [url=$SITEURL/reqdetails.php?id=" . $requestid . "][b]" . $arr[request] . "[/b][/url], has been filled by [url=$SITEURL/account-details.php?id=" . $CURUSER[id] . "][b]" . $arr2[username] . "[/b][/url]. You can download your request from [url=" . $filledurl. "][b]" . $filledurl. "[/b][/url].  Please do not forget to leave thanks where due.  If for some reason this is not what you requested, please reset your request so someone else can fill it by following [url=$SITEURL/reqreset.php?requestid=" . $requestid . "]this[/url] link.  Do [b]NOT[/b] follow this link unless you are sure that this does not match your request.";

       mysql_query ("UPDATE requests SET filled = '$filledurl', filledby = $CURUSER[id] WHERE id = $requestid") or sqlerr();
mysql_query("INSERT INTO messages (poster, sender, receiver, added, msg) VALUES(0, 0, $arr[userid], '" . get_date_time() . "', " . sqlesc($msg) . ")") or sqlerr(__FILE__, __LINE__);


print("<br><BR><div align=left>Request $requestid successfully filled with <a href=$filledurl>$filledurl</a>.  User <a href=account-details.php?id=$arr[userid]><b>$arr[username]</b></a> automatically PMd.  <br>
Filled that accidently? No worries, <a href=reqreset.php?requestid=$requestid>CLICK HERE</a> to mark the request as unfilled.  Do <b>NOT</b> follow this link unless you are sure there is a problem.<br><BR></div>");
print("<BR><BR>Thank you for filling a request :)<br><br><a href=viewrequests.php>View More Requests</a>");
end_frame();

stdfoot();
?>