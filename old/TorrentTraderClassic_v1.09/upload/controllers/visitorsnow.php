<?php

dbconn(true);

stdhead();

$activepeople = '';

$res = DB::query('
    SELECT id, username, class, donated, warned
    FROM users WHERE UNIX_TIMESTAMP(' . get_dt_num() . ') - UNIX_TIMESTAMP(last_access) < 900
    ORDER BY username
    LIMIT 5000');

while ($arr = $res->fetch()) {
	if ($activepeople)
		$activepeople .= ', ';
	switch ($arr['class'])
	{
	case UC_ADMINISTRATOR:
	  $arr['username'] = '<font color=#FF0000>' . $arr['username'] . '</font>';
	  break;
	case UC_MODERATOR:
	  $arr['username'] = '<font color=#A83838>' . $arr['username'] . '</font>';
	  break;
	case UC_VIP:
	  $arr['username'] = '<font color=#FEDE01>' . $arr['username'] . '</font>';
	  break;
	 case UC_UPLOADER:
	  $arr['username'] = '<font color=#C0C0C0>' . $arr['username'] . '</font>';
	  break;
	   case UC_JMODERATOR:
	  $arr['username'] = '<font color=#000000>' . $arr['username'] . '</font>';
	  break;
	}

	$donator = $arr['donated'] > 0;
	if ($CURUSER) {
		$activepeople .= '<a href=account-details.php?id=' . $arr['id'] . '>' . $arr['username'] . '</a>';
	} else {
		$activepeople .= '<a href=account-details.php?id=' . $arr['id'] . '>' . $arr['username'] . '</a>';
	}
	if ($donator) {
		$activepeople .= '<img src="images/star.gif" alt="Donator">';
	}
	$warned = $arr['warned'] === 'yes';
	if ($warned) {
		$activepeople .= '<img src="images/warned.gif" alt="Warning">';
	}
	// $usersactive++; // ???
}
//end visited today

begin_frame();
echo '<center><b><font color=#0000AA>Member</font> | <font color=#C0C0C0>Uploader</font> | <font color=#FEDE01>VIP</font> |' . 
    ' <font color=#000000>Moderator</font> | <font color=#A83838>Super Moderator</font> | <font color=#FF0000>Administrator</font> <br>' .
    '<img src="images/warned.gif"><font color=#008000> Warning</font> | <img src="images/star.gif"><font color=#008000> Donator</font> <br><br></b></center>';
end_frame();

begin_frame($txt['ONLINE_USERS'], 'center');
echo '<div align="left">' . $activepeople . '</div>';
end_frame();
stdfoot();
