<?php
//
// - Theme And Language Updated 27.Nov.05
//
ob_start("ob_gzhandler");
require "backend/functions.php";

dbconn();
stdhead("Rules");
begin_frame($txt['SITE_RULES']);

$res = DB::query("select * from rules order by id");
while ($arr = $res->fetch()) {
	if ($arr["public"] == "yes"
        || ($arr["public"] == "no" && $arr["class"] <= $CURUSER["class"])
    ){
		begin_frame($arr['title']);
		print(format_comment($arr["text"]));
		end_frame();
	}
}

echo "<BR><BR>";

end_frame();

stdfoot();
