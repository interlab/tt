<?php

header("Content-Type: text/html; charset=UTF-8");

dbconn();

// Only for logged users
loggedinorreturn();

loadLanguage();

global $CURUSER;

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $sent = (int) ($_POST["sent"] ?? 0);
    $adelids = explode(',', $_POST["delids"]);
    foreach ($adelids as $mid) {
        $id = (int) $mid;
        if ($sent === 0) {
            $do = DB::update('messages', ['deleted_by_receiver' => 1], ['id' => $id, 'receiver' => $CURUSER["id"]]);
        } else {
            $do = DB::update('messages', ['deleted_by_sender' => 1], ['id' => $id, 'sender' => $CURUSER["id"]]);
        }
    }
    // todo: delete all pm if deleted_by_receiver = 0 AND deleted_by_sender = 0
    die;
}

$sent = (int) ($_GET["sent"] ?? 0);

$_GET["get"] = $_GET["get"] ?? '';

if ($_GET["get"] === 'number') {
    if ($sent == 0) {
        $sql = '
            SELECT COUNT(id)
            FROM messages
            WHERE receiver = ' . $CURUSER["id"] . '
                AND deleted_by_receiver = 0';
    } else {
        $sql = '
            SELECT COUNT(id)
            FROM messages
            WHERE sender = ' . $CURUSER["id"] . '
                AND deleted_by_sender = 0';
    }

    $count = DB::fetchColumn($sql);
    ?>
    ofMsg = <?= $count ?>;
    <?php
    die;
}

$start = (int) ($_GET["start"] ?? 0);

if ($sent === 0) {
    $sql = "
    SELECT *, UNIX_TIMESTAMP(added) as utadded
    FROM messages
    WHERE receiver = ".$CURUSER["id"]."
        AND deleted_by_receiver = 0
    ORDER BY added DESC
    LIMIT $start, 20";
} else {
    $sql = '
    SELECT *, UNIX_TIMESTAMP(added) as utadded
    FROM messages
    WHERE sender = '.$CURUSER['id'].'
        AND deleted_by_sender = 0
    ORDER BY added DESC
    LIMIT ' . $start . ', 20';
}

$res = DB::query($sql);
if (!$res) {
    print("<br /><p align=center><b>Нет сообщений</b></p>");
} else {
    ?>
    <div width='100%' align='right' style='padding: 3px'>
    <input value="Выбрать все" onclick="selAll();" type="button" class="inputbt">
    </div>
    <?php

    while ($arr = $res->fetch()) {
        if ($sent === 0) {
            if (is_valid_id($arr["sender"])) {
                $arr2 = DB::fetchAssoc("
    SELECT username
    FROM users
    WHERE id = ".$arr["sender"]);
                $sender = "<a href=account-details.php?id=".$arr["sender"].">".$arr2["username"]."</a>";
            } else {
                $sender = "System";
            }
        } else {
            $arr2 = DB::fetchAssoc("
    SELECT username
    FROM users
    WHERE id = ".$arr["receiver"]);
            $receiver = "<a href=account-details.php?id=".$arr["receiver"].">".$arr2["username"]."</a>";
        }

        print("<table border=0 width=100% cellspacing=0 cellpadding=2>
        <tr><td class='forumtab'><img border='0' src='".TT_IMG_URL."/envelope.gif'></td>
        <td width='70%' class='forumtab'>");
        if ($sent == 0) {
            echo $txt['FROM2'] . ' <b>' . $sender . '</b> ';
        } else {
            echo $txt['FOR1'] . ' <b>' . $receiver . '</b> ';
        }

        print($txt['AT'].get_date_time($arr["utadded"], $CURUSER['tzoffset']));
        if (($sent == 0) && ($arr["unread"] == "yes")) {
            print("<b>(<font color=red>".$txt['ACCOUNT_NEW']."</font>)</b>");
            DB::query("
    UPDATE messages
        SET unread = 'no'
    WHERE id = " . $arr["id"]) or die("arghh");
        }
        print("</td><td class='forumtab' width='30%' align='right'>");
        if (($sent == 0) && ($arr["sender"] != "0")) {
            print("<a href=account-inbox.php?receiver=".$arr["sender"]."&replyto=".$arr["id"].">".$txt['ACCOUNT_REPLY']."</a> | ");
        }
        print("<INPUT type='checkbox' name='del-my-pm' value='".$arr['id']."'></td></tr>");
        print("<tr><td colspan=3>");
        print(format_comment($arr["msg"], '', true));
        print("<br />"
                ."<br /></td></tr></table>");
    }
}
