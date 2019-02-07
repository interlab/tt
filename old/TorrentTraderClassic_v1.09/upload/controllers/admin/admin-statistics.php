<?php

require_once '../../backend/functions.php';

dbconn(false);

loggedinorreturn();
adminonly();
stdhead("Statistics");
require_once '../../backend/admin-functions.php';
adminmenu();
begin_frame("STATISTICS");

$SITEURL2 = $SITEURL.'/admin-statistics.php';
?>

<CENTER>

<a href='admin-statistics.php?act=stats&code=reg'>Registration Stats</a>
• <a href='admin-statistics.php?act=stats&code=rate'>Rating Stats</a>
• <a href='admin-statistics.php?act=stats&code=post'>Forum Post Stats</a>
• <a href='admin-statistics.php?act=stats&code=msg'>Personal Message</a>
• <a href='admin-statistics.php?act=stats&code=torr'>Torrents Stats</a>
<br>
<a href='admin-statistics.php?act=stats&code=rqst'>Requests Stats</a>
• <a href='admin-statistics.php?act=stats&code=bans'>Bans Stats</a>
• <a href='admin-statistics.php?act=stats&code=comm'>Comments Stats</a>

<!-- <a href='admin-statistics.php?act=stats&code=new'>News Stats</a>
<a href='admin-statistics.php?act=stats&code=poll'>Poll Stats</a> -->

<br><br>

<?php

if (!isset($_GET['act']) && !isset($_POST['act'])) {
    echo "<BR><BR><BR><BR><BR>"; // Do nothing!
}


function start_form($hiddens="", $name='theAdminForm', $js="")
{
    global $SITEURL2;

    $form = "<form action='{$SITEURL2}' method='post' name='$name' $js>";

    if (is_array($hiddens)) {
        foreach ($hiddens as $k => $v) {
            $form .= "\n<input type='hidden' name='{$v[0]}' value='{$v[1]}'>";
        }
    }

    return $form;
}

function form_dropdown($name, $list=[], $default_val="", $js="", $css="")
{
    if ($js != "") {
        $js = ' '.$js.' ';
    }

    if ($css != "") {
        $css = ' class="'.$css.'" ';
    }

    $html = "<select name='$name'".$js." $css class='dropdown'>\n";

    foreach ($list as $k => $v) {
        $selected = "";
        if ( ($default_val != "") and ($v[0] == $default_val) ) {
            $selected = ' selected';
        }

        $html .= "<option value='".$v[0]."'".$selected.">".$v[1]."</option>\n";
    }

    $html .= "</select>\n\n";

    return $html;
}

function end_form($text = "", $js = "", $extra = "")
{
    // If we have text, we print another row of TD elements with a submit button
    $html    = "";
    $colspan = "";
    $td_colspan = 0;

    if ($text != "") {
        if ($td_colspan > 0) {
            $colspan = " colspan='".$td_colspan."' ";
        }

        $html .= "
            <tr><td align='center' class='form'".$colspan.">
            <input type='submit' value='$text'".$js." id='button' accesskey='s'>{$extra}
            </td></tr>";
    }

    $html .= "</form>";

    return $html;
}

// Don't ask!!

$tmp_in = array_merge( $_GET, $_POST );

foreach ( $tmp_in as $k => $v ) {
    unset($$k);
}

$month_names = [
    1 => 'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];

if (isset($tmp_in['code']) && $tmp_in['code'] != "") {
    switch ($tmp_in['code']) {
        case 'show_reg':
            result_screen('reg');
            break;

        case 'show_rate':
            result_screen('rate');
            break;

        case 'rate':
            main_screen('rate');
            break;

        case 'show_post':
            result_screen('post');
            break;

        case 'post':
            main_screen('post');
            break;

        case 'show_msg':
            result_screen('msg');
            break;

        case 'msg':
            main_screen('msg');
            break;

        case 'show_torr':
            result_screen('torr');
            break;

        case 'torr':
            main_screen('torr');
            break;

        case 'show_bans':
            result_screen('bans');
            break;

        case 'bans':
            main_screen('bans');
            break;

        case 'show_comm':
            result_screen('comm');
            break;

        case 'comm':
            main_screen('comm');
            break;

        case 'show_new':
            result_screen('new');
            break;

        case 'new':
            main_screen('new');
            break;

        case 'show_poll':
            result_screen('poll');
            break;

        case 'poll':
            main_screen('poll');
            break;

        case 'show_rqst':
            result_screen('rqst');
            break;

        case 'rqst':
            main_screen('rqst');
            break;

        default:
            main_screen('reg');
            break;
    }
}


function result_screen($mode='reg')
{
    global $month_names;

    $page_title = "<b>Statistic Results</b>";

    $page_detail = "&nbsp;";

    if ( ! checkdate($_POST['to_month']   ,$_POST['to_day']   ,$_POST['to_year']) )
    {
        die("The 'Date To:' time is incorrect, please check the input and try again");
    }

    if ( ! checkdate($_POST['from_month'] ,$_POST['from_day'] ,$_POST['from_year']) )
    {
        die("The 'Date From:' time is incorrect, please check the input and try again");
    }

    $to_time   = mktime(12 ,0 ,0 ,$_POST['to_month']   ,$_POST['to_day']   ,$_POST['to_year']  );
    $from_time = mktime(12 ,0 ,0 ,$_POST['from_month'] ,$_POST['from_day'] ,$_POST['from_year']);
    //$sql_date_to = date("Y-m-d",$to_time);
    //$sql_date_from = date("Y-m-d",$from_time);

    $human_to_date   = getdate($to_time);
    $human_from_date = getdate($from_time);

    if ($mode == 'reg')
    {
        $table     = 'Registration Statistics';

        $sql_table = 'users';
        $sql_field = 'added';

        $page_detail = "Showing the number of users registered. (Note: All times based on GMT)";
    } elseif ($mode == 'rate') {
        $table     = 'Rating Statistics';

        $sql_table = 'ratings';
        $sql_field = 'added';

        $page_detail = "Showing the number of ratings. (Note: All times based on GMT)";
    } elseif ($mode == 'post') {
        $table     = 'Post Statistics';

        $sql_table = 'forum_posts';
        $sql_field = 'added';

        $page_detail = "Showing the number of posts. (Note: All times based on GMT)";
    } elseif ($mode == 'msg') {
        $table     = 'PM Sent Statistics';

        $sql_table = 'messages';
        $sql_field = 'added';

        $page_detail = "Showing the number of sent messages. (Note: All times based on GMT)";
    } elseif ($mode == 'torr') {
        $table     = 'Torrent Statistics';

        $sql_table = 'torrents';
        $sql_field = 'added';

        $page_detail = "Showing the number of Torrents. (Note: All times based on GMT)";
    } elseif ($mode == 'bans') {
        $table     = 'Ban Statistics';

        $sql_table = 'bans';
        $sql_field = 'added';

        $page_detail = "Showing the number of Bans. (Note: All times based on GMT)";
    } elseif ($mode == 'comm') {
        $table     = 'Comment Statistics';

        $sql_table = 'comments';
        $sql_field = 'added';

        $page_detail = "Showing the number of torrent Comments. (Note: All times based on GMT)";
    } elseif ($mode == 'new') {
        $table     = 'News Statistics';

        $sql_table = 'news';
        $sql_field = 'added';

        $page_detail = "Showing the number of News Items added. (Note: All times based on GMT)";
    } elseif ($mode == 'poll') {
        $table     = 'Poll Statistics';

        $sql_table = 'polls';
        $sql_field = 'added';

        $page_detail = "Showing the number of Polls added. (Note: All times based on GMT)";
    } elseif ($mode == 'rqst') {
        $table     = 'Request Statistics';

        $sql_table = 'requests';
        $sql_field = 'added';

        $page_detail = "Showing the number of Requests made. (Note: All times based on GMT)";
    }

    switch ($_POST['timescale']) {
        case 'daily':
            $sql_date = "%w %U %m %Y";
            $php_date = "F jS - Y";
            //$sql_scale = "DAY";
            break;

        case 'monthly':
            $sql_date = "%m %Y";
            $php_date = "F Y";
            //$sql_scale = "MONTH";
            break;

        default:
            // weekly
            $sql_date = "%U %Y";
            $php_date = " [F Y]";
            //$sql_scale = "WEEK";
            break;
    }

    $sortby = $_POST['sortby'] ?? '';
    $sortby = $sortby === 'desc' ? 'desc' : 'asc';
    //$sortby = sqlesc($sortby);
    $sqlq = "SELECT UNIX_TIMESTAMP(MAX({$sql_field})) as result_maxdate,
                COUNT(*) as result_count,
                DATE_FORMAT({$sql_field},'{$sql_date}') AS result_time
                FROM {$sql_table}
                WHERE UNIX_TIMESTAMP({$sql_field}) > '{$from_time}'
                AND UNIX_TIMESTAMP({$sql_field}) < '{$to_time}'
                GROUP BY result_time
                ORDER BY {$sql_field} $sortby";

    $res = DB::executeQuery($sqlq);
/*    $res = @mysql_query( "SELECT UNIX_TIMESTAMP(MAX(added)) as result_maxdate,
                        COUNT(*) as result_count,
                        ".$sql_scale."(".$sql_field.") AS result_time
                        FROM ".$sql_table."
                        WHERE added > '".$sql_date_from."'
                        AND added < '".$sql_date_to."'
                        GROUP BY result_time
                        ORDER BY ".$sql_field); */

    $running_total = 0;
    $max_result    = 0;
    $results       = [];

    // Naaaaaaaaaaaaaaaaah!! STILL!
    //$td_header = [];
    //$td_header[] = array( "Date"    , "20%" );
    //$td_header[] = array( "Result"  , "70%" );
    //$td_header[] = array( "Count"   , "10%" );

    $html = $page_title."<br><table id=torrenttable cellpadding=3 cellspacing=1 style='border-collapse: collapse'
        bordercolor=#646262 width=95% border=1><tr><td colspan=3>".ucfirst($_POST['timescale']) ." ".$table
        ." ({$human_from_date['mday']} {$month_names[$human_from_date['mon']]} {$human_from_date['year']} to"
        ." {$human_to_date['mday']} {$month_names[$human_to_date['mon']]} {$human_to_date['year']})<br>{$page_detail}</td></tr>\n";

    $numrows = 0;

    while ($row = $res->fetch()) {
        $numrows += 1;
        if ( $row['result_count'] >  $max_result ) {
            $max_result = $row['result_count'];
        }

        $running_total += $row['result_count'];

        $results[] = [
            'result_maxdate'  => $row['result_maxdate'],
            'result_count'    => $row['result_count'],
            'result_time'     => $row['result_time'],
        ];        
    }

    if (! $numrows) {
        $html .= "<tr><td>No results found</td></tr>\n";
    } else {
        foreach ( $results as $pOOp => $data ) {
            $img_width = intval( ($data['result_count'] / $max_result) * 100 - 20);

            if ($img_width < 1) {
                $img_width = 1;
            }

            $img_width .= '%';

            if ($_POST['timescale'] == 'weekly') {
                $date = "Week #".strftime("%W", $data['result_maxdate'])."<br>" . date( $php_date, $data['result_maxdate'] );
            } else {
                $date = date( $php_date, $data['result_maxdate'] );
            }

            $html .= "<tr><td width=25%>" .$date . "</td>
            <td width=70%><img src='images/bar_left.gif' border='0' width='4' height='11' align='middle'
                alt=''><img src='images/bar.gif' border='0' width='$img_width' height='11' align='middle'
                alt=''><img src='images/bar_right.gif' border='0' width='4' height='11' align='middle'
                alt=''></td><td align=right width=5%>". $data['result_count']."</td></tr>\n";
        }

        $html .= '<tr><td colspan=3>&nbsp;'. "<div align='right'><b>Total </b>".
                    "<b>".$running_total."</b></div></td></tr>\n";

    }

    print $html."</table>\n<br>";
}


//| Date selection screen

function main_screen($mode='reg')
{
    global $month_names;

    $page_title = "";//put something here if you wish

    $page_detail = "Please define the date ranges and other options below.<br>Note: The statistics generated are based 
        on the information currently held in the database, they do not take into account pruned forums or delete posts, etc.<br>";

    if ($mode == 'reg') {
        $form_code = 'show_reg';
        $table     = 'Registration Statistics<br>';
    } elseif ($mode == 'rate') {
        $form_code = 'show_rate';
        $table     = 'Rating Statistics';
    } elseif ($mode == 'post') {
        $form_code = 'show_post';
        $table     = 'Post Statistics';
    } elseif ($mode == 'msg') {
        $form_code = 'show_msg';
        $table     = 'PM Statistics';
    } elseif ($mode == 'torr') {
        $form_code = 'show_torr';
        $table     = 'Torrent Statistics';
    } elseif ($mode == 'bans') {
        $form_code = 'show_bans';
        $table     = 'Ban Statistics';
    } elseif ($mode == 'comm') {
        $form_code = 'show_comm';
        $table     = 'Comment Statistics';
    } elseif ($mode == 'new') {
        $form_code = 'show_new';
        $table     = 'News Statistics';
    } elseif ($mode == 'poll') {
        $form_code = 'show_poll';
        $table     = 'Polls Statistics';
    } elseif ($mode == 'rqst') {
        $form_code = 'show_rqst';
        $table     = 'Request Statistics';
    }

    $old_date = getdate(time() - (3600 * 24 * 90));
    $new_date = getdate(time() + (3600 * 24));

    $html =  "<table id=torrenttable border=0><tr><td><B>$table</B></td></tr>";
    $html .=  "<tr><td>$page_title<br>$page_detail</td></tr>";
    $html .= start_form([
        1 => [ 'code', $form_code ],
        2 => [ 'act', 'stats' ],
    ]);

    // Naaaaaaaaaaaah!!
    //$td_header = [];
    //$td_header[] = array( "&nbsp;"  , "40%" );
    //$td_header[] = array( "&nbsp;"  , "60%" );

    $html .= "<tr><td><br><b>Date From: </b>" .
        form_dropdown( "from_month" , make_month(), $old_date['mon']  ).'&nbsp;&nbsp;'.
        form_dropdown( "from_day"   , make_day()  , $old_date['mday'] ).'&nbsp;&nbsp;'.
        form_dropdown( "from_year"  , make_year() , $old_date['year'] )."<br></td></tr>";

    $html .= "<tr><td><br><b>Date To: </b>" .
        form_dropdown( "to_month" , make_month(), $new_date['mon']  ).'&nbsp;&nbsp;'.
        form_dropdown( "to_day"   , make_day()  , $new_date['mday'] ).'&nbsp;&nbsp;'.
        form_dropdown( "to_year"  , make_year() , $new_date['year'] ) ."<br></td></tr>";

    if ($mode != 'views') {
        $html .= "<tr><td><br><b>Time scale: </b>" .form_dropdown( "timescale" , array(
            0 => array( 'daily', 'Daily'), 1 => array( 'weekly', 'Weekly' ),
            2 => array( 'monthly', 'Monthly' ) ) ) ."<br></td></tr>";
    }

    $html .= "<tr><td><br><b>Result Sorting: </b>" .form_dropdown( "sortby" , array(
            0 => array( 'asc', 'Ascending - Oldest dates first'),
            1 => array( 'desc', 'Descending - Newest dates first' ) ), 'desc' ) ."<br></td></tr>";

    $html .= end_form("Show")."</table>";

    print $html;
}


function make_year()
{
    $time_now = getdate();
    $return = [];
    $start_year = 2002;
    $latest_year = intval($time_now['year']);

    if ($latest_year == $start_year) {
        $start_year -= 1;
    }

    for ( $y = $start_year; $y <= $latest_year; $y++ ) {
        $return[] = [$y, $y];
    }

    return $return;
}


function make_month()
{
    global $month_names;

    reset($month_names);
    $return = [];
    for ( $m = 1 ; $m <= 12; $m++ ) {
        $return[] = [ $m, $month_names[$m] ];
    }

    return $return;
}


function make_day()
{
    $return = [];

    for ( $d = 1 ; $d <= 31; $d++ ) {
        $return[] = [ $d, $d ];
    }

    return $return;
}

echo "</CENTER>";
end_frame();
stdfoot();
