<?php

ob_start("ob_gzhandler");
require "backend/functions.php";

dbconn();

loggedinorreturn();


// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $request = $_POST["requesttitle"] ?? '';
    $descr = $_POST["descr"] ?? '';
    $cat = (int) ($_POST["category"] ?? 0);

    if (empty($request)) {
        bark('error', 'Title is empty');
    }

    if (empty($descr)) {
        bark('error', 'Description is empty');
    }

    if (! $cat) {
        bark('error', 'Category not selected');
    }

    DB::insert('requests', [
        'hits' => 1, 'userid' => $CURUSER['id'],
        'cat' => $cat, 'request' => $request,
        'descr' => $descr, 'added' => get_date_time()
    ]);

    $id = DB::lastInsertId();

    DB::insert('addedrequests', ['requestid' => $id, 'userid' => $CURUSER['id']]);

    if ($SHOUTBOX) {
        DB::insert('shoutbox', [
            'user' => 'Request',
            'message' => '[url=account-details.php?id=' . $CURUSER['id'] . '][b]' .$CURUSER['username'] . '[/b][/url] has made a request for [url='.
                $SITEURL.'/requests.php?details='.$id.']'.$_POST["requesttitle"].'[/url]',
            'date' => date('Y-m-d H:i:s'),
            'userid' => 0
        ]);
    }

    // write_log("$request was added to the Request section");

    header("Refresh: 0; url=viewrequests.php");

    die('');
}

if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    // dbconn();
    // loggedinorreturn();
    // global $CURUSER;

    stdhead("Delete");
    begin_frame("Delete");

    $delreq = array_map("intval", $_POST["delreq"]);

    if (get_user_class() > UC_JMODERATOR) {
        if (empty($_POST["delreq"])) {
            print("<CENTER>You must select at least one request to delete.</CENTER>");
            end_frame();
            stdfoot();
            die;
        }

        $do = "DELETE FROM requests WHERE id IN (" . implode(", ", $delreq) . ")";
        $do2 = "DELETE FROM addedrequests WHERE requestid IN (" . implode(", ", $delreq) . ")";
        $res2 = DB::query($do2);
        $res = DB::query($do);
        print("<CENTER>Request Deleted OK</CENTER>");

        echo "<BR><BR>";
    } else {
        foreach ($delreq as $id) {
            $delete_ok = checkRequestOwnership($CURUSER['id'], $id);
            if ($delete_ok) {
                $do = "DELETE FROM requests WHERE id IN ($id)";
                $do2 = "DELETE FROM addedrequests WHERE requestid IN ($id)";
                $res2 = DB::query($do2);
                $res = DB::query($do);
                print("<CENTER>Request ID $del_req Deleted</CENTER>");
            } else {
                print("<CENTER>No Permission to delete Request ID $del_req</CENTER>");
            }
        }
    }

    end_frame();
    stdfoot();

    function checkRequestOwnership($user, $delete_req)
    {
        $num = DB::fetchColumn('SELECT 1 FROM requests WHERE userid = ? AND id = ?',
            [$user, $delete_req]
        );

        return $num > 0;
    }

    die('');
}

// todo: check owner?
if (isset($_POST['filled'])) {
    stdhead("Fill Request");

    begin_frame("Request Filled");

    // todo: check
    $filledurl = $_POST["url"] ?? '';
    $requestid = (int) ($_POST["id"] ?? 0);

    $arr = DB::fetchAssoc("
        SELECT users.username, requests.userid, requests.request
        FROM requests
            inner join users on requests.userid = users.id
        where requests.id = $requestid");
    // dump($arr);
    $arr2 = DB::fetchAssoc("SELECT username FROM users where id = " . $CURUSER['id']);


    $msg = "Your request, [url=$SITEURL/requests.php?req-details=1&id=" . $requestid . "][b]" . $arr['request'] .
     "[/b][/url], has been filled by [url=$SITEURL/account-details.php?id=" . $CURUSER['id'] . "][b]" . $arr2['username'] .
     "[/b][/url]. You can download your request from [url=" . $filledurl. "][b]" . $filledurl.
     "[/b][/url].  Please do not forget to leave thanks where due.  If for some reason this is not what you requested, 
     please reset your request so someone else can fill it by following [url=$SITEURL/requests.php?reset=1&requestid=" .
     $requestid . "]this[/url] link.  Do [b]NOT[/b] follow this link unless you are sure that this does not match your request.";

    DB::query("UPDATE requests SET filled = '$filledurl', filledby = $CURUSER[id] WHERE id = $requestid");
    DB::insert('messages', [
        'poster' => 0,
        'sender' => 0,
        'receiver' => $arr['userid'],
        'added' => get_date_time(),
        'msg' => $msg
    ]);

    print("<br><BR><div align=left>Request $requestid successfully filled with <a href=$filledurl>$filledurl</a>. 
        User <a href=account-details.php?id=$arr[userid]><b>$arr[username]</b></a> automatically PMd.<br>
    Filled that accidently? No worries, <a href=requests.php?reset=1&requestid=$requestid>CLICK HERE</a> to mark the request as unfilled. 
    Do <b>NOT</b> follow this link unless you are sure there is a problem.<br><BR></div>");
    print("<BR><BR>Thank you for filling a request :)<br><br><a href=viewrequests.php>View More Requests</a>");
    end_frame();

    stdfoot();

    die('');
}


if (isset($_GET['details'])) {
    // loggedinorreturn();

    stdhead("Request Details");
    $id = (int) ($_GET['details'] ?? 0);
    $num = DB::fetchAssoc("SELECT * FROM requests WHERE id = $id");
    if (! $num) {
        stderr($txt['ID_NOT_FOUND'], "That request doesn't exist.");
    }

    $s = $num["request"];

    begin_frame("Request: $s");

    print("<center><table width=500 border=0 cellspacing=0 cellpadding=3>\n");
    print("<tr><td align=left><B>" . $txt['REQUEST'] . ": </B></td><td width=70% align=left>$num[request]</td></tr>");
    if ($num["descr"])
    print("<tr><td align=left><B>" . $txt['COMMENTS'] . ": </B></td><td width=70% align=left>$num[descr]</td></tr>");
    print("<tr><td align=left><B>" . $txt['DATE_ADDED']  . ": </B></td><td width=70% align=left>$num[added]</td></tr>");

    $cres = DB::fetchAssoc("SELECT username FROM users WHERE id = $num[userid]");
    if ($cres) {
        $username = "$cres[username]";
    }
    print("<tr><td align=left><B>" . $txt['ADDED_BY'] . ": </B></td>
        <td width=70% align=left>$username</td></tr>");
    print("<tr><td align=left><B>" . $txt['VOTE_FOR_THIS'] . ": </B></td>
        <td width=50% align=left><a href=addrequest.php?id=$id><b>" . $txt['VOTES'] . "</b></a></tr></tr>");

    if (! $num["filled"]) {
        print("<form method=post action=requests.php>");
        print("<tr><td align=left><B>To Fill This Request:</B> </td>
            <td>Enter the <b>full</b> direct URL of the torrent i.e. http://www.mysite.com/torrents-details.php?id=134 
            (just copy/paste from another window/tab) or modify the existing URL to have the correct ID number</td></tr>");
        print("</table>");
        print("<input type=text size=80 name=url value=TYPE-DIRECT-URL-HERE>\n");
        print("<input type=hidden value=1 name=filled>");
        print("<input type=hidden value=$id name=id>");
        print("<input type=submit value=Fill Request >\n</form>");
    }

    print("<p><hr></p><form method=get action=requests.php#add>OR <input type=submit value=\"Add A New Request\"></form></center></table>");
    end_frame();
    stdfoot();
}
    
    
if (isset($_GET['reset'])) {
    // loggedinorreturn();

    stdhead("Reset Request");

    begin_frame("Reset");

    $id = (int) ($_GET["requestid"] ?? 0);

    $arr = DB::fetchAssoc("SELECT userid, filledby FROM requests WHERE id = $id");

    if (! $arr) {
        bark('Error', 'Request not found.');
    }

    if (($CURUSER['id'] == $arr['userid']) || (get_user_class() >= 4) || ($CURUSER['id'] == $arr['filledby'])) {
        DB::query("UPDATE requests SET filled = '', filledby = 0 WHERE id = $id");
        print("Request $id successfully reset.");
    } else {
        print("Sorry, cannot reset a request when you are not the owner");
    }

    end_frame();

    stdfoot();
    die('');
}

stdhead("Requests Page");

begin_frame($txt['MAKE_REQUEST']);

print("<br>\n");

$_GET["cat"] = (int) ($_GET["cat"] ?? 0);

?>

<table border=0 width=100% cellspacing=0 cellpadding=3>
<tr><td class=colhead align=left><?= $txt['SEARCH'] . ' ' . $txt['TORRENT'] ?></td></tr>
<tr><td align=left><form method="get" action=torrents-search.php>
<input type="text" name="search" size="40" value="" />
in
<select name="cat">
<option value="0">(all types)</option>
<?php

$cats = genrelist();
$catdropdown = "";
foreach ($cats as $cat) {
   $catdropdown .= "<option value=\"" . $cat["id"] . "\"";
   if ($cat["id"] == $_GET["cat"])
       $catdropdown .= " selected=\"selected\"";
   $catdropdown .= ">" . h($cat["name"]) . "</option>\n";
}

$deadchkbox = "<input type=\"checkbox\" name=\"incldead\" value=\"1\"";
if ($_GET["incldead"])
   $deadchkbox .= " checked=\"checked\"";
$deadchkbox .= " /> " . $txt['INC_DEAD'] . "\n";

?>
<?= $catdropdown ?>
</select>
<?= $deadchkbox ?>
<input type="submit" value="<?= $txt['SEARCH'] ?>"  />
</form>
</td></tr></table><BR><HR><BR>

<br>

<form method=post action="requests.php">
<CENTER><table border=0 width=600 cellspacing=0 cellpadding=3>
<tr><td class=colhead align=center><B><?= $txt['MAKE_REQUEST'] ?></B></a></td><tr>
<tr><td align=center><b>Title: </b><input type=text size=40 name=requesttitle>

<select name="category">
<option value="0">(Select a Category)</option>
<?php

$res2 = DB::query("SELECT id, name FROM categories order by name");

$catdropdown2 = "";
while ($cats2 = $res2->fetch()) {  
    $catdropdown2 .= "<option value=\"" . $cats2["id"] . "\"";
    $catdropdown2 .= ">" . h($cats2["name"]) . "</option>\n";
}

?>
<?= $catdropdown2 ?>
</select>

<br>
<tr><td align=center>Additional Information (Optional)
<br><textarea name=descr rows=7 cols=60></textarea></td></tr>
<tr><td align=center><input type=submit value="<?= $txt['SUBMIT'] ?>" style="height: 22px"></td></tr>
<input type=hidden value='add' name=action>
</form>
</table></CENTER>

<?php
end_frame();

stdfoot();
