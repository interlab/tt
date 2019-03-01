<?php

dbconn(false);

loggedinorreturn();

stdhead('Smilies');

$ss_uri = getThemeUri();

require_once TT_THEMES_DIR . '/' . $ss_uri . '/block.php';

insert_smilies_frame();

stdfoot();

function insert_smilies_frame()
{
    global $smilies;

    begin_frame('Smilies', true);

    echo '
    <table align=center cellpadding="0" cellspacing="0" class="ttable_headouter" width=100%>
        <tr><td>
        <table style="border: 1px solid #D1D7DC; border-collapse: collapse; width: 100%;" class="ttable_headinner">
        <tr><td class=colhead>Было...</td><td class=colhead>Стало...</td></tr>';

    foreach ($smilies as $code => $url) {
        echo '
        <tr style="border: 1px solid #6E8DC2;"><td>', $code, '</td><td><img src="', TT_IMG_URL, '/smilies/', $url, '"></td>';
    }

    echo '
            </table>
        </td></tr>
    </table>';

    end_frame();
}
