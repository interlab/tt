<?php

function dbainsert ($table, $a1)
{
    global $errors;

    $q = 'INSERT INTO `' . $table . '` (';
    $n = '1';
    foreach ($a1 as $k => $v) {
        $q .= '`' . $k . '`';
        if ($n != count($a1)) {
            $q .= ',';
        }
        $n++;
    }
    $q .= ') VALUES (';
    $n = '1';
    foreach ($a1 as $k => $v) {
        $q .= '\'' . $v . '\'';
        if ($n != count($a1)) {
            $q .= ', ';
        }
        $n++;
    }
    $q .= ')';
   
    return (bool) DB::executeUpdate($q);
}

adminmenu(); // show menu
//output
begin_frame("News Settings", 'center');
if ($_POST['submit'] != 'Update Settings') {
    $query = 'SELECT * FROM news_options';
    $res = DB::query($query);
    while ($row = $res->fetch()) {
        ?>
        <form id="esetting" name="esetting" method="POST" action="./admin.php?act=news">
        <br>----------------------Global Options--------------------------<br>
        <B>Max News In Index:</b>
        <input type='text' name='maxdisplay' value='<?= $row['max_display'] ?>' maxlength='2' size='2'>
        <br><B>Enable News Comments: </b>
        <input type="checkbox" name="comments"<?= $row['comment'] === 'on' ? ' CHECKED' : '' ?>>
        <br><B>Enable News Archiving: </b>
        <input type="checkbox" name="archive"<?= $row['archive'] == "on" ? " CHECKED" : '' ?>>
        <br><br>----------------------Scrolling Options--------------------------<br>
        <br><B>Enable News Scrolling: </b>
        <input type="checkbox" name="scrolling"<?= $row['scrolling'] == "on" ? " CHECKED" : '' ?>>
        <br><B>Scroll Speed (1-10 1=slowest):</b>
        <input type='text' name='sspeed' value='<?= $row['sspeed'] ?>' maxlength='2' size='2'>
        <br><B>Title Size:</b>
        <input type='text' name='titles' value='<?= $row['titles'] ?>' maxlength='2' size='2'>
        <br><B>Posted by size:</b>
        <input type='text' name='subs' value='<?= $row['subs'] ?>' maxlength='2' size='2'>
        <br><B>Posted by colour:</b>
        <input type='text' name='subc' value='<?= $row['subc'] ?>' maxlength='15' size='15'>
        <br><br><input type="submit" name="submit" value="Update Settings">
        <?php
        //  if($scrolling == "on")
    }
} else {
    $query = "UPDATE news_options SET max_display='{$_POST['maxdisplay']}', scrolling='$scrolling',
        comment='$comments', archive='$archive', titles='{$_POST['titles']}',
        subs='{$_POST['subs']}', subc='{$_POST['subc']}', sspeed='{$_POST['sspeed']}'";
    if (DB::executeUpdate($query)) {
        echo 'Settings Updated!<br>';
        $query = 'SELECT * FROM news_options';
        $res = DB::query($query);
        while ($row = $res->fetch()) {
            ?>
            <form id="esetting" name="esetting" method="POST" action="./admin.php?act=news">
            <br>----------------------Global Options--------------------------<br>
            <B>Max News In Index:</b>
            <input type='text' name='maxdisplay' value='<?=$row['max_display']?>' maxlength='2' size='2'>
            <br><B>Enable News Comments: </b>
            <input type="checkbox" name="comments"<?= $row['comment'] == "on" ? " CHECKED" : '' ?>>
            <br><B>Enable News Archiving: </b>
            <input type="checkbox" name="archive"<?= $row['archive'] == "on" ? " CHECKED" : '' ?>>
            <br><br>----------------------Scrolling Options--------------------------<br>
            <br><B>Enable News Scrolling: </b>
            <input type="checkbox" name="scrolling"<?= $row['scrolling'] == "on" ? " CHECKED" : '' ?>>
            <br><B>Scroll Speed (1-10 1=slowest):</b>
            <input type='text' name='sspeed' value='<?= $row['sspeed'] ?>' maxlength='2' size='2'>
            <br><B>Title Size:</b>
            <input type='text' name='titles' value='<?= $row['titles'] ?>' maxlength='2' size='2'>
            <br><B>Posted by size:</b>
            <input type='text' name='subs' value='<?= $row['subs'] ?>' maxlength='2' size='2'>
            <br><B>Posted by colour:</b>
            <input type='text' name='subc' value='<?= $row['subc'] ?>' maxlength='15' size='15'>
            <br><br><input type="submit" name="submit" value="Update Settings">
            <?php
        }
    } else {
        echo 'DB update ERROR in line: ' . __LINE__;
    }
}
end_frame();
print("</form>");

begin_frame("Add News", 'center');
$form = '
<form id="anews" name="anews" method="POST" action="' . $_SERVER['php_self'] . '?act=news">
    <span style="font: bold 11px sans-serif; letter-spacing: 2px;">Title:</span>
    <br><input type="text" name="title" id="title" size="50" maxlength="255" value="Title">
    <br><span style="font: bold 11px sans-serif; letter-spacing: 2px;">News Text:</span><br>
    <textarea rows="10" cols="75" name="text" id="text">News Text</textarea><br><br>
    <input type="submit" name="submit" value="Add News">
</form>';

// new news
if ($_POST['submit'] != 'Add News') {
    // No form
    echo $form;
} else {
    $news = str_replace("\n", '<br>', $_POST['text']);
    $v = [
        'title' => $_POST['title'],
        'user'  => $CURUSER['username'],
        'date'        => date("F j, g:i a"),
        'text'        => $news
    ];
    if (!dbainsert('news', $v)) {
        'Could not insert, ERROR in line: ' . __LINE__;
        exit;
    }
    echo '<b>News has been added</b><br>';
    echo $form;
}

end_frame();
print("</form>");

begin_frame("Edit News", 'center');

if ($_POST['submit'] != 'Edit News' && $_POST['submit'] != 'Delete') {
    // No Results
   echo $_POST['submit'];
} elseif ($_POST['submit'] == 'Edit News' || $_POST['submit'] == 'Delete') {
    if ($_POST['submit'] == 'Edit News') {
    // Edit Ahoy!
    $news = str_replace("\n", '<br>', $_POST['text']);

    $news = str_replace($smileys1, $smileys2, $news);
    $query = "UPDATE news SET title = '{$_POST['title']}', text = '{$news}', date = '" . date("F j, g:i a") . "' WHERE id='{$_POST['id']}'";
    if (DB::executeUpdate($query)) {
        echo 'News updated!';
    } else {
        echo 'ERROR in line: ' . __LINE__;
    }
} elseif ($_POST['submit'] == 'Delete') {
    $query = "DELETE FROM news WHERE id = '{$_POST['id']}'";
    if (mysql_query($query))
        echo 'News Deleted!';
    }
}

$query = 'SELECT max_display FROM news_options';
$res = DB::query($query);
while ($row = $res->fetch()) {
    $query = 'SELECT id, title, text FROM news ORDER BY id DESC LIMIT ' . $row['max_display'] . '';
    $res = DB::query($query);
    while ($row = $res->fetch()) {
        $news = str_replace("<br>", "\n", $row['text']);
        ?>
        <form id="anews" name="anews" method="POST" action="<?= $_SERVER['php_self'] ?>?act=news">
        <input type="text" name="title" id="title" size="50" maxlength="255" value="<?= $row['title'] ?>"><br><br>
        <textarea rows="10" cols="75" name="text" id="text"><?= $news ?></textarea><br><br>
        <input type="hidden" name="id" value="<?= $row['id'] ?>">
        <input type="submit" name="submit" value="Edit News"> <input type="submit" name="submit" value="Delete">
        </form>
        </div>
        <?php
    }

    end_frame();
    print("</form>");
}
