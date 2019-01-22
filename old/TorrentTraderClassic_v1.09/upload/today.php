<?
require "backend/functions.php";
dbconn(false);
stdhead();

begin_frame("Todays Torrents");

$date_time=get_date_time(time()-(3600*24)); // the 24 is the hours you want listed
if (!$LOGGEDINONLY){
	$catresult = mysql_query("SELECT id, name FROM categories ORDER BY sort_index");
		while($cat = mysql_fetch_array($catresult))
		{
			$orderby = "ORDER BY torrents.id DESC"; //Order
			$where = "WHERE banned = 'no' AND category='$cat[id]' AND visible='yes'";

			$query = "SELECT torrents.id, torrents.category, torrents.leechers, torrents.seeders, torrents.name, torrents.times_completed, torrents.size, torrents.comments, torrents.nfo,torrents.owner, torrents.banned, torrents.numfiles, torrents.added, torrents.hits, categories.name AS cat_name, categories.image AS cat_pic, users.username FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id $where AND torrents.added>='$date_time' $orderby";

			$res = mysql_query($query);
			$numtor = mysql_num_rows($res);
			if ($numtor != 0) {
		echo "<B><a href=browse.php?cat=".$cat[id].">$cat[name]</a></B>";
					list($pagertop, $pagerbottom, $limit) = pager(1000, $count, "index.php?" . $addparam); //adjust pager to match LIMIT
					torrenttable($res);
					echo "<div align=left>» <a href=browse.php?cat=".$cat[id].">Show All</a></div>";
			}
						echo "<BR>";
    }
}



if ($LOGGEDINONLY){
	if (!$CURUSER){
				echo "<BR><BR><b><CENTER>You Are Not Logged In<br>Only Members Can View Torrents Please Signup.</CENTER><BR><BR>";
}else{
		   $catresult = mysql_query("SELECT id, name FROM categories ORDER BY sort_index");
		while($cat = mysql_fetch_array($catresult))
		{
			$orderby = "ORDER BY torrents.id DESC"; //Order
			$where = "WHERE banned = 'no' AND category='$cat[id]' AND visible='yes'";

			$query = "SELECT torrents.id, torrents.category, torrents.leechers, torrents.seeders, torrents.name, torrents.times_completed, torrents.size, torrents.comments, torrents.nfo,torrents.owner, torrents.banned, torrents.numfiles, torrents.added, torrents.hits, categories.name AS cat_name, users.username FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id $where AND torrents.added>='$date_time' $orderby";

			$res = mysql_query($query);
			$numtor = mysql_num_rows($res);
			if ($numtor != 0) {
		echo "<B><a href=browse.php?cat=".$cat[id].">$cat[name]</a></B>";
					list($pagertop, $pagerbottom, $limit) = pager(1000, $count, "index.php?" . $addparam); //adjust pager to match LIMIT
					torrenttable($res);
					echo "<div align=left>» <a href=browse.php?cat=".$cat[id].">Show All</a></div>";
			}
			echo "<BR>";
    }
	}
}



end_frame();
stdfoot();
?>