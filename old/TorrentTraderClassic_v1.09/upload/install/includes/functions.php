<?php

function h($str)
{
    return htmlspecialchars($str, ENT_COMPAT, 'utf-8', false);
}

function getip() {
   if (isset($_SERVER)) {
     if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && validip($_SERVER['HTTP_X_FORWARDED_FOR'])) {
       $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
     } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && validip($_SERVER['HTTP_CLIENT_IP'])) {
       $ip = $_SERVER['HTTP_CLIENT_IP'];
     } else {
       $ip = $_SERVER['REMOTE_ADDR'];
     }
   } else {
     if (getenv('HTTP_X_FORWARDED_FOR') && validip(getenv('HTTP_X_FORWARDED_FOR'))) {
       $ip = getenv('HTTP_X_FORWARDED_FOR');
     } elseif (getenv('HTTP_CLIENT_IP') && validip(getenv('HTTP_CLIENT_IP'))) {
       $ip = getenv('HTTP_CLIENT_IP');
     } else {
       $ip = getenv('REMOTE_ADDR');
     }
   }

   return $ip;
 }

function validip($ip)
{
	if (!empty($ip) && $ip == long2ip(ip2long($ip)))
	{
		// reserved IANA IPv4 addresses
		// http://www.iana.org/assignments/ipv4-address-space
		$reserved_ips = array (
				array('0.0.0.0','2.255.255.255'),
				array('10.0.0.0','10.255.255.255'),
				array('127.0.0.0','127.255.255.255'),
				array('169.254.0.0','169.254.255.255'),
				array('172.16.0.0','172.31.255.255'),
				array('192.0.2.0','192.0.2.255'),
				array('192.168.0.0','192.168.255.255'),
				array('255.255.255.0','255.255.255.255')
		);

		foreach ($reserved_ips as $r)
		{
				$min = ip2long($r[0]);
				$max = ip2long($r[1]);
				if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
		}
		return true;
	}
	else return false;
}

function validusername($username)
{
    if ($username == "") {
        return false;
    }

    $allowedchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    for ($i = 0; $i < strlen($username); ++$i) {
        if (strpos($allowedchars, $username[$i]) === false) {
            return false;
        }
    }

    return true;
}

function begin_frame($caption='-', $align='justify')
{

	print("<br /><TABLE width=100% border=0 align=center>\n"
		."\t<TR>\n"
		."\t\t<TD style='PADDING-LEFT: 12px; FONT-WEIGHT: bold; VERTICAL-ALIGN: middle; PADDING-TOP: 2px' bgColor=#dd1212 height=25>&nbsp;<img src=images/dot_arrow.gif align=middle>&nbsp;&nbsp;$caption</TD></TR>\n"
		."\t<TR>\n"
		."\t\t<TD>\n"
		."\t<TABLE cellSpacing=0 cellPadding=0 width=100% align=center>\n"
		."\t<TBODY>\n"
		."\t<TR>\n"
		."\t\t<TD style='PADDING-TOP: 5px' valign=top align=$align>");
  }

  function end_frame()
  {
    print("\t\t</TD>\n"
		."\t</TR>\n"
		."\t</TBODY>\n"
		."\t</TABLE>\n"
		."\t\t</TD>\n"
		."\t</TR>\n"
		."\t</TABLE>");
  }

function begin_block($caption='-', $align='justify')
{

	print("<br /><TABLE width=100% align=center>\n"
		."\t<TR>\n"
		."\t\t<TD style='PADDING-LEFT: 12px; FONT-WEIGHT: bold; VERTICAL-ALIGN: middle; PADDING-TOP: 2px' bgColor=#4396ca height=25>&nbsp;<img src=themes/troots2/images/dot_arrow.gif align=middle>&nbsp;&nbsp;$caption</TD></TR>\n"
		."\t<TR>\n"
		."\t\t<TD>\n"
		."\t<TABLE cellSpacing=0 cellPadding=0 width=100% align=center>\n"
		."\t<TBODY>\n"
		."\t<TR>\n"
		."\t\t<TD style='PADDING-TOP: 5px' valign=top align=$align>");
  }

  function end_block()
  {
    print("\t\t</TD>\n"
		."\t</TR>\n"
		."\t</TBODY>\n"
		."\t</TABLE>\n"
		."\t\t</TD>\n"
		."\t</TR>\n"
		."\t</TABLE>");
  }

function head($title='') {
     header("Content-Type: text/html; charset=iso-8859-1");
     header("Cache-Control: no-cache, must-revalidate");
     header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
     $title = "TorrentTrader Installer ..::.. " . h($title);

echo <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>$title</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="imagetoolbar" content="no" />
<link rel="stylesheet" type="text/css" href="includes/installer.css" />

</head>

<body leftMargin=0 topMargin=0 MARGINHEIGHT="0" MARGINWIDTH="0">
<center>
<table cellSpacing=0 cellPadding=0 width=780 border=0>
	<tr>
		<td>
			<img alt="" src="images/top.jpg" width=780 height=162 border=0>
		</td>
	</tr>
	<Tr>
		<td width=780 height=34 background="images/tile_h.gif" bgColor=#d70d0d colSpan=2 height=39></td>
	</tr>
	<tr><td></td></tr>
	<tr><td>
EOD;

}

function foot() {
  echo <<<EOD
</td></tr></table>
    <BR>
      <table  width="780">

<tr>
	<td align=center>
		Installer by <a href="http://www.torrenttrader.org" target=_blank>TorrentialStorm @ TorrentTrader.org</a>
	</td>
</tr>

</table>
</BODY></HTML>
EOD;
}

function generateRandomString($length = 20)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}


function mksecret($len = 20)
{
    return generateRandomString(20);
}

function bark($heading = "Error", $text, $sort = "Error") {
  head("$sort: $heading");
  begin_frame("<font color=white>$sort: $heading</font>", 'center');
  echo $text;
  end_frame();
  foot();
  die;
}
