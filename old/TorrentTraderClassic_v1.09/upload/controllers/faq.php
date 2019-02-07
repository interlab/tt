<?php
//
// - Theme And Language Updated 27.Nov.05
//

ob_start("ob_gzhandler");
require_once __DIR__ . '/../backend/functions.php';

dbconn(false);

stdhead("FAQ");
begin_frame($txt['FAQ']);

$faq_categ = [];

$res = DB::query("
    SELECT `id`, `question`, `flag`
    FROM `faq`
    WHERE `type` = 'categ'
    ORDER BY `order` ASC");
while ($arr = $res->fetch()) {
    $faq_categ[$arr['id']]['title'] = $arr['question'];
    $faq_categ[$arr['id']]['flag'] = (int) $arr['flag'];
}

$res = DB::query(
    'SELECT `id`, `question`, `answer`, `flag`, `categ`
    FROM `faq`
    WHERE `type`= \'item\'
    ORDER BY `order` ASC');
while ($arr = $res->fetch()) {
    $faq_categ[$arr['categ']]['items'][$arr['id']]['question'] = $arr['question'];
    $faq_categ[$arr['categ']]['items'][$arr['id']]['answer'] = $arr['answer'];
    $faq_categ[$arr['categ']]['items'][$arr['id']]['flag'] = (int) $arr['flag'];
}

if (isset($faq_categ)) {
    // gather orphaned items
    foreach ($faq_categ as $id => $temp) {
        if (!array_key_exists("title", $faq_categ[$id])) {
            foreach ($faq_categ[$id]['items'] as $id2 => $temp) {
                $faq_orphaned[$id2]['question'] = $faq_categ[$id]['items'][$id2]['question'];
                $faq_orphaned[$id2]['answer'] = $faq_categ[$id]['items'][$id2]['answer'];
                $faq_orphaned[$id2]['flag'] = $faq_categ[$id]['items'][$id2]['flag'];
                unset($faq_categ[$id]);
            }
        }
    }

    begin_frame($txt['CONTENTS']);

    foreach ($faq_categ as $id => $temp) {
        if ($faq_categ[$id]['flag'] === 1) {
            echo '
    <ul>
        <li><a href="#', $id, '"><b>', $faq_categ[$id]['title'], '</b></a>
    <ul><br>';
            if (array_key_exists("items", $faq_categ[$id])) {
                foreach ($faq_categ[$id]['items'] as $id2 => $temp) {
                    if ($faq_categ[$id]['items'][$id2]['flag'] === 1) {
                        echo '
        <li><a href="#'.$id2.'" class="altlink">' . $faq_categ[$id]['items'][$id2]['question'] . '</a></li>';
                    } elseif ($faq_categ[$id]['items'][$id2]['flag'] === 2) {
                        echo '
        <li><a href="#'.$id2.'" class="altlink">' . $faq_categ[$id]['items'][$id2]['question'] . '</a> <img src="/images/updated.png" alt="Updated" width="46" height="13" align="absbottom"></li>';
                    } elseif ($faq_categ[$id]['items'][$id2]['flag'] === 3) {
                        echo '
        <li><a href="#'.$id2.'" class="altlink">' . $faq_categ[$id]['items'][$id2]['question'] . '</a> <img src="/images/new.png" alt="New" width="25" height="12" align="absbottom"></li>';
                    }
                }
            }
            echo '
            </ul>
        </li>
    </ul><br>';
        }
    }

    end_frame();

    foreach ($faq_categ as $id => $temp) {
        if ($faq_categ[$id]['flag'] == "1") {
            $frame = $faq_categ[$id]['title']." - <a href=\"#top\">Top</a>";
            begin_frame($frame);
            print("<a name=\"#".$id."\" id=\"".$id."\"></a>\n");
            if (array_key_exists("items", $faq_categ[$id])) {
                foreach ($faq_categ[$id]['items'] as $id2 => $temp) {
                    if ($faq_categ[$id]['items'][$id2]['flag'] > 0) {
                        echo '<br><b>' . $faq_categ[$id]['items'][$id2]['question'] . '</b><a name="#'.$id2.'" id="'.$id2.'"></a><br>
                             <br>' . $faq_categ[$id]['items'][$id2]['answer'] . '<br><br>';
                    }
                }
            }
            end_frame();
        }
    }
}

end_frame();

stdfoot();
