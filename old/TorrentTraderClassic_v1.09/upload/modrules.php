<?php

require_once('backend/functions.php');
dbconn(false);
loggedinorreturn();
jmodonly();

// todo: delete section

$_GET['act'] = $_GET['act'] ?? '';

// ADD NEW RULE SECTION PAGE/FORM
if ($_GET['act'] === 'newsect') {
    stdhead('Add Rule Section');
    require_once('backend/admin-functions.php');
    adminmenu();
    begin_frame('Add Rule Section');

    echo '
        <form method="post" action="modrules.php?act=addsect">
        <table border="0" cellspacing="0" cellpadding="10" align="center">
        <tr><td>Section Title:</td><td><input style="width: 400px;" type="text" name="title"/></td></tr>
        <tr><td style="vertical-align: top;">Rules:<br><a href=tags.php>[BB Tags]</a></td>
        <td><textarea cols=90 rows=20 name="text"></textarea><br>
        <br><a href=tags.php>[BB Tags]</a> are <b>on</b></td></tr>
        <tr><td colspan="2" align="center">
            <input type="radio" name="public" value="yes" checked>For everybody
            <input type="radio" name="public" value="no">&nbsp;Members Only - 
            (Min User Class: <input type="text" name="class" value="0" size=1>)</td></tr>
        <tr><td colspan="2" align="center"><input type="submit" value="Add" style="width: 60px;"></td></tr>
        </table></form>';
    end_frame();
    stdfoot();
}
// ADD NEW RULE SECTION TO DATABASE
elseif ($_GET['act'] === 'addsect') {
    $title = $_POST['title'];
    $text = $_POST['text'];
    $public = $_POST['public'];
    $class = $_POST['class'];
    DB::executeQuery('insert into rules (title, text, public, class) values(?, ?, ?, ?)',
        [$title, $text, $public, $class]
    );
    header('Refresh: 0; url=modrules.php');
}
// EDIT RULE
elseif ($_GET['act'] === 'edit') {
    $id = (int) ($_POST['id'] ?? 0);
    $res = DB::fetchAssoc('select * from rules where id = ' . $id);
    stdhead('Edit Rules');
    require_once('backend/admin-functions.php');
    adminmenu();
    begin_frame('Edit Rule Section');

    echo '<form method="post" action="modrules.php?act=edited">
    <table border="0" cellspacing="0" cellpadding="10" align="center">
    <tr><td>Section Title:</td>
    <td><input style="width: 400px;" type="text" name="title" value="'.$res['title'].'" /></td></tr>
    <tr><td style="vertical-align: top;">Rules:<br><a href=tags.php>[BB Tags]</a></td>
    <td><textarea cols=90 rows=20 name="text">'
        . $res["text"] . '</textarea><br><a href=tags.php>[BB Tags]</a> are <b>on</b>
    </td></tr>
    <tr><td colspan="2" align="center"><input type="radio" name="public" value="yes" '
        .($res["public"] === "yes" ? "checked" : "")
        .'>For everybody<input type="radio" name="public" value="no" '
        .($res["public"] == "no" ? "checked" : "")
        .'>Members Only (Min User Class: <input type="text" name="class" value="'.$res['class'].'" size=1>)
    </td></tr>
    <tr><td colspan="2" align="center">
        <input type=hidden value=' . $res['id'] .' name=id>
        <input type="submit" value="Save" style="width: 60px;"></td></tr>
    </table>';
    end_frame();
    stdfoot();
}
// DO EDIT RULE, UPDATE DB
elseif ($_GET['act'] === 'edited') {
    $id = (int) $_POST['id'];
    $title = $_POST['title'];
    $text = $_POST['text'];
    $public = $_POST['public'];
    $class = $_POST['class'];
    DB::executeUpdate('update rules set title = ?, text = ?, public = ?, class = ? where id = ?',
        [$title, $text, $public, $class, $id]
    );
    header('Refresh: 0; url=modrules.php');
} else {
    // STANDARD MENU OR HOMEPAGE ETC
    $res = DB::query('select * from rules order by id');
    stdhead();
    require_once('backend/admin-functions.php');
    adminmenu();
    begin_frame('Site Rules Editor');
    echo '<br><table width=100% border=0 cellspacing=0 cellpadding=10>
        <tr><td align=center><a href=modrules.php?act=newsect>Add New Rules Section</a></td></tr></table>';

    begin_frame('Current Rules');

    while ($arr = $res->fetch()) {
        begin_frame($arr['title']);
        echo '
            <form method=post action=modrules.php?act=edit&id=><table width=95% border=0 cellspacing=0>
            <tr><td width=100%>
            ' .format_comment($arr['text']).'
            </td></tr>
            <tr><td>
            <input type=hidden value='.$arr['id'].' name="id">
            <input type=submit value="Edit"></td></tr></table></form>';
        end_frame();
    }

    echo '<br><br>';

    end_frame();

    echo '<br><br>';

    end_frame();

    stdfoot();
}
