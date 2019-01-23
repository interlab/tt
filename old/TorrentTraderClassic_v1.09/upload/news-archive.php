<?php

require "backend/functions.php";
dbconn(false);
loggedinorreturn();
stdhead();
begin_frame("News Archive");

$opt = DB::fetchColumn('SELECT archive FROM news_options');

if ($opt == 'on'){

    $res = DB::query('SELECT id, title, user, date, text FROM news ORDER BY date DESC');
    while ($row = $res->fetch(\PDO::FETCH_ASSOC)) {
        begin_frame("" . $row['title'] . "");
        print("<I>Posted By " . $row['user'] . "</i> On " . $row['date'] . "\n");
        echo'<BR>' . stripslashes($row['text']) . '';
        end_frame();
    }
}
else {
     begin_frame("Error");
     print("News Archiving Has Been Disabled!");
     end_frame();
}

end_frame();
stdfoot();
