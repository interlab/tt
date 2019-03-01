<?php

dbconn();

loadLanguage();

isNotGuest();

global $SITEURL;

$search = trim(($_GET['search'] ?? ''));
$letter = strip_tags(trim(($_GET['letter'] ?? '')));
$q = '';
$class = $_GET['class'] ?? '';
if ($class == '-' || !is_numeric($class)) {
    $class = '';
}

$queryparams = [];

if ($search != '' || $class) {
    $query = 'u.username LIKE ? AND u.status = ?';
    $queryparams[] = '%'.$search.'%';
    $queryparams[] = 'confirmed';
    if ($search) {
        $q = 'search='.h($search);
    }
} else {
    if (strlen($letter) > 1) {
        die;
    }

    if ($letter === '' || strpos('abcdefghijklmnopqrstuvwxyz', $letter) === false) {
        $query = 'u.status = ?';
        $queryparams[] = 'confirmed';
    } else {
        $query = 'u.username LIKE ? AND u.status = ?';
        $queryparams[] = $letter.'%';
        $queryparams[] = 'confirmed';
    }
    $q = 'letter='.$letter;
}

if ($class !== '') {
    $query .= ' AND u.class = ?';
    $queryparams[] = $class;
    $q .= ($q ? "&amp;" : "")."class=$class";
}

stdhead($txt['USERS']);

echo '
<style>
.td_user_name a {
    font-size: 1.2em;
    font-weight: bold;
    white-space: nowrap;
}
</style>';

begin_frame($txt['MEMBERS'], 'center');
print("<br /><form method=get action=?>".
$txt['SEARCH'].": <input type=text size=30 name=search>
<select name=class>
<option value='-'>(any class)</option>\n");
for ($i = 0;; ++$i) {
    if ($c = get_user_class_name($i)) {
        print("<option value=$i".($class && $class == $i ? " selected" : "").">$c</option>\n");
    } else {
        break;
    }
}
echo '</select>
<input type="submit" value="'.$txt['SEARCH'].'">
</form>

<p>';

echo '
    <a href="'.$SITEURL.'/extras-users.php"><b>'.$txt['ALL'].'</b></a> - ';
for ($i = 97; $i < 123; ++$i) {
    $l = chr($i);
    $L = chr($i - 32);
    if ($l == $letter) {
        print("<b>$L</b>\n");
    } else {
        print("<a href=?letter=$l><b>$L</b></a>\n");
    }
}

echo '
    </p>';

$page = (int) ($_GET['page'] ?? 0);
$perpage = 50;
$pagemenu = '';
$browsemenu = '';

$total = (int) DB::fetchColumn('SELECT COUNT(*) FROM users AS u WHERE ' . $query, $queryparams);

if ($total) {
    $pages = floor($total / $perpage);
    if ($pages * $perpage < $total) {
        ++$pages;
    }

    if ($page < 1) {
        $page = 1;
    } elseif ($page > $pages) {
        $page = $pages;
    }

    for ($i = 1; $i <= $pages; ++$i) {
        if ($i == $page) {
            $pagemenu .= "$i\n";
        } else {
            $pagemenu .= "<a href=?$q&page=$i>$i</a>\n";
        }
    }

    if ($page == 1) {
        $browsemenu .= '';
    }
    //  $browsemenu .= "[Prev]";
    else {
        $browsemenu .= '<a href="?'.$q.'&page='.($page - 1).'">[Prev]</a>';
    }

    $browsemenu .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

    if ($page == $pages) {
        $browsemenu .= '';
    }
    //  $browsemenu .= "[Next]";
    else {
        $browsemenu .= '<a href="?'.$q.'&page='.($page + 1).'">[Next]</a>';
    }

    $offset = ($page * $perpage) - $perpage;

    $sql = '
        SELECT u.*, c.flagpic AS flag_pic, c.name AS flag_name
        FROM users AS u
            LEFT JOIN countries AS c ON (c.id = u.country)
        WHERE ' . $query . '
        ORDER BY u.username
        LIMIT ' . $offset . ', ' . $perpage;

    $res = DB::executeQuery($sql, $queryparams);

    begin_table();
    echo '
    <tr>
    <td class="ttable_head" align="left">'.$txt['AVATAR'].'</td>
    <td class="ttable_head">'.$txt['USERNAME'].'</td>
    <td class="ttable_head">'.$txt['REGISTERED'].'</td>
    <td class="ttable_head">'.$txt['LAST_ACCESS'].'</td>
    <td class="ttable_head">'.$txt['RANK'].'</td>
    <td class="ttable_head">'.$txt['COUNTRY'].'</td>
    </tr>';

    while ($row = $res->fetch(\PDO::FETCH_OBJ)) {
        if ($row->country > 0) {
            $country = '
    <td align="center" class="ttable_col2" style="padding: 0px;" align="center">
        <img src="images/flag/'.$row->flag_pic.'" alt="'.$row->flag_name.'" />
    </td>';
        } else {
            $country = '
    <td align="center"  class="ttable_col1" style="padding: 0px" align="center">
        <img src="images/flag/unknown.gif" alt="Unknown" />
    </td>';
        }
        if ($row->added === '0000-00-00 00:00:00') {
            $row->added = '-';
        }
        if ($row->last_access === '0000-00-00 00:00:00') {
            $row->last_access = '-';
        }

        $avatar = $row->avatar === '' ? '' : '<img src="' . $row->avatar . '" width="60px" height="60px" />';

        echo '
        <tr>
        <td class="ttable_col1" align="center" style="width: 64px; height: 64px;">
        ', $avatar, '
        </td>
        <td align="center" class="ttable_col2 td_user_name">
        <a href="', $SITEURL, '/account-details.php?id=', $row->id, '">', ($row->class > 1 ? '' : ''),
            '<b>', $row->username, '</b></a>',
        ($row->donated > 0 ? '<img src="'.ST_IMG_URL.'/star.gif" border="0" alt="Donated">' : ''),
        '</td>
        <td align="center" class="ttable_col1">', $row->added, '</td>
        <td align="center" class="ttable_col2">', $row->last_access, '</td>
        <td class="ttable_col1" align="center">', get_user_class_name($row->class), '</td>
        ', $country, '
        </tr>';
    }
    end_table();

    echo '<p>', $pagemenu, '<br />', $browsemenu, '</p>';
}

end_frame();
stdfoot();
