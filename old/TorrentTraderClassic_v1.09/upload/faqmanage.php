<?php

require "backend/functions.php";
dbconn(false);
loggedinorreturn();
ob_start("ob_gzhandler");
stdhead("FAQ Management");
require_once("backend/admin-functions.php");
adminmenu();
begin_frame();

jmodonly();

print("<h1 align=\"center\">FAQ Management</h1>");

$pagerbottom = '';

// make the array that has all the faq in a nice structured
$res = DB::query("SELECT `id`, `question`, `flag`, `order` FROM `faq` WHERE `type`='categ' ORDER BY `order` ASC");
while ($arr = $res->fetch()) {
    $faq_categ[$arr['id']]['title'] = $arr['question'];
    $faq_categ[$arr['id']]['flag'] = $arr['flag'];
    $faq_categ[$arr['id']]['order'] = $arr['order'];
}

$res = DB::query("SELECT `id`, `question`, `flag`, `categ`, `order` FROM `faq` WHERE `type`='item' ORDER BY `order` ASC");
while ($arr = $res->fetch()) {
    $faq_categ[$arr['categ']]['items'][$arr['id']]['question'] = $arr['question'];
    $faq_categ[$arr['categ']]['items'][$arr['id']]['flag'] = $arr['flag'];
    $faq_categ[$arr['categ']]['items'][$arr['id']]['order'] = $arr['order'];
}

if (isset($faq_categ)) {
// gather orphaned items
    foreach ($faq_categ as $id => $temp) {
        if (!array_key_exists("title", $faq_categ[$id])) {
            foreach ($faq_categ[$id]['items'] as $id2 => $temp) {
                $faq_orphaned[$id2]['question'] = $faq_categ[$id]['items'][$id2]['question'];
                $faq_orphaned[$id2]['flag'] = $faq_categ[$id]['items'][$id2]['flag'];
                unset($faq_categ[$id]);
            }
        }
    }

    // print the faq table
    print("<form method=\"post\" action=\"faqactions.php?action=reorder\">");

    foreach ($faq_categ as $id => $temp) {
        print("<br />\n<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" width=\"95%\">\n");
        print("<tr><td class=\"colhead\" align=\"center\" colspan=\"2\">Position</td>
            <td class=\"colhead\" align=\"left\">Section/Item Title</td>
            <td class=\"colhead\" align=\"center\">Status</td>
            <td class=\"colhead\" align=\"center\">Actions</td></tr>\n");

        print("<tr><td align=\"center\" width=\"40px\"><select name=\"order[". $id ."]\">");
        for ($n=1; $n <= count($faq_categ); $n++) {
            $sel = ($n == $faq_categ[$id]['order']) ? " selected=\"selected\"" : "";
            print("<option value=\"$n\"". $sel .">". $n ."</option>");
        }
        $status = ($faq_categ[$id]['flag'] == "0") ? "<font color=\"red\">Hidden</font>" : "Normal";
        print("</select></td><td align=\"center\" width=\"40px\">&nbsp;</td>
            <td><b>". $faq_categ[$id]['title'] ."</b></td>
            <td align=\"center\" width=\"60px\">". $status ."</td>
            <td align=\"center\" width=\"60px\"><a href=\"faqactions.php?action=edit&id=". $id ."\">edit</a> 
            <a href=\"faqactions.php?action=delete&id=". $id ."\">delete</a></td></tr>\n");

        if (array_key_exists("items", $faq_categ[$id])) {
            foreach ($faq_categ[$id]['items'] as $id2 => $temp) {
                print("<tr><td align=\"center\" width=\"40px\">&nbsp;</td>
                    <td align=\"center\" width=\"40px\"><select name=\"order[". $id2 ."]\">");
                for ($n=1; $n <= count($faq_categ[$id]['items']); $n++) {
                    $sel = ($n == $faq_categ[$id]['items'][$id2]['order']) ? " selected=\"selected\"" : "";
                    print("<option value=\"$n\"". $sel .">". $n ."</option>");
                }
                if ($faq_categ[$id]['items'][$id2]['flag'] == "0") $status = "<font color=\"#FF0000\">Hidden</font>";
                elseif ($faq_categ[$id]['items'][$id2]['flag'] == "2") $status = "<font color=\"#0000FF\">Updated</font>";
                elseif ($faq_categ[$id]['items'][$id2]['flag'] == "3") $status = "<font color=\"#008000\">New</font>";
                else $status = "Normal";
                print("</select></td><td>". $faq_categ[$id]['items'][$id2]['question'] ."</td>
                    <td align=\"center\" width=\"60px\">". $status ."</td>
                    <td align=\"center\" width=\"60px\"><a href=\"faqactions.php?action=edit&id=". $id2 ."\">edit</a> 
                    <a href=\"faqactions.php?action=delete&id=". $id2 ."\">delete</a></td></tr>\n");
            }
        }

        print("<tr><td colspan=\"5\" align=\"center\"><a href=\"faqactions.php?action=additem&inid="
            . $id ."\">Add new item</a></td></tr>\n");
        print("</table>\n");
    }
}

// print the orphaned items table
if (isset($faq_orphaned)) {
    print("<br />\n<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" width=\"95%\">\n");
    print("<tr><td align=\"center\" colspan=\"3\"><b style=\"color: #FF0000\">Orphaned Items</b></td>\n");
    print("<tr><td class=\"colhead\" align=\"left\">Item Title</td>
        <td class=\"colhead\" align=\"center\">Status</td>
        <td class=\"colhead\" align=\"center\">Actions</td></tr>\n");
    foreach ($faq_orphaned as $id => $temp) {
        if ($faq_orphaned[$id]['flag'] == "0") $status = "<font color=\"#FF0000\">Hidden</font>";
        elseif ($faq_orphaned[$id]['flag'] == "2") $status = "<font color=\"#0000FF\">Updated</font>";
        elseif ($faq_orphaned[$id]['flag'] == "3") $status = "<font color=\"#008000\">New</font>";
        else $status = "Normal";
        print("<tr><td>". $faq_orphaned[$id]['question'] ."</td><td align=\"center\" width=\"60px\">". $status ."</td>
            <td align=\"center\" width=\"60px\"><a href=\"faqactions.php?action=edit&id=". $id ."\">edit</a> 
            <a href=\"faqactions.php?action=delete&id=". $id ."\">delete</a></td></tr>\n");
    }
    print("</table>\n");
}

print("<br />\n<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" width=\"95%\">\n
    <tr><td align=\"center\"><a href=\"faqactions.php?action=addsection\">Add new section</a></td></tr>\n</table>\n");
print("<p align=\"center\"><input type=\"submit\" name=\"reorder\" value=\"Reorder\"></p>\n");
print("</form>\n");
print("When the position numbers don't reflect the position in the table, it means the order id is bigger "
    ."than the total number of sections/items and you should check all the order id's in the table and click \"reorder\"\n");
echo $pagerbottom;

end_frame();
stdfoot();
