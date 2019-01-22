<?
//
// - Theme And Language Updated 25.Nov.05 - 1/2 DONE, NEED TO ADD ERRORS
//
ob_start();
require_once("backend/functions.php");
dbconn();
loggedinorreturn();


//Here we decide if uploads are for uploaders only
if ($UPLOADERSONLY)
{
	if (get_user_class() < UC_UPLOADER) {
		stdhead("Uploaders Only");
		begin_frame("Uploaders Only");
		echo "<center><br><BR><B>You are not a uploader class, you cannot upload.<br><BR><b>";
		echo "<br><BR>You can apply to become a uploader by filling out <a href=uploadapp.php>this</a> form<br><BR></center>";
		end_frame();
		stdfoot();
		exit;
	}
}
//end



ini_set("upload_max_filesize",$max_torrent_size);

if($MAX_FILE_SIZE) {
  require_once("backend/benc.php");

  foreach(explode(":","descr:type:name") as $v) {
	if (!isset($_POST[$v]))
	  $message = "Missing form data";
  }

  if (!isset($_FILES["file"]))
	$message = "Missing form data";

  $f = $_FILES["file"];
  $fname = unesc($f["name"]);
  if (empty($fname))
	$message = "Empty filename!";

if ($_FILES['nfo']['size'] != 0) {
	  $nfofile = $_FILES['nfo'];
	  if ($nfofile['name'] == '')
		$message = "No NFO!";
        
    if (!preg_match('/^(.+)\.nfo$/si', $nfofile['name'], $fmatches))
	$message = "Invalid filename (not a .NFO).";

	  if ($nfofile['size'] == 0)
		$message = "0-byte NFO";

	  if ($nfofile['size'] > 65535)
		$message = "NFO is too big! Max 65,535 bytes.";

	  $nfofilename = $nfofile['tmp_name'];
	  if (@!is_uploaded_file($nfofilename))
		$message = "NFO upload failed";
    
}

  $descr = unesc($_POST["descr"]);
  if (!$descr)
    $message = "You must enter at least a short description";

  $catid = (0 + $_POST["type"]);
  if (!is_valid_id($catid))
	$message = "Please be sure to select a torrent category";

  if (!validfilename($fname))
	$message = "Invalid filename!";
  if (!preg_match('/^(.+)\.torrent$/si', $fname, $matches))
	$message = "Invalid filename (not a .torrent).";
  $shortfname = $torrent = $matches[1];
  if (!empty($_POST["name"]))
	$torrent = unesc($_POST["name"]);

  $tmpname = $f["tmp_name"];
  if (!is_uploaded_file($tmpname))
	$message = "The file was uploaded, but wasn't found on the temp directoy.";

  $dict = bdec_file($tmpname, $max_torrent_size);
  if (!isset($dict))
	$message = "What the hell did you upload? This is not a bencoded file!";

  function dict_check($d, $s) {
	if ($d["type"] != "dictionary")
	  $message = "Not a dictionary";
	$a = explode(":", $s);
	$dd = $d["value"];
	$ret = array();
	foreach ($a as $k) {
		unset($t);
		if (preg_match('/^(.*)\((.*)\)$/', $k, $m)) {
			$k = $m[1];
			$t = $m[2];
		}
		if (!isset($dd[$k]))
			$message = "The dictionary is missing key(s)";
		if (isset($t)) {
			if ($dd[$k]["type"] != $t)
				$message = "There is an invalid entry in the dictionary";
			$ret[] = $dd[$k]["value"];
		}
		else
			$ret[] = $dd[$k];
	}
	return $ret;
  }

  function dict_get($d, $k, $t) {
	if ($d["type"] != "dictionary")
		$message = "This isn't a dictionary.";
	$dd = $d["value"];
	if (!isset($dd[$k]))
		return;
	$v = $dd[$k];
	if ($v["type"] != $t)
		$message = "invalid dictionary entry type";
	return $v["value"];
  }

  list($ann, $info) = dict_check($dict, "announce(string):info");
  list($dname, $plen, $pieces) = dict_check($info, "name(string):piece length(integer):pieces(string)");


  if (!in_array($ann, $announce_urls, 1))
	$message = "$ann Invalid announce url! It MUST be <b>" . $announce_urls[0] . "</b>";

  if (strlen($pieces) % 20 != 0)
	$message = "Invalid pieces!";

  $filelist = array();
  $totallen = dict_get($info, "length", "integer");
  if (isset($totallen)) {
	$filelist[] = array($dname, $totallen);
	$type = "single";
  }
  else {
	$flist = dict_get($info, "files", "list");
	if (!isset($flist))
		$message = "Missing both length and files";
	if (!count($flist))
		$message = "No files";
	$totallen = 0;
	foreach ($flist as $fn) {
		list($ll, $ff) = dict_check($fn, "length(integer):path(list)");
		$totallen += $ll;
		$ffa = array();
		foreach ($ff as $ffe) {
			if ($ffe["type"] != "string")
				$message = "Filename error";
			$ffa[] = $ffe["value"];
		}
		if (!count($ffa))
			$message = "Filename error";
		$ffe = implode("/", $ffa);
		$filelist[] = array($ffe, $ll);
	}
	$type = "multi";
  }

if ($DHT){
// DHT private key
$dict["value"]["info"]["value"]["private"]["type"] = "integer";
$dict["value"]["info"]["value"]["private"]["value"] = 1;
$fn = benc($dict);
$dict = bdec($fn);
list($info) = dict_check($dict, "info");
// end private key
}

$tmphex = sha1($info["string"]);
$hexhash = strtolower($tmphex);

if (strlen($hexhash) != 40)
{
	$message = "Error: Info hash must be exactly 40 hex bytes. Contact an admin to fix this";
}

  // Replace punctuation characters with spaces
  if(!$message) {
    $torrent = str_replace("_", " ", $torrent);

    $nfo = sqlesc(str_replace("\x0d\x0d\x0a", "\x0d\x0a", @file_get_contents($nfofilename)));
    $ret = mysql_query("INSERT INTO torrents (search_text, filename, owner, visible, info_hash, name, size, numfiles, type, descr, ori_descr, category, save_as, added, last_action, nfo) VALUES (" .
		implode(",", array_map("sqlesc", array(searchfield("$shortfname $dname $torrent"), $fname, $CURUSER["id"], "no", $hexhash, $torrent, $totallen, count($filelist), $type, $descr, $descr, 0 + $_POST["type"], $dname))) .
		", '" . get_date_time() . "', '" . get_date_time() . "', $nfo)");
    if (!$ret) {
      $message = "Mysql Error: ".mysql_error();
	  if (mysql_errno() == 1062)
		$message = "Torrent already uploaded!";
    }
    $id = mysql_insert_id();

    @mysql_query("DELETE FROM files WHERE torrent = $id");
    foreach ($filelist as $file) {
	  @mysql_query("INSERT INTO files (torrent, filename, size) VALUES ($id, ".sqlesc($file[0]).",".$file[1].")");
    }

    move_uploaded_file($tmpname, "$torrent_dir/$id.torrent");
	if (isset($_FILES['nfo'])) {
		move_uploaded_file($nfofilename, "$nfo_dir/$id.nfo");
	}
    
    
    
    write_log("Torrent $id (".htmlspecialchars($torrent).") was uploaded by " . $CURUSER["username"]);

if (isset($_POST['request'])) {
	if ($_POST['request'] > 0) {
	/* PM for requested user */
	$res = mysql_query("SELECT `userid` FROM `requests` WHERE `id` = ". ($_POST['request'] + 0)) or sqlerr(__FILE__, __LINE__);
	$re_msg = "Your request \"$torrent\" was filled by " . $CURUSER["username"] . ".You can download it <a href=".$SITEURL."/torrents-details.php?id=$id&amp;hit=1>HERE</a>";
	while($row = mysql_fetch_assoc($res)) {
	mysql_query("INSERT INTO messages (poster, sender, receiver, added, msg) VALUES(0, 0, $row[userid], '" . get_date_time() . "', " . sqlesc($re_msg) . ")") or sqlerr(__FILE__, __LINE__);
	}
	/* requests delete */
	@mysql_query("DELETE FROM `requests` WHERE `id` = ". ($_POST['request'] + 0));
	@mysql_query("DELETE FROM `addedrequests` WHERE `requestid` = ". ($_POST['request'] + 0));
	write_log("The request ($torrent) was filled by " . $CURUSER["username"] . "");
	}
}

if ($DHT){
// Code to write the updated dictionary to the torrent file
$fp = fopen("$torrent_dir/$id.torrent", "w");
if ($fp)
	{
	@fwrite($fp, benc($dict), strlen(benc($dict)));
	fclose($fp);
	}
// End of code to write the updated dictionary to the torrent file
}
// start irc announce hack v1.0 by FLASH
if ($IRCANNOUNCE)
{
$rs = mysql_query(" SELECT * FROM categories WHERE id='" . intval($catid) . "' LIMIT 1"); 
$cat_details = mysql_fetch_assoc($rs); 
$user = mysql_fetch_array(mysql_query("SELECT username FROM users WHERE id=".$CURUSER["id"])); 
$user = $user["username"]; 
$msg_bt = chr(3)."9".chr(2)." $SITENAME".chr(2)." -".chr(3)."10 New Torrent: (".chr(3)."15 $torrent".chr(3)."10 ) Size: (".chr(3)."15 ".mksize($totallen).chr(3)."10 )  Category: (".chr(3)."15 ". $cat_details["name"].chr(3)."10 ) Uploader: (".chr(3)."15 $user".chr(3)."10 ) Link: (".chr(3)."15 $SITEURL/torrents-details.php?id=$id&hit=1".chr(3)."10 )\r\n"; 
$fs = fsockopen($ANNOUNCEIP, $ANNOUNCEPORT, $errno, $errstr);
if($fs) { 
       fwrite($fs, $msg_bt); 
       fclose($fs); 
	} 
}
//end irc announce hack v1.0 by FLASH
    $res = mysql_query("SELECT name FROM categories WHERE id=$catid") or sqlerr();
    $arr = mysql_fetch_assoc($res);
    $cat = $arr["name"];
    $res = mysql_query("SELECT email FROM users WHERE enabled='yes' AND notifs LIKE '%[cat$catid]%'") or sqlerr();
    $uploader = $CURUSER['username'];

    $size = mksize($totallen);
    $description = ($html ? strip_tags($descr) : $descr);

//EMAIL NOTIFICATION
    $body = <<<EOD
A new torrent has been uploaded.

Name: $torrent
Size: $size
Category: $cat
Uploaded by: $uploader

Description:
-------------------------------------------------------------------------------
$description
-------------------------------------------------------------------------------

You can use the URL below to download the torrent (you may have to login).

$SITEURL/torrents-details.php?id=$id&hit=1

--
$SITENAME
EOD;
    $to = "";
    $nmax = 100; // Max recipients per message
    $nthis = 0;
    $ntotal = 0;
    $total = mysql_num_rows($res);
    while ($arr = mysql_fetch_row($res)) {
      if ($nthis == 0)
        $to = $arr[0];
      else
        $to .= "," . $arr[0];
      ++$nthis;
      ++$ntotal;
      if ($nthis == $nmax || $ntotal == $total) {
        if (!mail("Multiple recipients <$SITEEMAIL>", "New torrent - $torrent", $body,
         "From: $SITEEMAIL\r\nBcc: $to", "-f$SITEEMAIL"))
	    stderr("Error", "Your torrent has been been uploaded. DO NOT RELOAD THE PAGE!\n" .
	      "There was however a problem delivering the e-mail notifcations.\n" .
	      "Please let an administrator know about this error!\n");
        $nthis = 0;
      }
    }
    bark("Upload Succeeded", "The torrent has been uploaded successfully!
    <br>
    <br>
    <br>
    Now that you have uploaded a torrent, you will need to seed it in order to allow other users to download the file.<br>
    To seed the file, open the .TORRENT file you just uploaded and open it in your favourite BitTorrent Client.<br>
    Have your client save to the same file that you have just created the torrent. It will then check for completion and begin to seed.<br><br>
    To download a copy of the .torrent file you just uploaded so you can seed - <a href=\"download.php?id=$id&name=$fname\">CLICK HERE</a> -
    ", Success);
    
  }
}

stdhead("Upload");
begin_frame("" . UPLOAD_RULES . "");
?>
<br />
<ol>
<li>All releases must include a description.</li>
<li>If you are releasing movies you should also include a .nfo file wherever
possible.</li>
<li>Try to make sure your torrents are well-seeded for at least 24 hours.</li>
<li>Do not re-release material that is still active.</li>
</ol>

<?
end_frame();

begin_frame("" . UPLOAD . "");
$max_torrent_size_nice = mksize($max_torrent_size);
$max_nfo_size_nice = mksize($max_nfo_size);

if ($message != "")
  bark2("" . UPLOAD_FAILED . "", $message);
?>
<form enctype="multipart/form-data" action="torrents-upload.php" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="<?=$max_torrent_size?>" />
<table border="0" cellspacing="0" cellpadding="6" align="center">
<?
tr("" . ANNOUNCE . "", "$announce_urls[0]\n", 1);
tr("" . TORRENT_FILE . "", "<input type=file name=file size=50 value=" . $_FILES['file']['name'] . "><br />" . MAX_SIZE_T . " $max_torrent_size_nice\n", 1);
tr("" . NFO . "", "<input type=file name=nfo size=50 value=" . $_FILES['nfo']['name'] . "><br />" . MAX_SIZE_N . " $max_nfo_size_nice\n", 1);
tr("" . TNAME . "", "<input type=text name=name size=60 value=" . $_POST['name'] . ">\n", 1);
tr("" . TDESC . "", "<textarea name=descr rows=7 cols=45>$descr</textarea>" .
  "<br />" . NO_HTML . "", 1);

$s = "<select name=\"type\">\n<option value=\"0\">" . CHOOSE_ONE . "</option>\n";

$cats = genrelist();
foreach ($cats as $row)
	$s .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";

$s .= "</select>\n";
tr("" . TTYPE . "", $s, 1);

//Request filled?
if ($REQUESTSON){
	$sql_request = "SELECT `id`, `request` FROM requests ORDER BY `request` ASC";
	$res = mysql_query($sql_request) or sqlerr(__FILE__, __LINE__);
	if (mysql_num_rows($res) > 0) {
		$request = "<select name=\"request\">\n<option value=\"0\">(Chose the request to be filled)</option>\n";
		while($row = mysql_fetch_array($res)) {
		$request .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["request"]) . "</option>\n";
	}
		$request .= "</select>\n";
		tr("If your upload is to fill a resquest, select it here", $request , 1);
	}
}
//end requests

?>
<tr><td></td><td><input type="submit" value="<? print("" . UPLOADT . "\n"); ?>" /></td></tr>
</table>
</form>
<?

end_frame();
stdfoot();

?>