<?php

require 'backend/functions.php';
dbconn(false);
loggedinorreturn();
stdhead();
begin_frame('News Archive');

$opt = DB::fetchColumn('SELECT archive FROM news_options');

if ($opt == 'on') {
    $res = DB::query('SELECT id, title, user, date, text FROM news ORDER BY date DESC');
    while ($row = $res->fetch()) {
        begin_frame($row['title']);
        echo '<I>Posted By ' . $row['user'] . '</i> On ' . $row['date'] . '<BR>' . $row['text'];
        end_frame();
    }
} else {
     begin_frame('Error');
     print('News Archiving Has Been Disabled!');
     end_frame();
}

end_frame();
stdfoot();
