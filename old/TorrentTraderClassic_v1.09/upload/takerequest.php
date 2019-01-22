<?

require_once("backend/functions.php");

dbconn();

$userid = $_POST["userid"];
$requestartist = $_POST["requestartist"];
$requesttitle = $_POST["requesttitle"];
$request = $requestartist . " - " . $requesttitle;
$descr = $_POST["descr"];
$cat = $_POST["category"];

$userid = sqlesc($userid);
$request = sqlesc($request);
$descr = sqlesc($descr);
$cat = sqlesc($cat);


mysql_query("INSERT INTO requests (hits,userid, cat, request, descr, added) VALUES(1,$CURUSER[id], $cat, $request, $descr, '" . get_date_time() . "')") or sqlerr(__FILE__,__LINE__);


$id = mysql_insert_id();

@mysql_query("INSERT INTO addedrequests (requestid,userid) VALUES($id, $CURUSER[id])") or sqlerr();

if ($SHOUTBOX){
mysql_query("INSERT INTO shoutbox (user,message,date,userid) VALUES('Request', '$CURUSER[username] has made a request for [url=".$SITEURL."/reqdetails.php?id=".$id."]".$requesttitle."[/url]', now(), '0')") or sqlerr(__FILE__,__LINE__);
}


//write_log("$request was added to the Request section");

header("Refresh: 0; url=viewrequests.php");

?>