<?
//
// Confirm account Via email and send PM
//
require_once("backend/functions.php");


$id = 0 + $HTTP_GET_VARS["id"];
$md5 = $HTTP_GET_VARS["secret"];

if (!$id)
	httperr();

dbconn();

$res = mysql_query("SELECT password, secret, status FROM users WHERE id = $id");
$row = mysql_fetch_array($res);

if (!$row)
	httperr();

if ($row["status"] != "pending") {
	header("Refresh: 0; url=account-confirm-ok.php?type=confirmed");
	exit();
}

$sec = hash_pad($row["secret"]);
if ($md5 != md5($sec))
	httperr();

$newsec = mksecret();

mysql_query("UPDATE users SET secret=" . sqlesc($newsec) . ", status='confirmed' WHERE id=$id AND secret=" . sqlesc($row["secret"]) . " AND status='pending'");

if (!mysql_affected_rows())
	httperr();

logincookie($id, $row["password"], $newsec);
    //send welcome pm
    if ($WELCOMEPMON)
    {
        $WELCOMEPMMSG = trim($WELCOMEPMMSG);
        $added = sqlesc(get_date_time());
        mysql_query("INSERT INTO messages (poster, sender, receiver, added, msg) VALUES ('0', '0', $id, $added, " . sqlesc($WELCOMEPMMSG) . ")");
    
    }
header("Refresh: 0; url=account-confirm-ok.php?type=confirm");

?>
