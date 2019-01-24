<?php

require_once("backend/functions.php");
if (!mkglobal("id"))
	die('bad id');

if (!is_numeric($id)) {
	die('id is not number!');
}

$id = (int) $id;

dbconn(false);
loggedinorreturn();

$quicktags = "<center>
<table border=0 cellpadding=0 cellspacing=0>
<tr><td width=26><a href=\"javascript:Smilies(':)')\"><img src=images/smilies/smile1.gif border=0 alt=':)'></a></td>
<td width=26><a href=\"javascript:Smilies(';)')\"><img src=images/smilies/wink.gif border=0 alt=';)'></a></td>
<td width=26><a href=\"javascript:Smilies(':D')\"><img src=images/smilies/grin.gif border=0 alt=':D'></a></td></tr>
<tr><td width=26><a href=\"javascript:Smilies(':P')\"><img src=images/smilies/tongue.gif border=0 alt=':P'></a></td>
<td width=26><a href=\"javascript:Smilies(':lol:')\"><img src=images/smilies/laugh.gif border=0 alt=':lol:'></a></td>
<td width=26><a href=\"javascript:Smilies(':yes:')\"><img src=images/smilies/yes.gif border=0 alt=':yes:'></a></td></tr>
<tr><td width=26><a href=\"javascript:Smilies(':no:')\"><img src=images/smilies/no.gif border=0 alt=':no:'></a></td>
<td width=26><a href=\"javascript:Smilies(':wave:')\"><img src=images/smilies/wave.gif border=0 alt=':wave:'></a></td>
<td width=26><a href=\"javascript:Smilies(':ras:')\"><img src=images/smilies/ras.gif border=0 alt=':ras:'></a></td></tr>
<tr><td width=26><a href=\"javascript:Smilies(':sick:')\"><img src=images/smilies/sick.gif border=0 alt=':sick:'></a></td>
<td width=26><a href=\"javascript:Smilies(':yucky:')\"><img src=images/smilies/yucky.gif border=0 alt=':yucky:'></a></td>
<td width=26><a href=\"javascript:Smilies(':rolleyes:')\"><img src=images/smilies/rolleyes.gif border=0 alt=':rolleyes:'></a></td></tr>
</table>
<br><a href=smilies.php target=_blank>[More Smilies]</a><br><br><a href=tags.php target=_blank>[BB Tags]</a></center>";


$torrow = DB::fetchAssoc("SELECT name FROM torrents WHERE id = $id");
if (!$torrow) {
	die('torrent not found!');
}

stdhead("Add a comment to \"" . $torrow["name"] . "\"");

begin_frame("Add a comment to \"" . h($torrow["name"]) . "\"", 'center');
?>
<p>
Please type your comment here, please remember to obey the <a href="rules.php">Rules</a>.
<table border=0 cellpadding=5>
<form name=Form method="post" action="take-comment.php">
<input type="hidden" name="id" value="<?= $id ?>" />
<tr>
<td><?= $quicktags ?></td><td><textarea name="body" rows="10" cols="60"></textarea></td>
</tr>
<tr><td colspan=2><center><input type="submit" class=btn value="Add Comment" /></center></td></tr></table></p>
</form>
<?php

$res = DB::query("SELECT comments.id, text, comments.added, username, users.id as user,
users.avatar, users.title, users.signature, users.downloaded, users.uploaded, users.privacy
FROM comments
    LEFT JOIN users ON comments.user = users.id
WHERE torrent = $id
ORDER BY comments.id ASC");

$allrows = [];
while ($row = $res->fetch())
	$allrows[] = $row;

if (count($allrows)) {
	commenttable($allrows);
}

end_frame();
stdfoot();

