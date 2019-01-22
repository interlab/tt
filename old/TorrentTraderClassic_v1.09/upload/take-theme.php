<?
require_once("backend/functions.php");
dbconn();
loggedinorreturn();

$set = array();

$updateset = array();
$stylesheet = $_POST["stylesheet"];
$language = $_POST["language"];

if (is_valid_id($stylesheet))
  $updateset[] = "stylesheet = '$stylesheet'";

mysql_query("UPDATE users SET " . implode(",", $updateset) . " WHERE id = " . $CURUSER["id"]);

if (is_valid_id($language))
  $updateset[] = "language = '$language'";

mysql_query("UPDATE users SET " . implode(",", $updateset) . " WHERE id = " . $CURUSER["id"]);

header("Location: index.php" . $urladd);

?>
