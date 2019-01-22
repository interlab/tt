<?

?>
<script>
function toggle(nome) {
if(document.getElementById(nome).style.display=='none')
{
 document.getElementById(nome).style.display = '';
 document.getElementById(nome+"img").src="images/noncross.gif";
} else {
 document.getElementById(nome).style.display = 'none';
 document.getElementById(nome+"img").src="images/cross.gif";
}
}
</script>
<table algin="center" width="98%" cellpadding="2" cellspacing="2">
<tr>
<td>
<?
$query = 'SELECT max_display, comment FROM news_options';
$nopt = mysql_query($query) or die(mysql_error());
$arow = mysql_fetch_array($nopt);



$n_q = mysql_query("SELECT news.id, news.title, news.text, news.user, news.date, news.comments, users.username FROM news,users WHERE users.username = news.user ORDER BY news.id DESC LIMIT ".$arow['max_display']."");


$n_t = mysql_num_rows($n_q);

if($n_t !== 0){
	   if (!$nid_pedido) $mostrar = '';
	else $mostrar = 'none';
	$img = 'noncross';

	while ($n = mysql_fetch_array($n_q))
	   {
				   $nid = $n['id'];
			 if ($nid_pedido == $nid) $mostrar = '';
			 $uid = $n['user'];
			 $title = $n['title'];
			 $date = $n['date'];
			 $text = $n['text'];
			 $username = $n['username'];
			?>
			<a id="<?=$nid;?>"></a>
			<a style="cursor: hand;" onClick="toggle('n<?=$nid;?>');"><img id="n<?=$nid;?>img" src="images/<?=$img;?>.gif"><strong> <?=$title;?></strong></a> (By <?=$username;?></a> at <em><?=$date;?></em>)</a>
			<div id="n<?=$nid;?>" style="display:<?=$mostrar;?>"><br />
			<table align="center" width="95%"><tr><td ><?=stripslashes($text) ?>
			<?
			if ($arow['comment'] == 'on'){
       echo'<br><br><a href="./show-archived.php?id=' . $n['id'] . '">Comment on this article. (' . $n['comments'] . ') Replys so far';
       }
			?>
			</td></tr></table></div><br>
			<?
			 $mostrar = 'none';
			 $img = 'cross';
	   }

}else{
	echo "<center><font color=red>No News!</font></center>";
}
?>
</td>
</tr>
</table>
<?echo '<a href=news-archive.php>View Archive</a>';?>