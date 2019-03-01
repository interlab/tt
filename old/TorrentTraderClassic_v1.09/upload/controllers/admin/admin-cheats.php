<?php

dbconn(true);

stdhead("BIG Uploaders");
loggedinorreturn();

jmodonly();

$daysago = $_POST['daysago'] ?? '';
$megabts = $_POST['megabts'] ?? '';

if ($daysago && $megabts) {
    $timeago = 84600 * $daysago; // last 7 days
    $bytesover = 1048576 * $megabts; // over 500MB Upped
    $timenow = time() - $timeago; 

    $result = DB::fetchAll('
        SELECT *
        FROM users
        WHERE UNIX_TIMESTAMP(' . get_dt_num() . ') - UNIX_TIMESTAMP(added) < '.$timeago.'
            AND status = \'confirmed\'
            AND uploaded > '.$bytesover.'
        ORDER BY uploaded DESC'); 
    $num = count($result); // how many uploaders

    begin_frame("Big Uploaders");
    echo "<p>" . $num . " Users with found over last "
        .$daysago." days with more than ".$megabts." MB ("
        .$bytesover.") Bytes Uploaded.</p>";

    if ($num > 0) {
        echo '<table align=center class=table_table>
        <tr>
        <td class=table_head>No.</td>
        <td class=table_head>' . USERNAME . '</td>
        <td class=table_head>' . UPLOADED . '</td>
        <td class=table_head>' . DOWNLOADED . '</td>
        <td class=table_head>' . RATIO . '</td>
        <td class=table_head>' . TORRENTS_POSTED . '</td>
        <td class=table_head>AVG Daily Upload</td>
        <td class=table_head>' . ACCOUNT_SEND_MSG . '</td>
        <td class=table_head>Joined</td>
        </tr>';

        $i = 0;
        foreach ($result as $row) {
            $uploaded = $row['uploaded'];
            $downloaded = $row['downloaded'];

            $joindate = get_elapsed_time(sql_timestamp_to_unix_timestamp($row['added'])) . " ago";
            // get uploader torrents activity

            $torrentinfo = DB::fetchAssoc('SELECT added FROM torrents WHERE owner = ' . $row['id']);
            $numtorrents = $torrentinfo ? 1 : 0;

            // $dayUpload   = $user["uploaded"];
            // $dayDownload = $user["downloaded"];

            $seconds = mkprettytime(strtotime("now") - strtotime($row['added']));
            $days = explode("d ", $seconds);
            if (sizeof($days) > 1) {
                $dayUpload  = $uploaded / $days[0];
                $dayDownload = $downloaded / $days[0];
            }
 
            if ($downloaded > 0) {
                $ratio = $uploaded / $downloaded;
                $ratio = number_format($ratio, 3);
                $color = get_ratio_color($ratio);
                if ($color)
                    $ratio = "<font color=$color>$ratio</font>";
            }
            elseif ($uploaded > 0)
                $ratio = "Inf.";
            else
                $ratio = "---";

            // get donor
            if ($dated >= "1")
                $star = "<img src=images/star.gif>";
            else
                $star = "";

            // get warned
            if ($row['warned'] === "yes")
                $klicaj = "<img src=images/warned.gif>";
            else
                $klicaj = "";

            $counter = $i + 1;
            $i++;

            echo "<tr>
            <td align=center class=table_col1>$counter.</td>
            <td class=table_col2><a href=account-details.php?id=$row[id]>$row[username]</a>
            $star $klicaj</td>
            <td class=table_col1>" . mksize($uploaded). "</td>
            <td class=table_col2>" . mksize($downloaded) . "</td>
            <td class=table_col1>$ratio</td>";
            if (! $numtorrents)
                echo "<td class=table_col2><font color=red>$numtorrents torrents</font></td>";
            else
                echo "<td class=table_col2>$numtorrents torrents</td>";

            echo "<td class=table_col1>" . mksize($dayUpload) . "</td>
            <td align=center class=table_col2>
                <a href=account-inbox.php?receiver=$row[username]><img src=images/button_pm.gif border=0></a></td>
            <td class=table_col1>" . $joindate . "</td>
            </tr>";
        }
        echo "</table><br><br>";
        end_frame();
    }

    if ($num == 0) {
        end_frame();
    }
} else {
    begin_frame();
?>
    <form action='<?= $PHP_SELF ?>' method='post'>
    Number of days joined: <input type='text' size='4' maxlength='4' name='daysago'> Days<br />
    MB Uploaded: <input type='text' size='6' maxlength='6' name='megabts'> MB<br />
    <input type='submit' value='   Submit   ' style='background:#eeeeee'>
    &nbsp;&nbsp;&nbsp;<input type='reset' value='  Reset  ' style='background:#eeeeee'>
    </form>
<?php
    end_frame();
}

stdfoot();
