<?

require_once("backend/functions.php");

dbconn();

$body = trim($_POST["body"]);
if (!$body) {
  bark("Oops...", "You must enter something!");
  exit;
}

if (!isset($CURUSER))
        die();

if (!mkglobal("body:id"))
        die();

$id = 0 + $id;
if (!$id)
        die();

$res = mysql_query("SELECT 1 FROM news WHERE id = $id");
$row = mysql_fetch_array($res);
if (!$row)
        die();

mysql_query("INSERT INTO comments (user, news, added, text, ori_text) VALUES (" .
                $CURUSER["id"] . ",$id, '" . get_date_time() . "', " . sqlesc($body) .
     "," . sqlesc($body) . ")");

$newid = mysql_insert_id();

mysql_query("UPDATE news SET comments = comments + 1 WHERE id = $id");

header("Refresh: 0; url=show-archived.php?id=$id&viewcomm=$newid#comm$newid");

?>