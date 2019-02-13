<?php
//
// CSS and language updated 30.11.05
//
require_once __DIR__ . '/../../backend/functions.php';
dbconn(false);
loggedinorreturn();

global $CURUSER, $tzs;

if (!empty($_POST['submit'])) {
    $errors = [];

    $updateset = [];
    $changedemail = $newsecret = 0;

    $chpassword = $_POST['chpassword'] ?? '';
    $originalpassword = $_POST['originalpassword'] ?? '';
    $passagain = $_POST['passagain'] ?? '';
    $email = $_POST['email'] ?? '';

    // $query = '';
    $params = [];

    if ($chpassword != '') {
        // if ($CURUSER["password"] != md5($originalpassword))
        if (! password_verify($originalpassword, $CURUSER["password"]))
            $errors[] = $txt['THATS_NOT_YOUR_ORIGNAL_PASS'];
        if (strlen($chpassword) < 6)
            $errors[] = $txt['PASS_TO_SHORT'];
        if ($chpassword != $passagain)
            $errors[] = $txt['PASSWORDS_NOT_MATCH'];
        // $chpassword = md5($chpassword);
        $chpassword = password_hash($chpassword, PASSWORD_DEFAULT);
        $params['password'] = $chpassword;
        $newsecret = 1;
    }

    if ($email != $CURUSER["email"]) {
        if (! validemail($email)) {
            $errors[] = $txt['NOT_VAILD_EMAIL'];
        } else {
            // check if email addy is already in use
            $a = DB::fetchColumn('select count(*) from users where email = ?', [$email]);
            if ($a) {
                $errors[] = "The e-mail address $email is already in use.";
            }
            $changedemail = 1;
        }
    }

    $username = trim($_POST['username'] ?? '');
    if ($username != $CURUSER["username"]) {
        if (! validusername($username)) {
            $errors[] = "Invalid username.";
        } else {
            $a = DB::fetchColumn('
                SELECT count(*) from users where username = ?',
                [$username]
            );
            if ($a) {
                $errors[] = "The username $username is already in use.";
            }
        }
    }

    $acceptpms = $_POST["acceptpms"];
    $pmnotif = $_POST["pmnotif"] ?? '';
    $privacy = $_POST["privacy"];
    $notifs = ($pmnotif == 'yes' ? "[pm]" : '');

    $cats = genrelist();
    foreach ($cats as $row) {
        if (isset($_POST['cat' . $row['id']]) && $_POST['cat' . $row['id']] === 'yes') {
            $notifs .= "[cat$row[id]]";
        }
    }

    $avatar = strip_tags($_POST["avatar"]);
    $title = strip_tags($_POST["title"]);
    $signature = $_POST["signature"];
    $stylesheet = $_POST["stylesheet"];
    $commentpm = $_POST["commentpm"];
    $language = $_POST["language"] ?? '';
    $client = $_POST["client"];
    $age = $_POST["age"];
    $gender = $_POST["gender"];
    $country = $_POST["country"];
    $tzoffset = $_POST["tzoffset"];
    $privacy = $_POST["privacy"];

    $about_myself = strip_tags($_POST["about_myself"] ?? '');

    if (is_valid_id($stylesheet))
        $params['stylesheet'] = $stylesheet;
    if (is_valid_id($language))
        $params['language'] = $language;
    if (is_valid_id($country))
        $params['country'] = $country;

    $params['tzoffset'] = $tzoffset;
    if ($acceptpms == "yes")
        $acceptpms = 'yes';
    else
        $acceptpms = 'no';

    if (is_valid_id($age))
        $params['age'] = $age;

    $params['username'] = $username;
    $params['acceptpms'] = $acceptpms;
    $params['commentpm'] = $commentpm;
    $params['notifs'] = $notifs;
    $params['privacy'] = $privacy;
    $params['gender'] = $gender;
    $params['client'] = $client;
    $params['avatar'] = $avatar;
    $params['signature'] = $signature;
    $params['about_myself'] = $about_myself;
    $params['title'] = $title;

    if (empty($errors)) {
        if ($newsecret) {
            $sec = mksecret();
            $params['secret'] = $sec;
            logincookie($CURUSER["id"], $chpassword, $sec);
        }

        if ($changedemail) {
            $sec = mksecret();
            $hash = md5($sec . $email . $sec);
            $obemail = rawurlencode($email);
            $params['editsecret'] = $sec;
            $thishost = $_SERVER["HTTP_HOST"];
            $thisdomain = preg_replace('/^www\./is', '', $thishost);
            $body = <<<EOD
You have requested that your user profile (username {$CURUSER["username"]})
on $SITEURL should be updated with this email address ($email) as
user contact.

If you did not do this, please ignore this email. The person who entered your
email address had the IP address {$_SERVER["REMOTE_ADDR"]}. Please do not reply.

To complete the update of your user profile, please follow this link:

$SITEURL/account-ce.php?id={$CURUSER["id"]}&secret=$hash&email=$obemail

Your new email address will appear in your profile after you do this. Otherwise
your profile will remain unchanged.
EOD;

            mail($email, "$SITENAME profile change confirmation", $body, "From: $SITEEMAIL", "-f$SITEEMAIL");
            $mailsent = 1;
        }
        DB::update('users', $params, ['id' => $CURUSER['id']]);
        $edited = 1;
        $message = 'Account has been updated';
        header("Location: account.php?edited=$edited&message=$message&mailsent=$mailsent");
        die;
    }
}

$messages = numUserMsg();
$unread = numUnreadUserMsg();

stdhead("User CP");
begin_frame($txt['YOUR_SETTINGS']);

if (! empty($errors)) {
    echo '<h3>Errors:</h3><ul>';
    foreach ($errors as $error) {
        echo '<li>', $error, '</li>';
    }
    echo '</ul>';
}

?>
<form method=post action=account-settings.php>
<input type=hidden name=submit value=1>
<table border="0" cellspacing=0 cellpadding="5" width="100%">
<?php

$stylesheets = Helper::getStylesheets();

$countries = "<option value=0>----</option>\n";
$res = DB::query("SELECT id, name from countries ORDER BY name");
while ($ct_a = $res->fetch()) {
    $countries .= "<option value=$ct_a[id]" . ($CURUSER["country"] == $ct_a['id'] ? " selected" : '') . ">$ct_a[name]</option>\n";
}

ksort($tzs);

$timezone = '';
foreach ($tzs as $key => $val) {
    if ($CURUSER['tzoffset'] == $key) {
        $timezone .= "<option value=\"$key\" selected>$val</option>\n";
    } else {
        $timezone .= "<option value=\"$key\">$val</option>\n";
    }
}

tr('Real username', $CURUSER['real_name'], 1);
// todo: ajax check name on dublicate in db
tr('Showing username', '<input type="text" name="username" value="'.$CURUSER['username'].'">', 1);

$acceptpms = $CURUSER["acceptpms"] == "yes";
tr($txt['ACCOUNT_ACCEPTPM'], "<input type=radio name=acceptpms" . ($acceptpms ? " checked" : '') .
  " value=yes>From all <input type=radio name=acceptpms" .
  ($acceptpms ? '' : " checked") . " value=no>" . $txt['ACCOUNT_PMSTAFFONLY'], 1);

$gender = "<option value=Male" . ($CURUSER["gender"] == 'Male' ? " selected" : '') . ">" . $txt['MALE'] . "</option>\n"
     ."<option value=Female" . ($CURUSER["gender"] == 'Female' ? " selected" : '') . ">" . $txt['FEMALE'] . "</option>\n";

$torrentnotif = "<input type=checkbox checked>" . $txt['ACCOUNT_NOTIFY_WHEN_TORRENT_UPLOADED_IN'] . ":<br>";
$res = DB::query("SELECT id,name FROM categories ORDER by sort_index, name");

while ($a = $res->fetch()) {
    $torrentnotif .= "&nbsp;&nbsp;&nbsp;&nbsp;<input type=checkbox name=cat$a[id]"
        . (strpos($CURUSER['notifs'], "[cat$a[id]]") !== false ? " checked" : '') .
        " value='yes'>$a[name]<br>\n";
}

function priv($name, $descr)
{
    global $CURUSER;

    if ($CURUSER["privacy"] == $name) {
        return '<input type="radio" name="privacy" value="'.$name.'" checked="checked"> ' . $descr;
    }

    return '<input type="radio" name="privacy" value="'.$name.'"> '.$descr;
}

tr($txt['ACCOUNT_PRIVACY_LV'],  priv("normal", $txt['NORMAL']) . " " . priv("low", $txt['LOW']) . " " .
    priv("strong", $txt['STRONG'] . " <br>(Stong level will hide your ratio and make your uploads anonymous)"), 1);

print("<tr><td align=right>PM on Comments</td><td align=left><input type=radio name=commentpm" .
    ($CURUSER["commentpm"] == "yes" ? " checked" : '') . " value=yes>yes<input type=radio name=commentpm" .
    ($CURUSER["commentpm"] == "no" ? " checked" : '') . " value=no>no");

tr($txt['ACCOUNT_EMAIL_NOTIFICATION'], "<input type=checkbox name=pmnotif"
    . (strpos($CURUSER['notifs'], "[pm]") !== false ? " checked" : '') .
   " value=yes>" . $txt['ACCOUNT_PM_NOTIFY_ME'] . "<br>\n" .
   $torrentnotif, 1);


tr($txt['THEME'], "<select name=stylesheet>\n$stylesheets\n</select>",1);
tr($txt['CLIENT'], "<input type=text size=20 maxlength=20 name=client value=\"" . h($CURUSER["client"]) . "\" />", 1);
tr($txt['AGE'], "<input type=text size=4 maxlength=3 name=age value=\"" . h($CURUSER["age"]) . "\" />", 1);
tr($txt['GENDER'], "<select size=1 name=gender>\n$gender\n</select>", 1);
tr($txt['COUNTRY'], "<select name=country>\n$countries\n</select>", 1);
tr($txt['ACCOUNT_TIMEZONE'], "<select name=tzoffset>\n$timezone\n</select><br>" . $txt['ACCOUNT_TIMEZONEMSG'], 1);
tr($txt['AVATAR_URL'], "<input name=avatar size=50 value=\"" . h($CURUSER["avatar"]) .
  "\"><br>\n80x80 px", 1);
tr($txt['CUSTOMTITLE'], "<input name=title size=50 value=\"" . strip_tags($CURUSER["title"]) .
  "\"><br>\n " . $txt['HTML_NOT_ALLOWED'], 1);
tr($txt['SIGNATURE'], "<textarea name=signature cols=50 rows=10>" . h($CURUSER["signature"]) .
  "</textarea><br>\n " . $txt['HTML_NOT_ALLOWED'] . "",1);

tr('О себе<br>(эта информация будет видна в вашем профиле)', "<textarea name=about_myself cols=50 rows=10>".h($CURUSER["about_myself"])
    . "</textarea><br>\n " . $txt['HTML_NOT_ALLOWED'] . "",1);

tr($txt['EMAIL_ADDRESS'], "<input type=\"text\" name=\"email\" size=50 value=\"" . h($CURUSER["email"]) .
  "\"><br>\n" . $txt['REPLY_TO_CONFIRM_EMAIL'] . "<br>",1);
?>
<tr><td colspan="2" align="center"><input type="submit" value="<?= $txt['SUBMIT']
?>" style='height: 25px'> <input type="reset" value="<?= $txt['REVERT'] ?>" style='height: 25px'></td></tr>
</table>

<?php end_frame(); ?>

<br><br>

<?php begin_frame($txt['CHANGE_YOUR_PASS']); ?>

<table border="0" cellspacing=0 cellpadding="5" width="100%">
<?php
tr($txt['CURRENT_PASSWORD'], "<input type=\"password\" name=\"originalpassword\" size=\"50\" />", 1);
tr($txt['NEW_PASSWORD'], "<input type=\"password\" name=\"chpassword\" size=\"50\" />", 1);
tr($txt['REPEAT'], "<input type=\"password\" name=\"passagain\" size=\"50\" />", 1);
?>
<tr>
    <td colspan="2" align="center">
    <input type="submit" value="<?= $txt['SUBMIT'] ?>" style='height: 25px'> <input type="reset" value="<?= $txt['REVERT'] ?>" style='height: 25px'>
    </td>
</tr>
</table></form>

<?php

end_frame();
stdfoot();

