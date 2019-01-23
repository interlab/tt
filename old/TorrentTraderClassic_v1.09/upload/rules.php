<?php
//
// - Theme And Language Updated 27.Nov.05
//
ob_start("ob_gzhandler");
require "backend/functions.php";
dbconn();
stdhead("Rules");
begin_frame($txt['SITE_RULES']);

$res = mysql_query("select * from rules order by id");
while ($arr=mysql_fetch_assoc($res)){
	if ($arr["public"]=="yes")
		{
		begin_frame($arr[title]);
		print(format_comment($arr["text"]));
		end_frame();
		}
elseif($arr["public"]=="no" && $arr["class"]<=$CURUSER["class"])
		{
		begin_frame($arr[title]);
		print(format_comment($arr["text"]));
		end_frame();
		}
}

echo "<BR><BR>";

end_frame();

stdfoot();
