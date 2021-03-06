<?php

require_once __DIR__ . '/../../backend/functions.php';
dbconn();
loggedinorreturn();

// Here we decide if uploads are for uploaders only
if ($UPLOADERSONLY) {
	if (get_user_class() < UC_UPLOADER) {
		stdhead("Uploaders Only");
		begin_frame("Uploaders Only");
		echo '<br><br><center><b>You are not a uploader class, you cannot upload.<b>
        <br><br><br><br>
        You can apply to become a uploader by filling out <a href=uploadapp.php>this</a> form
        </center><br><br>';
		end_frame();
		stdfoot();
		exit;
	}
}
// end

$html = null;
$message = '';
$descr = '';

ini_set("upload_max_filesize", $max_torrent_size);

if (!empty($_POST['MAX_FILE_SIZE'])) {
    require_once TT_BACKEND_DIR . '/benc.php';

    foreach(explode(":","descr:type:name") as $v) {
        if (!isset($_POST[$v]))
            $message = "Missing form data";
    }

    if (!isset($_FILES["file"])) {
        $message = "Missing form data";
    }

    $f = $_FILES["file"];
    $fname = $f["name"];
    if (empty($fname))
        $message = "Empty filename!";

    $descr = $_POST["descr"];
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
        $torrent = $_POST["name"];

    $tmpname = $f["tmp_name"];
    if (!is_uploaded_file($tmpname)) {
        $message = "The file was uploaded, but wasn't found on the temp directoy.";
    }

    if (!is_writable($torrent_dir)) {
        $message = 'Upload directory must writable!';
    }

    $dict = bdec_file($tmpname, $max_torrent_size);
    if (!isset($dict)) {
        $message = "What the hell did you upload? This is not a bencoded file!";
    }

    function dict_check($d, $s)
    {
        if ($d["type"] != "dictionary")
            $message = "Not a dictionary";
        
        $a = explode(":", $s);
        $dd = $d["value"];
        $ret = [];
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
            else {
                $ret[] = $dd[$k];
            }
        }

        return $ret;
    }

    function dict_get($d, $k, $t)
    {
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

    /*
    if (!in_array($ann, $announce_urls, 1))
	    $message = "$ann Invalid announce url! It MUST be <b>" . $announce_urls[0] . "</b>";
    */

    if (strlen($pieces) % 20 != 0) {
        $message = "Invalid pieces!";
    }

    $filelist = [];
    $totallen = dict_get($info, "length", "integer");
    if (isset($totallen)) {
        $filelist[] = [$dname, $totallen];
        $type = "single";
    } else {
        $flist = dict_get($info, "files", "list");
        if (!isset($flist))
            $message = "Missing both length and files";
	
        if (!count($flist))
            $message = "No files";

        $totallen = 0;
        foreach ($flist as $fn) {
            list($ll, $ff) = dict_check($fn, "length(integer):path(list)");
            $totallen += $ll;
            $ffa = [];

            foreach ($ff as $ffe) {
                if ($ffe["type"] != "string")
                    $message = "Filename error";
			
                $ffa[] = $ffe["value"];
            }

            if (!count($ffa))
                $message = "Filename error";

            $ffe = implode("/", $ffa);
            $filelist[] = [$ffe, $ll];
        }
        $type = "multi";
    }

    if ($DHT) {
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

    if (strlen($hexhash) != 40) {
        $message = "Error: Info hash must be exactly 40 hex bytes. Contact an admin to fix this";
    }

    // Replace punctuation characters with spaces
    if (!$message) {
        $torrent = str_replace("_", " ", $torrent);

        try {
            DB::executeUpdate('
                INSERT INTO torrents (search_text, filename, owner, visible, info_hash, name, size,
                    numfiles, type, descr, ori_descr, category, save_as, added, last_action, nfo)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                    [
                        searchfield("$shortfname $dname $torrent"), $fname, $CURUSER["id"], "no",
                        $hexhash, $torrent, $totallen, count($filelist), $type, $descr, $descr,
                        0 + $_POST["type"], $dname, get_date_time(), get_date_time(), ''
                    ]
            );
        // } catch (PDOException $e) {
        } catch (\Exception $e) {
            // http://php.net/manual/en/class.pdoexception.php
            if ($e->getCode() === 1062) {
                // duplicate entry
                $message = "Torrent already uploaded!";
            } else {
                $message = 'DB Error: ' . $e->getMessage();
            }
        }

        if (! $message) {
            $id = DB::lastInsertId();

            DB::query("DELETE FROM files WHERE torrent = $id"); // ??? what? todo: check logic for this line
            foreach ($filelist as $file) {
                DB::insert('files', ['torrent' => $id, 'filename' => $file[0], 'size' => $file[1]]);
            }

            move_uploaded_file($tmpname, "$torrent_dir/$id.torrent");

            write_log("Torrent $id (".h($torrent).") was uploaded by " . $CURUSER["username"]);

            if (isset($_POST['request'])) {
                $req_id = (int) $_POST['request'];
                if ($req_id > 0) {
                    /* PM for requested user */
                    $row = DB::fetchAssoc('SELECT `userid` FROM `requests` WHERE `id` = ' . $req_id);
                    $re_msg = "Your request \"$torrent\" was filled by " . $CURUSER["username"] . ".You can download it <a href=".
                        $SITEURL."/torrents-details.php?id=$id>HERE</a>";
                    if ($row) {
                        DB:insert('messages', [
                            'poster' => 0, 'sender' => 0, 'receiver' => $row['userid'],
                            'added' => get_date_time(), 'msg' => $re_msg
                        ]);
                    }
                    // requests delete
                    DB::query("DELETE FROM `requests` WHERE `id` = ". $req_id);
                    DB::query("DELETE FROM `addedrequests` WHERE `requestid` = ". $req_id);
                    write_log("The request ($torrent) was filled by " . $CURUSER["username"] . "");
                }
            }

            if ($DHT) {
                // Code to write the updated dictionary to the torrent file
                $fp = fopen("$torrent_dir/$id.torrent", "w");
                if ($fp) {
                    @fwrite($fp, benc($dict), strlen(benc($dict)));
                    fclose($fp);
                }
                // End of code to write the updated dictionary to the torrent file
            }

            $arr = DB::fetchAssoc("SELECT name FROM categories WHERE id = $catid");
            $cat = $arr["name"];
            $res = DB::fetchAll("SELECT email FROM users WHERE enabled='yes' AND notifs LIKE '%[cat$catid]%'");
            $uploader = $CURUSER['username'];

            $size = mksize($totallen);
            $description = ($html ? strip_tags($descr) : $descr);

            // EMAIL NOTIFICATION
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

$SITEURL/torrents-details.php?id=$id

--
$SITENAME
EOD;
            $to = "";
            $nmax = 100; // Max recipients per message
            $nthis = 0;
            $ntotal = 0;
            $total = count($res[0] ?? []);
            foreach ($res as $arr) {
                if ($nthis == 0)
                    $to = $arr[0];
                else
                    $to .= "," . $arr[0];
            
                ++$nthis;
                ++$ntotal;
                if ($nthis == $nmax || $ntotal == $total) {
                    if (!mail("Multiple recipients <$SITEEMAIL>", "New torrent - $torrent", $body,
                        "From: $SITEEMAIL\r\nBcc: $to", "-f$SITEEMAIL")
                    ) {
                        stderr("Error", "Your torrent has been been uploaded. DO NOT RELOAD THE PAGE!\n" .
                            "There was however a problem delivering the e-mail notifcations.\n" .
                            "Please let an administrator know about this error!\n");
                    }
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
                To download a copy of the .torrent file you just uploaded so you can seed - <a href=\"download.php?id=$id&name=$fname\">CLICK HERE</a> - <br>
                <br>
                Описание вашей раздачи можно будет увидеть по этой <a href=\"torrents-details.php?id=$id\">ссылке</a>
                ", 'SUCESS');
        }
    }
}

stdhead("Upload");
begin_frame($txt['UPLOAD_RULES']);
?>
<br>
<ol>
<li>All releases must include a description.</li>
<li>Try to make sure your torrents are well-seeded for at least 24 hours.</li>
<li>Do not re-release material that is still active.</li>
</ol>

<?php
end_frame();

begin_frame($txt['UPLOAD']);
$max_torrent_size_nice = mksize($max_torrent_size);

if ($message != '')
  bark2($txt['UPLOAD_FAILED'], $message);
?>
<form enctype="multipart/form-data" action="torrents-upload.php" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="<?= $max_torrent_size ?>" />
<table border="0" cellspacing="0" cellpadding="6" align="center">
<?php
tr($txt['ANNOUNCE'], $announce_urls[0], 1);
tr($txt['TORRENT_FILE'], '<input type="file" name="file" size=50 value=""><br>' . $txt['MAX_SIZE_T'] . ' ' . $max_torrent_size_nice, 1);
tr($txt['TNAME'], '<input type=text name=name size=60 value=' . ($_POST['name'] ?? '') . ">", 1);
tr($txt['TDESC'], '<textarea name=descr rows=7 cols=45>' . $descr . '</textarea>' .
    '<br>' . $txt['NO_HTML'], 1);

$s = "<select name=\"type\">\n<option value=\"0\">" . $txt['CHOOSE_ONE'] . "</option>\n";

$cats = genrelist();
foreach ($cats as $row) {
	$s .= "<option value=\"" . $row["id"] . "\">" . h($row["name"]) . "</option>\n";
}

$s .= "</select>\n";
tr($txt['TTYPE'], $s, 1);

// Request filled?
if ($REQUESTSON) {
    $res = DB::fetchAll('SELECT `id`, `request` FROM requests ORDER BY `request` ASC');
    if ($res) {
        $request = "<select name=\"request\">\n<option value=\"0\">(Chose the request to be filled)</option>\n";
        foreach ($res as $row) {
            $request .= "<option value=\"" . $row["id"] . "\">" . h($row["request"]) . "</option>\n";
        }
        $request .= "</select>\n";
        tr("If your upload is to fill a resquest, select it here", $request , 1);
    }
}
//end requests

?>
<tr><td></td><td><input type="submit" value="<?= $txt['UPLOADT'] ?>" /></td></tr>
</table>
</form>
<?php

end_frame();
stdfoot();

