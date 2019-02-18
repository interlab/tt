
<script>
function toggle(nome) {
    if (document.getElementById(nome).style.display=='none')
    {
        document.getElementById(nome).style.display = '';
        document.getElementById(nome+"img").src="/images/noncross.gif";
    } else {
        document.getElementById(nome).style.display = 'none';
        document.getElementById(nome+"img").src="/images/cross.gif";
    }
}
</script>
<table algin="center" width="98%" cellpadding="2" cellspacing="2">
<tr>
<td>

<?php

// todo: news settings merge with settings
$opts = DB::fetchAssoc('SELECT max_display, comment FROM news_options');

$res = DB::fetchAll('
    SELECT news.id, news.title, news.text, news.user, news.date, news.comments,
        users.username
    FROM news, users
    WHERE users.username = news.user
    ORDER BY news.id DESC
    LIMIT ' . $opts['max_display']
);

$nid_pedido = false; // ???
if ($res) {
    $mostrar = !$nid_pedido ? '' : 'none';
    $img = 'noncross';

    foreach ($res as $n) {
        $nid = $n['id'];
        if ($nid_pedido == $nid)
            $mostrar = '';
        $uid = $n['user'];
        $title = $n['title'];
        $date = $n['date'];
        $text = $n['text'];
        $username = $n['username'];
        echo '
            <a id="', $nid, '"></a>
            <a style="cursor: hand;" onClick="toggle(\'n', $nid, '\');">
<img id="n', $nid, 'img" src="/images/', $img, '.gif">
<strong> ', $title, '</strong></a> (By ', $username, '</a> at <em>', $date, '</em>)</a>
            <div id="n', $nid, '" style="display:', $mostrar, '">
            <table align="center" width="95%"><tr><td >', $text, '';

        if ($opts['comment'] == 'on') {
            echo '
    <br><a href="./show-archived.php?id=', $n['id'], '">Прокомментировать. Всего комментариев: ', $n['comments'], ' ';
        }

        echo '
			</td></tr></table></div><br>';

        $mostrar = 'none';
        $img = 'cross';
    }
} else {
	echo "<center><font color=red>Нет новостей!</font></center>";
}

echo '

</td>
</tr>
</table>

<a href=news-archive.php>View Archive</a>';

