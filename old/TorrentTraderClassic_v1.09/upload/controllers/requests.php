<?php

dbconn();

loggedinorreturn();

if (isset($_GET['sa']) && $_GET['sa'] === 'view') {

    stdhead("Requests Page");
    begin_frame($txt['REQUESTS']);

    if (! $REQUESTSON) {
        echo $txt['REQUESTS_OFFLINE'];
        end_frame();
        stdfoot();
        die('');
    }

    print("<a href=requests.php>Add New Request</a> | <a href=requests.php?sa=view&requestorid=" . $CURUSER['id'] . ">View my requests</a>");

    $categ = $_GET["category"] = (int) ($_GET["category"] ?? 0);
    $requestorid = $_GET["requestorid"] = (int) ($_GET["requestorid"] ?? 0);

    $sort = $_GET["sort"] = $_GET["sort"] ?? '';
    $search = $_GET["search"] = $_GET["search"] ?? '';
    $filter = $_GET["filter"] = $_GET["filter"] ?? '';

    if ($search) {
        $search = " AND requests.request like '%$search%' ";
    }

    if ($sort == "votes")
        $sort = " order by hits desc ";
    else if ($sort == "request")
        $sort = " order by request ";
    else
        $sort = " order by added desc ";


    if ($filter == "true")
        $filter = " AND requests.filledby = 0 ";
    else
        $filter = "";

    if ($requestorid) {
        if ($categ)
            $categ = "WHERE requests.cat = " . $categ . " AND requests.userid = " . $requestorid;
        else
            $categ = "WHERE requests.userid = " . $requestorid;
    } elseif ($categ == 0) {
        $categ = '';
    } else {
        $categ = "WHERE requests.cat = " . $categ;
    }

    $count = DB::fetchColumn("
        SELECT count(requests.id)
        FROM requests
            inner join categories on requests.cat = categories.id
            inner join users on requests.userid = users.id
        $categ
            $filter
            $search");
    $perpage = 50;

    [$pagertop, $pagerbottom, $limit] = pager($perpage, $count,
        'requests.php?sa=view&category=' . $_GET["category"] . '&sort=' . $_GET["sort"] . '&');

    $res = DB::executeQuery("
        SELECT users.downloaded, users.uploaded, users.username, users.privacy,
            requests.filled, requests.filledby, requests.id, requests.userid,
            requests.request, requests.added, requests.hits, categories.name as cat
        FROM requests
            inner join categories on requests.cat = categories.id
            inner join users on requests.userid = users.id
        $categ
        $filter
        $search
        $sort
        $limit");

    print("<br><br><CENTER><form method=get action=requests.php?sa=view>"
        . $txt['SEARCH'] . ": <input type=text size=30 name=search>
        <input type=submit align=center value=" . $txt['SEARCH'] . " style='height: 22px'>
        </form></CENTER><br>");

    echo $pagertop;

    echo "<table border=0 width=100% cellspacing=0 cellpadding=0>
        <TR><TD width=50% align=left valign=bottom>
        <p>" . $txt['SORT_BY']
        . " <a href=requests.php?sa=view&category=" . $_GET['category'] . "&filter=" . $_GET['filter'] .
        "&sort=votes>" . $txt['VOTES'] . "</a>, <a href=".
        "requests.php?sa=view&category=" . $_GET['category'] . "&filter=" . $_GET['filter'] .
        "&sort=request>Request Name</a>, or <a href="
        . "requests.php?sa=view&category=" . $_GET['category'] . "&filter=" . $_GET['filter'] .
        "&sort=added>" . $txt['DATE_ADDED'] . "</a>.</p>";

    print("<form method=get action=requests.php?sa=view>");
    ?>
    </td><td width=100% align=right valign=bottom>
    <select name="category">
    <option value="0"><?= $txt['SHOW_ALL'] ?></option>
    <?php 

    $cats = genrelist();
    $catdropdown = "";
    foreach ($cats as $cat) {
       $catdropdown .= "<option value=\"" . $cat["id"] . "\"";
       $catdropdown .= ">" . h($cat["name"]) . "</option>\n";
    }

    ?>
    <?= $catdropdown ?>
    </select>
    <?php 
    print("<input type=submit align=center value=" . $txt['DISPLAY'] . " style='height: 22px'>
        </form></td></tr></table>");

    echo '<form method=post action=requests.php>
          <input type="hidden" name="sa" value="delete">
          <table width=100% cellspacing=0 cellpadding=3 class=table_table>';
    print("<tr><td class=table_head align=left>" . $txt['REQUESTS'] . "</td>
        <td class=table_head align=center>" . $txt['TYPE'] . "</td>
        <td class=table_head align=center width=150>" . $txt['DATE_ADDED'] . "</td>
        <td class=table_head align=center>" . $txt['ADDED_BY'] . "</td>
        <td class=table_head align=center>" . $txt['FILLED'] . "</td>
        <td class=table_head align=center>" . $txt['FILLED_BY'] . "</td>
        <td class=table_head align=center>" . $txt['VOTES'] . "</td>
        <td class=table_head align=center>" . $txt['DEL'] . "</td></tr>\n");

    while ($arr = $res->fetch()) {
        $privacylevel = $arr["privacy"];

        if ($arr["downloaded"] > 0) {
            $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 2);
            $ratio = "<font color=" . get_ratio_color($ratio) . "><b>$ratio</b></font>";
        }
        else if ($arr["uploaded"] > 0)
           $ratio = "Inf.";
        else
           $ratio = "---";

        // todo: sub query
        $arr2 = DB::fetchAssoc("SELECT username from users where id=" . $arr['filledby']);

        if ($arr2['username'])
            $filledby = $arr2['username'];
        else
            $filledby = " ";     

        if ($privacylevel == "strong") {
            if (get_user_class() >= UC_JMODERATOR) {
                $addedby = "<td class=table_col2 align=center><a href=account-details.php?id=$arr[userid]><b>$arr[username] ($ratio)</b></a></td>";
            } else {
                $addedby = "<td class=table_col2 align=center><a href=account-details.php?id=$arr[userid]><b>$arr[username] (----)</b></a></td>";
            }
        } else {
            $addedby = "<td class=table_col2 align=center><a href=account-details.php?id=$arr[userid]><b>$arr[username] ($ratio)</b></a></td>";
        }

        $filled = $arr['filled'];
        if ($filled) {
            $filled = "<a href=$filled><font color=green><b>Yes</b></font></a>";
            $filledbydata = "<a href=account-details.php?id=$arr[filledby]><b>$arr2[username]</b></a>";
        } else {
            $filled = "<a href=requests.php?details=$arr[id]><font color=red><b>No</b></font></a>";
            $filledbydata  = "<i>nobody</i>";
        }

        print("<tr><td class=table_col1 align=left><a href=requests.php?details=$arr[id]><b>".h($arr['request'])."</b></a></td>" .
            "<td class=table_col2 align=center>$arr[cat]</td>
            <td align=center class=table_col1>$arr[added]</td>
            $addedby
            <td class=table_col2>$filled</td>
            <td class=table_col1>$filledbydata</td>
            <td class=table_col2><a href=votesview.php?requestid=$arr[id]><b>$arr[hits]</b></a></td>");
        if (($CURUSER['id'] == $arr['userid']) || get_user_class() > UC_JMODERATOR) {
            print("<td class=table_col1><input type=\"checkbox\" name=\"delreq[]\" value=\"" . $arr['id'] . "\" /></td>");
        } else {
            print("<td class=table_col1>&nbsp;</td>");
        }
        print("</tr>\n");
    }

    print("</table>
        <p align=right><input type=submit value=" . $txt['DO_DELETE'] . "></p>
        </form>");

    echo $pagerbottom;

    end_frame();

    stdfoot();

    die('');
}

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
if (isset($_POST['sa']) && $_POST['sa'] === 'add') {
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
            'message' => '[url=account-details.php?id=' . $CURUSER['id'] . '][b]' .
                $CURUSER['username'] . '[/b][/url] has made a request for [url='.
                $SITEURL.'/requests.php?details='.$id.']'.$_POST["requesttitle"].'[/url]',
            'date' => date('Y-m-d H:i:s'),
            'userid' => 0
        ]);
    }

    // write_log("$request was added to the Request section");

    header("Refresh: 0; url=requests.php?sa=view");

    die('');
}

if (isset($_POST['sa']) && $_POST['sa'] === 'delete') {
    // dbconn();
    // loggedinorreturn();
    // global $CURUSER;

    stdhead('Delete');
    begin_frame('Delete');

    $delreq = array_map('intval', $_POST['delreq']);

    if (get_user_class() > UC_JMODERATOR) {
        if (empty($_POST['delreq'])) {
            print('<CENTER>You must select at least one request to delete.</CENTER>');
            end_frame();
            stdfoot();
            die;
        }

        $do = 'DELETE FROM requests WHERE id IN (' . implode(', ', $delreq) . ')';
        $do2 = 'DELETE FROM addedrequests WHERE requestid IN (' . implode(', ', $delreq) . ')';
        $res2 = DB::query($do2);
        $res = DB::query($do);
        print('<CENTER>Request Deleted OK</CENTER>');

        echo '<BR><BR>';
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
    print("<BR><BR>Thank you for filling a request :)<br><br><a href=requests.php?sa=view>View More Requests</a>");
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

    print("<center><table width=500 border=0 cellspacing=0 cellpadding=3>
        <tr><td align=left><B>" . $txt['REQUEST'] . ": </B></td>
        <td width=70% align=left>$num[request]</td></tr>");
    if ($num["descr"]) {
        print("<tr><td align=left><B>" . $txt['COMMENTS'] . ": </B></td>
            <td width=70% align=left>$num[descr]</td></tr>");
    }
    print("<tr><td align=left><B>" . $txt['DATE_ADDED']  . ": </B></td>
        <td width=70% align=left>$num[added]</td></tr>");

    $cres = DB::fetchAssoc("SELECT username FROM users WHERE id = $num[userid]");
    if ($cres) {
        $username = "$cres[username]";
    }
    print("<tr><td align=left><B>" . $txt['ADDED_BY'] . ": </B></td>
        <td width=70% align=left>$username</td></tr>
        <tr><td align=left><B>" . $txt['VOTE_FOR_THIS'] . ": </B></td>
        <td width=50% align=left><a href=requests.php?sa=addvote&id=$id><b>" . $txt['VOTES'] . "</b></a></td></tr>");

    if (! $num["filled"]) {
        print("<form method=post action=requests.php>
            <tr><td align=left><B>To Fill This Request:</B> </td>
            <td>Enter the <b>full</b> direct URL of the torrent i.e. http://www.mysite.com/torrents-details.php?id=134 
            (just copy/paste from another window/tab) or modify the existing URL to have the correct ID number</td></tr>
            </table>
            <input type=text size=80 name=url value=TYPE-DIRECT-URL-HERE>
            <input type=hidden value=1 name=filled>
            <input type=hidden value=$id name=id>
            <input type=submit value=Fill Request>\n</form>");
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

    if (($CURUSER['id'] == $arr['userid']) || (get_user_class() >= 4)
         || ($CURUSER['id'] == $arr['filledby'])
    ) {
        DB::query("UPDATE requests SET filled = '', filledby = 0 WHERE id = $id");
        print("Request $id successfully reset.");
    } else {
        print("Sorry, cannot reset a request when you are not the owner");
    }

    end_frame();

    stdfoot();
    die('');
}

if (isset($_GET['sa']) && $_GET['sa'] === 'addvote') {
    stdhead("Vote");

    begin_frame($txt['VOTES']);

    $requestid = (int) ($_GET["id"] ?? 0);
    $userid = (int) ($CURUSER["id"] ?? 0);

    if (!$requestid || !$userid) {
        bark('error', 'bad id');
    }

    $voted = DB::fetchAssoc("SELECT * FROM addedrequests WHERE requestid = $requestid and userid = $userid");

    if ($voted) {
    ?>
    <br><p>You've already voted for this request, only 1 vote for each request is allowed</p>
    <p>Back to <a href=requests.php?sa=view><b>requests</b></a></p>
    <br><br>
    <?php
    } else {
        DB::query("UPDATE requests SET hits = hits + 1 WHERE id = $requestid");
        DB::query("INSERT INTO addedrequests VALUES(0, $requestid, $userid)");

        print("<br><p>Successfully voted for request $requestid</p>"
            . "<p>Back to <a href=requests.php?sa=view><b>requests</b></a></p>"
            . "<br><br>");
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
if (! empty($_GET["incldead"])) {
   $deadchkbox .= " checked=\"checked\"";
}
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
    <input type=hidden value='add' name='sa'>
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
</form>
</table></CENTER>

<?php
end_frame();

stdfoot();
