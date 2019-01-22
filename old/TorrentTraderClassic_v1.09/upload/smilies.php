<?
ob_start("ob_gzhandler");
require "backend/functions.php";

dbconn(false);

loggedinorreturn();

$ss_a = @mysql_fetch_array(@mysql_query("select uri from stylesheets where id=" . $CURUSER["stylesheet"]));

    if ($ss_a) $ss_uri = $ss_a["uri"];
		require_once("themes/" . $ss_uri . "/block.php");

insert_smilies_frame();
end_frame();
?>