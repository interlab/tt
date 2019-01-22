<?

require_once("backend/functions.php");

dbconn();

if (get_user_class() < UC_MODERATOR)
	stderr("Error", "Access denied.");
function bark($msg) {
 stdhead();
stdmsg("Edit failed!", $msg);
 stdfoot();
 exit;
}

if (!validfilename($requesttitle))
	bark("Invalid filename!");
$request = $requesttitle;
$descr = unesc($_POST["descr"]);
if (!$descr)
  bark("You must enter a description!");
$cat = (0 + $_POST["category"]);
if (!is_valid_id($cat))
	bark("You must select a category to put the request in!");
$request = sqlesc($request);
$descr = sqlesc($descr);
$cat = sqlesc($cat);
$filledby = $_POST["filledby"];
$id = (int)$_GET["id"];
$filled = $_POST["filled"];
if ($filled)
{
if (!is_valid_id($filledby))
	bark("Not a valid id!");
$res = mysql_query("SELECT id FROM users WHERE id=".$filledby."");
if (mysql_num_rows($res) == 0)
       bark("Filled by id($filledby) doesn't match any users, try again");
$filledurl = $_POST["filledurl"];
if (!$filledurl)
	bark("No torrent url");
mysql_query("UPDATE requests SET cat=$cat, request=$request, descr=$descr, filledby=$filledby, filled ='yes', filledurl='$filledurl' WHERE id = $id") or sqlerr(__FILE__,__LINE__);
}
else
mysql_query("UPDATE requests SET cat=$cat, filledby = 0, request=$request, descr=$descr, filled = 'no' WHERE id = $id") or sqlerr(__FILE__,__LINE__);

header("Refresh: 0; url=reqdetails.php?id=$id");

?>