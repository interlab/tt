<?php

require_once 'backend/functions.php';
dbconn(true);
stdhead();

$todayactive = '';

$res = DB::query('
    SELECT id, username, class, donated, warned FROM users 
    WHERE UNIX_TIMESTAMP(' . get_dt_num() . ') - UNIX_TIMESTAMP(last_access) < 86400 
    ORDER BY username
    LIMIT 5000');
while ($arr = $res->fetch()) {
	if ($todayactive) {
		$todayactive .= ', ';
    }
    $color = '';
	switch ($arr['class']) {
        case UC_ADMINISTRATOR:
            $color = 'FF0000';
        break;
        case UC_MODERATOR:
            $color = 'A83838';
        break;
        case UC_VIP:
            $color = 'FEDE01';
        break;
        case UC_UPLOADER:
            $color = 'C0C0C0';
        break;
        case UC_JMODERATOR:
            $color = '000000';
        break;
	}
    if ($color) {
        $arr['username'] = '<font color=#' . $color . '>' . $arr['username'] . '</font>';
    }
    
	$donator = $arr['donated'] > 0;
	if ($CURUSER) {
		$todayactive .= '<a href=account-details.php?id=' . $arr['id'] . '>' . $arr['username'] . '</a>';
	} else {
		$todayactive .= '<a href=account-details.php?id=' . $arr['id'] . '>' . $arr['username'] . '</a>';
	}
	if ($donator) {
		$todayactive .= '<img src="images/star.gif">';
	}
	$warned = $arr['warned'] === 'yes';
	if ($warned) {
		$todayactive .= '<img src="images/warned.gif">';
	}

	// $usersactivetoday++; // ???
}
//end visited today

begin_frame();
echo '<center><b><font color=#0000AA>Member</font> | <font color=#C0C0C0>Uploader</font> | <font color=#FEDE01>VIP</font> | '.
    '<font color=#000000>Moderator</font> | <font color=#A83838>Super Moderator</font> | <font color=#FF0000>Administrator</font> ' .
    '<br><img src="images/warned.gif"><font color=#008000> Warning</font> | <img src="images/star.gif">' .
    '<font color=#008000> Donator</font> <br><br></b></center>';
end_frame();

begin_frame($txt['VISITORS_TODAY'], 'center');
echo "<div align='left'>" . $todayactive . "</div>";
end_frame();
stdfoot();

