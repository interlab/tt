<?php
   // =================================== //
  // TorrentTrader v1.06+ Installer v1.0 //
 //           by TorrentialStorm        //
// =================================== //

$installed = false;
$PHP_SELF = '';

if (PHP_MAJOR_VERSION < 7) {
    die('Your version not support. You will should be used php >= 7');
}

@session_start();

$_SESSION['MYSQL_HOST'] = $_SESSION['MYSQL_HOST'] ?? '';
$_SESSION['MYSQL_USER'] = $_SESSION['MYSQL_USER'] ?? '';
$_SESSION['MYSQL_PASS'] = $_SESSION['MYSQL_PASS'] ?? '';
$_SESSION['MYSQL_DB'] = $_SESSION['MYSQL_DB'] ?? '';
$_SESSION['ADMIN_COUNTRY'] = $_SESSION['ADMIN_COUNTRY'] ?? '';
$_SESSION['ADMIN_NAME'] = $_SESSION['ADMIN_NAME'] ?? '';
$_SESSION['ADMIN_PASS'] = $_SESSION['ADMIN_PASS'] ?? '';
$_SESSION['ADMIN_PASS2'] = $_SESSION['ADMIN_PASS2'] ?? '';
$_SESSION['ADMIN_EMAIL'] = $_SESSION['ADMIN_EMAIL'] ?? '';
$_SESSION['ADMIN_AGE'] = $_SESSION['ADMIN_AGE'] ?? '';
$_SESSION['ADMIN_CLIENT'] = $_SESSION['ADMIN_CLIENT'] ?? '';


require_once 'includes/functions.php';
if (empty($_POST['page']))
    $_POST['page'] = '';

$docroot = str_replace('/install', '', str_replace('\\', '/', getcwd()));
require("includes/config.php");

if ($installed) {
    head("Failed");
    begin_frame("Error");
    echo "<center><h1>The installer has already been completed.</h1>
    <h3>If you need to run it again empty includes/config.php</h3></center>";
    end_frame();
    die;
}

switch ($_POST['page']) {
    case 'environment':
        $phpver = phpversion();
        if ($phpver >= 7)
            $phpver = "<font color=green>OK</font> - Your PHP version is $phpver";
        else
            $phpver = "<font color=red>BAD</font>, Only PHP 7.0+ is supported - Your PHP version is $phpver";

        $registerglobals = ini_get('register_globals');
        if (!$registerglobals)
            $registerglobals = "<font color=green>OFF</font>";
        else
            $registerglobals = "<font color=red>ON</font>";

        head("Server Environment");
        begin_frame("Server Environment");
        echo <<<EOD
    <table>
      <tr>
        <td><b>PHP Version:</b></td>
        <td><b>$phpver</b></td>
      </tr>
      <tr>
        <td><b>Document Root:</b></td>
        <td>$docroot</td>
      </tr>
      <tr>
        <td><b>Register_Globals:</b></td>
        <td><b>$registerglobals</b></td>
      </tr>
    </table>
    <form action="$PHP_SELF" method="post">
      <input type="hidden" name="page" value="sql-config"> <input type="submit" value=
      'Continue'>
    </form>
EOD;
            end_frame();
        break;

        case 'sql-config':
            head("Database Settings");
            begin_frame("Database Settings");
            echo <<<EOD
    Please enter the following data to set up a connection to your MySQL database
    server.This database must exist and have read/write privileges.<br>
    <form action='$PHP_SELF' method="post">
      <input name="page" type="hidden" value='sql-connection'> 
      Hostname or IP address of MySQL database server:<br>
      (if you are unsure use 'localhost')<br>
      
      <input name="MYSQL_HOST" type="text" value='{$_SESSION['MYSQL_HOST']}' size="45" maxlength="45"><br>
      
      Username:<br>
      <input name="MYSQL_USER" type="text" value='{$_SESSION['MYSQL_USER']}' size="45" maxlength="45"><br>
      
      Password:<br>
      <input name="MYSQL_PASS" type="password" value='{$_SESSION['MYSQL_PASS']}' size="45" maxlength="45"><br>
      
      Database Name:<br>
      <input name="MYSQL_DB" type="text" value='{$_SESSION['MYSQL_DB']}' size="45" maxlength="45"><br>
      
      Internal Root path:<br>
      (don't change if you are unsure)<br>
      <input name="ROOT_PATH" type="text" value='$docroot' size="45" maxlength='60'><br>
      
      <input type="submit" name="submit" value='Continue'>
    </form>
EOD;
            end_frame();
        break;
        
        case 'sql-connection':
            $message = null;
            if (!$_POST['MYSQL_HOST'])
                $message .= "MySQL host is not set.<BR>";
            if (!$_POST['MYSQL_USER'])
                $message .= "MySQL user is not set.<BR>";
            if (!$_POST['MYSQL_PASS'])
                $message .= "MySQL password is not set.<BR>";
            if (!$_POST['MYSQL_DB'])
                $message .= "MySQL database is not set.<BR>";

            $_SESSION['MYSQL_HOST'] = $_POST['MYSQL_HOST'];
            $_SESSION['MYSQL_USER'] = $_POST['MYSQL_USER'];
            $_SESSION['MYSQL_PASS'] = $_POST['MYSQL_PASS'];
            $_SESSION['MYSQL_DB'] = $_POST['MYSQL_DB'];
            $_SESSION['ROOT_PATH'] = $_POST['ROOT_PATH'];

            if ($message != '') {
                $message .= "<BR><BR>Click 'Back' to try again.<BR><BR>
                <form method=POST action=$PHP_SELF>
                <input type=hidden value='sql-config' name='page'/>
                <input type=submit value='Back' />
                </form>";
                bark('Data missing',$message);
            }
            head("Verifying Database Settings");
            begin_frame('');

            $conn = new mysqli($_POST['MYSQL_HOST'], $_POST['MYSQL_USER'], $_POST['MYSQL_PASS']);
            if ($conn->connect_error) {
                // die("Connection failed: " . $conn->connect_error);

                echo '
                    <h1>Database Settings</h1>
                    <img src="images/no.gif"> Error connecting to database server
                    <b>' . $_SESSION['MYSQL_HOST'] . '</b> using username <b>' . $_SESSION['MYSQL_USER'] . '</b> and
                    password <b>' . $_SESSION['MYSQL_PASS'] . '</b> :<br>
                    error ' . $conn->connect_error . '
                    <BR><BR>Click \'Back\' and check your settings
                    <form action="." method="post">
                        <input type="hidden" name="page" value="sql-config">
                        <input type="submit" value="Back">
                     </form>';
            } else {

                if ( ! $conn->select_db($_SESSION['MYSQL_DB'])) {
                    echo '
    <h1>Database Settings</h1>
    <img src="images/no.gif"> Could not select database "' . $_SESSION['MYSQL_DB'] . '".<br>
    
    Click Continue to try to create the database. Or click Back to change your settings.<br>
    <br>
    <form action="." method="post">
      <input type="submit" name="submit" value="Back">
    </form>
    <form action="." method="post">
      <input name="page" type="hidden" value="create-database"> <input type="submit"
      name="submit" value="Continue">
    </form>';
                } else {
                    echo '
    <h1>Database Settings</h1>
    <img src="images/yes.gif"> Connection to database server established successfully.
    <form action="." method="post">
      <input name="page" type="hidden" value="write-database"> <input type="submit" name="submit" value="Continue">
    </form>';
                }

                $conn->close();
            }

            end_frame();

        break;

        case 'create-database':
            head("Creating Database");
            begin_frame("");
            $conn = new mysqli($_SESSION['MYSQL_HOST'], $_SESSION['MYSQL_USER'], $_SESSION['MYSQL_PASS']);
            $create = $conn->query('CREATE DATABASE ' . $_SESSION['MYSQL_DB']);
            $conn->close();

            if (!$create) {
                echo <<<EOD
    <h1>Create Database</h1>
    <img src="images/no.gif"> The database '<b>{$_SESSION['MYSQL_DB']}</b>' could not
    be created.<br>
    Click Back to try to insert a new database configuration.
    <form action='.' method='post'>
      <input name='page' type='hidden' value='sql-config'>
      <input type='submit' name='submit' value='Back'>
    </form>
EOD;
            } else {
                echo <<<EOD
    <h1>Create Database</h1>
    <img src='images/yes.gif'> The database "<b>{$_SESSION['MYSQL_DB']}</b>" has been
    created.
    <form action='.' method='post'>
      <input name='page' type='hidden' value='write-database'>
      <input type='submit' name='submit' value='Continue'>
    </form>
EOD;
            }
            end_frame();
        break;

        case 'write-database':
            head("Writing Database");
            $message = null;
            $conn = new mysqli($_SESSION['MYSQL_HOST'], $_SESSION['MYSQL_USER'], $_SESSION['MYSQL_PASS'], $_SESSION['MYSQL_DB']);
            if (!$conn) {
                echo <<<EOD
    <h1>Write Database</h1>
    Couldn't connect to the database..<br>
    (This is strange, because the same test was passed a few steps ago.)<br>
    
    <form action="." method="post">
      <input name="nextpage" type="hidden" value='write-database'>
      <input type="submit" name="submit" value="Recheck">
    </form>
EOD;
            } else {
                foreach(explode('||', file_get_contents('./includes/database.sql')) as $query) {
                    $query = trim($query);
                    if (!$conn->query($query)) {
                        $message .= "<img src='images/no.gif'>". $conn->error ."<BR>";
                    }
                }
                $conn->close();
            }

            if (!empty($message)) {
                echo <<<EOD
    <h1>Write Database</h1>
    Couldn't write to the database!<br>
    Are you sure the database is empty?<br>
    $message
    <form action="." method="post">
      <input name="page" type="hidden" value='write-database'>
      <input type="submit" name="submit" value='Refresh'>
    </form>
EOD;
            } else {
                echo <<<EOD
    <h1>Write Database</h1>
    <img src="images/yes.gif">All tables have been written successfully.<br>
    <br>
    <form action="." method="post">
      <input name="page" type="hidden" value="setup-admin">
      <input type="submit" name="submit" value="Continue">
    </form>
EOD;
            }

        break;
        
        case 'setup-admin':
            head("Create Admin Account");
            $link = new mysqli($_SESSION['MYSQL_HOST'], $_SESSION['MYSQL_USER'], $_SESSION['MYSQL_PASS'], $_SESSION['MYSQL_DB']);

            $nuIP = getip();
            $dom = @gethostbyaddr($nuIP);
            if ($dom == $nuIP || @gethostbyname($dom) != $nuIP)
                $dom = "";
            else {
                $dom = strtoupper($dom);
                preg_match('/^(.+)\.([A-Z]{2,3})$/', $dom, $tldm);
                $dom = $tldm[2];
            }

            $countries = "<option value='0'>---- None selected ----</option>\n";
            $ct_r = $link->query("SELECT id, name, domain from countries ORDER BY name") or die;
            while ($ct_a = $ct_r->fetch_assoc()) {
                $countries .= "\t\t\t\t\t\t<option value=\"$ct_a[id]\"";
                if ($dom == $ct_a["domain"] || $_SESSION['ADMIN_COUNTRY'] == $ct_a['id']) $countries .= " SELECTED";
                $countries .= ">$ct_a[name]</option>\n";
            }
            $date = date("D dS M, Y h:i a");
            echo <<<EOD
    <h1>Creating Administrator Account</h1>
    Please fill out the following form to create your administrator account.
    <form method="post" action=".">
      <table cellspacing="0" cellpadding="2" border="0">
        <tr>
          <td>Username: <font class="small" color="#FF0000">*</font></td>

          <td><input type="text" size="40" name="admin_name" value="{$_SESSION['ADMIN_NAME']}"></td>
        </tr>
        <tr>
          <td>Password: <font class="small" color="#FF0000">*</font></td>
          <td><input type="password" size="40" name="admin_pass" value="{$_SESSION['ADMIN_PASS']}"></td>
        </tr>
        <tr>
          <td>Confirm Password: <font class="small" color="#FF0000">*</font></td>
          <td><input type="password" size="40" name="admin_pass2" value="{$_SESSION['ADMIN_PASS2']}"></td>
        </tr>
        <tr>
          <td>Email Address: <font class="small" color="#FF0000">*</font></td>
          <td><input type="text" size="40" name="admin_email" value="{$_SESSION['ADMIN_EMAIL']}"></td>
        </tr>
        <tr>
          <td>Age: <font class="small" color="#FF0000">*</font></td>
          <td><input type="text" size="40" name="admin_age" maxlength="3" value="{$_SESSION['ADMIN_AGE']}"></td>
        </tr>
        <tr>
          <td>Country: <font class="small" color="#FF0000">*</font></td>
          <td><select name="admin_country" size="1">
          $countries
          </select></td>
        </tr>
        <tr>
          <td>Gender: <font class="small" color="#FF0000">*</font></td>
          <td><input type="radio" name="admin_gender" value="Male">Male &nbsp;&nbsp;
          <input type="radio" name="admin_gender" value="Female">Female</td>
        </tr>
        <tr>
          <td>Preferred BitTorrent Client: <font class="small" color=
          "#FF0000">*</font></td>
          <td><input type="text" size="40" name="admin_client" maxlength="20" value=
          "{$_SESSION['ADMIN_CLIENT']}"></td>
        </tr>
        <tr>
          <td>Signup Time:</td>
          <td><b>$date</b></td>
        </tr>
        <tr>
          <td align="middle" colspan="2">
            <input name="page" type="hidden" value="create-admin">
            <input type="submit" name="submit" value="Continue">
          </td>
        </tr>
      </table>
    </form>
EOD;

            $link->close();
        break;

        case 'create-admin':
            $_SESSION['ADMIN_NAME'] = $_POST['admin_name'];
            $_SESSION['ADMIN_PASS'] = $_POST['admin_pass'];
            $_SESSION['ADMIN_PASS2'] = $_POST['admin_pass2'];
            $_SESSION['ADMIN_EMAIL'] = $_POST['admin_email'];
            $_SESSION['ADMIN_AGE'] = $_POST['admin_age'];
            $_SESSION['ADMIN_CLIENT'] = $_POST['admin_client'];
            $_SESSION['ADMIN_GENDER'] = $_POST['admin_gender'] ?? 'Male';
            $_SESSION['ADMIN_COUNTRY'] = $_POST['admin_country'];

            head("Writing Admin Account");
            require_once __DIR__ . '/../libs/vendor/autoload.php';
            require_once __DIR__ . '/../helpers/DB.php';
            my_pdo_connect($_SESSION['MYSQL_DB'], $_SESSION['MYSQL_USER'], $_SESSION['MYSQL_PASS'], $_SESSION['MYSQL_HOST']);

            $message = null;

            if (empty($_SESSION['ADMIN_PASS'])
                || empty($_SESSION['ADMIN_EMAIL'])
                || empty($_SESSION['ADMIN_AGE'])
                || empty($_SESSION['ADMIN_CLIENT'])
                || empty($_SESSION['ADMIN_GENDER'])
                || $_SESSION['ADMIN_COUNTRY'] == 0
            ) {
                $message = "Don't leave any required field blank.<BR>";
            }
            if (strlen($_SESSION['ADMIN_NAME']) > 12)
                $message .= "Sorry, the username is too long (max is 12 chars)<BR>";
            if ($_SESSION['ADMIN_PASS'] != $_SESSION['ADMIN_PASS2'])
                $message .= "The passwords didn't match! Must've typoed. Try again.<BR>";
            if (strlen($_SESSION['ADMIN_PASS']) < 6)
                $message .= "Sorry, the password is too short (min is 6 chars)<BR>";
            if (strlen($_SESSION['ADMIN_PASS']) > 40)
                $message .= "Sorry, the password is too long (max is 40 chars)<BR>";
            if ($_SESSION['ADMIN_PASS'] == $_SESSION['ADMIN_NAME'])
                $message .= "Sorry, the password cannot be same as username.<BR>";
            if (!validusername($_SESSION['ADMIN_NAME']))
                $message .= "The username is not valid.";

            $secret = mksecret();
            $wantpassword = password_hash($_SESSION['ADMIN_PASS'], PASSWORD_DEFAULT);
            $now = date('Y-m-d H:i:s');

            if (empty($message)) {
                $ret = false;
                try {
                    $ret = DB::executeUpdate('
                        INSERT INTO users (username, real_name, password, secret, email, status, added, age,
                            country, gender, client, class, about_myself, passkey)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                        [$_SESSION['ADMIN_NAME'], $_SESSION['ADMIN_NAME'], $wantpassword,
                        $secret, $_SESSION['ADMIN_EMAIL'], 'confirmed',
                        $now, $_SESSION['ADMIN_AGE'], $_SESSION['ADMIN_COUNTRY'],
                        $_SESSION['ADMIN_GENDER'], $_SESSION['ADMIN_CLIENT'], 5,
                        '', generateRandomString(32)]
                    );
                } catch (Exception $e) {
                    $error = 'Error occurred:' . nl2br($e);
                }
                if (!$ret) {
                    echo <<<EOD
<h1>Admin Account</h1>
The account has NOT been saved into the database.<br>
<img src=images/no.gif /> MySQL Error: $error
<form action="." method="post">
<input name="page" type="hidden" value="setup-admin">
<input type="submit" name="submit" value="Back">

</form>
EOD;
                } else {
                    $_SESSION['ADMIN_PASS'] = null;
                    $_SESSION['ADMIN_PASS2'] = null;
                    echo <<<EOD
    <h1>Admin Account</h1>

    <img src='images/yes.gif'> The account has been saved into the database.<br>
    <form action="." method="post">
      <input name="page" type="hidden" value="setup-config"> <input type="submit" name="submit" value="Continue">
    </form>
EOD;
                }
            } else {
                echo <<<EOD
    <h1>Admin Account</h1>
    The account has NOT been saved into the database.<br>
    <img src="images/no.gif">$message
    <form action="." method="post">
      <input name="page" type="hidden" value="setup-admin">
      <input type="submit" name="submit" value="Back">
    </form>
EOD;
            }

        break;

        case 'setup-config':
            @chmod("../backend/config.php", 0777);
            @chmod("../backend/oldconfig.php", 0777);
            @chmod("../backups", 0777);
            @chmod("../uploads", 0777);
            @chmod("../disclaimer.txt", 0777);
            @chmod("../sponsors.txt", 0777);
            @chmod("../banners.txt", 0777);

            require_once($_SESSION['ROOT_PATH']."/backend/config.php");

            $_SESSION['SITENAME'] = $_SESSION['SITENAME'] ?? '';
            $_SESSION['SITEURL'] = "http://".$_SERVER['HTTP_HOST'].preg_replace("/\/install\/*/", "", $_SERVER['REQUEST_URI']);
            $_SESSION['SITEEMAIL'] = $_SESSION['SITEEMAIL'] ?? '';

            head("Setup Config");
            echo <<<EOD
    <h1>Final Stage: Tracker Configuration</h1>
    <p>You MUST fill in this form.</p>
    <form method="post" action=".">
      <table cellspacing="0" cellpadding="2" border="0">
        <tr>
          <td>Tracker Name: <font class="small"><font color="#FF0000">*</font></font></td>
          <td><input type="text" size="40" name="SITENAME" value="{$_SESSION['SITENAME']}">
          </td>
        </tr>
        <tr>
          <td>Tracker URL: <font class="small"><font color="#FF0000">*</font><br>
          <i>(leave auto-fill if unsure)</i></font></td>
          <td><input type="text" size="40" name="SITEURL" value="{$_SESSION['SITEURL']}">
          </td>
        </tr>
        <tr>
          <td>Tracker Email: <font class="small"><font color="#FF0000">*</font></font></td>
          <td><input type="text" size="40" name="SITEEMAIL" value="{$_SESSION['SITEEMAIL']}">
          </td>
        </tr>
        <tr>
          <td align="middle" colspan="2"><input name="page" type="hidden" value="write-config">
          <input type="submit" name="submit" value="Continue"></td>
        </tr>
      </table>
    </form>
EOD;
            break;

        case 'write-config':
            head("Writing Config");
            $_SESSION['SITENAME'] = $_POST['SITENAME'];
            $_SESSION['SITEURL'] = $_POST['SITEURL'];
            $_SESSION['SITEEMAIL'] = $_POST['SITEEMAIL'];

            if (empty($_SESSION['SITENAME']) || empty($_SESSION['SITEEMAIL']) || empty($_SESSION['SITEURL']))
                $message = "Don't leave any required field blank.";

            if (empty($message)) {
                $newconfig = <<<EOD
<?php

// MySQL Settings (please change these to reflect your MYSQL settings, all other settings can be changed via adminCP)
\$mysql_host = "{$_SESSION['MYSQL_HOST']}";
\$mysql_user = "{$_SESSION['MYSQL_USER']}";
\$mysql_pass = "{$_SESSION['MYSQL_PASS']}";
\$mysql_db = "{$_SESSION['MYSQL_DB']}";

// Default Language / Theme Settings (These are currently set via the databases DEFAULT values, NOT THE ADMIN CP)
\$language = "1";
\$theme = "1";

// Site Settings
\$SITENAME = "{$_SESSION['SITENAME']}";
\$SITEEMAIL = "{$_SESSION['SITEEMAIL']}";
\$SITEURL = "{$_SESSION['SITEURL']}";
\$SITE_ONLINE = true;
\$OFFLINEMSG = "Site is down for a little while";
\$UPLOADERSONLY = false;
\$LOGGEDINONLY = false;
\$INVITEONLY = false;
\$ACONFIRM = false;
\$WELCOMEPMON = true;
\$CENSORWORDS = true;
\$MAXDISPLAYLENGTH = "45";
\$WELCOMEPMMSG = "Thank you for registering at our tracker!

Please remember to keep your ratio at 1.00 or greater :)";
\$DHT = false;
\$POLLON = false;

//Setup Site Blocks
\$SITENOTICEON = true;
\$REMOVALSON = true;
\$NEWSON = true;
\$DONATEON = true;
\$DISCLAIMERON = true;
\$SITENOTICE = "Welcome To TorrentTrader";
\$SHOUTBOX = true;
\$FORUMS = true;
\$REQUESTSON = true;

//setup IRC Chat
\$IRCCHAT = false;
\$IRCCHANNEL = "#torrenttrader";
\$IRCSERVER1 = "irc.p2p-irc.net";
\$IRCSERVER2 = "";
\$IRCSERVER3 = "";

//Setup IRC Announcer
\$IRCANNOUNCE = false;
\$ANNOUNCEIP = "x.x.x.x";
\$ANNOUNCEPORT= "2500";

//WAIT TIME VARS
\$GIGSA= "1";
\$RATIOA= "0.50";
\$WAITA= "24";
\$GIGSB= "3";
\$RATIOB= "0.65";
\$WAITB= "12";
\$GIGSC= "5";
\$RATIOC= "0.80";
\$WAITC= "6";
\$GIGSD= "7";
\$RATIOD= "0.95";
\$WAITD= "2";

//RATIO WARNING VARS
\$RATIO_WARNINGON = "true";//ratiowarn on/off
\$RATIOWARN_AMMOUNT = "0.50"; //user warned if this ratio is held
\$RATIOWARN_TIME = "10"; //ammount of time for user have have poor ratio before warning
\$RATIOWARN_BAN = "5"; //ammount of time after warning to auto-ban user.

// Tracker Settings
\$torrent_dir = "{$_SESSION['ROOT_PATH']}/uploads";
\$nfo_dir = "{$_SESSION['ROOT_PATH']}/uploads";
\$image_dir = "{$_SESSION['ROOT_PATH']}/uploads";
\$announce_urls = array();
\$announce_urls[] = "{$_SESSION['SITEURL']}/announce.php";
\$GLOBALBAN = false;
\$MEMBERSONLY = true;
\$MEMBERSONLY_WAIT = true;
\$RATIO_WARNINGON = true;
\$PEERLIMIT = "10000";

// Advanced Settings for announce and cleanup
\$autoclean_interval = "600";
\$max_torrent_size = "1000000";
\$max_nfo_size = "1000000";
\$max_image_size = "1000000000000";
\$announce_interval = "1800";
\$signup_timeout = "259200";
\$minvotes = "1";
\$maxsiteusers = "10000";
\$max_dead_torrent_time = "21600";

EOD;

                $_SESSION['newconfig'] = $newconfig;

                $file = @fopen($_SESSION['ROOT_PATH']."/backend/config.php", "w");
                $write = @fwrite($file,$newconfig);
                @fclose($file);
                if((!$file) || (!$write))
                    echo <<<EOD
    <h1>Setup Configuration</h1>
    <img src="images/no.gif"> The data has NOT been saved into the config file. (You probably have a chmod problem!) <BR>
    Click "Continue" to download a copy of the config you can upload with FTP.
    <form action="." method="post">
      <input name="page" type="hidden" value="show-config">
      <BR>
      <input type="submit" value="Continue">
    </form>
    <form action="." method="post">
      <input name="page" type="hidden" value="setup-config">
      <input type="submit" name="submit" value="Back">
    </form>
EOD;

                else
                    echo <<<EOD
    <h1>Setup Configuration</h1>
    <img src="images/yes.gif"> The configuration has been saved into the config file. 
    <form action="." method="post">
      <input name="page" type="hidden" value="goodbye">
      <input type="submit" name="submit" value="Finish">
    </form>
EOD;

            } else
                echo <<<EOD
    <h1>Setup Configuration</h1>
    The data has NOT been saved into the config file.<BR>
    <img src='images/no.gif'> Error: $message 
    <form action="." method="post">
      <input name="page" type="hidden" value="setup-config">
      <input type="submit" name="submit" value="Back">
    </form>
EOD;

            break;
            
        case 'show-config':
            header("Content-Type: text/plain");
            header("Content-Disposition: attachment; filename=\"config.php\"");
            header("Content-Length: ".strlen($_SESSION['newconfig']));
            echo $_SESSION['newconfig'];
            die;
        break;

        case 'goodbye':
            $siteurl = $_SESSION['SITEURL'];
            session_unset();
            session_destroy();
            head("Success");
            @chmod("includes/config.php", 0777);
            $conf = @fopen("includes/config.php", "w");
            @fwrite($conf, "<?php\r\$installed = true;\r\n?>");
            @fclose($conf);
echo <<<EOD
    <h1>Success!</h1>
    Your site <b><a href="{$siteurl}">here</a></b>
    <br>
    TorrentTrader Classic is now installed.<br>
    A few tips before you leave the installer: <br>
     <ul>
      <li>Please remove /install folder</li>
      <li>Look at <a href="http://www.torrenttrader.org/">the Offical TorrentTrader website</a> for support and updates</li>
    </ul>
    If you want support, more hacks etc etc... visit <a href="http://www.torrenttrader.org">the Offical TorrentTrader website</a> to see how to become Premium member.<br>
    <br>
    Please now ensure all other CHMOD's are correct via the checker <a href="../check.php">here</a><br>
EOD;
            break;
        default:
            head("Welcome");
            begin_frame("Welcome");
            echo <<<EOD
    <h3>Welcome to the Install Wizard for TorrentTrader Classic</h3>
    This Install Wizard will guide you through the installation of TorrentTrader
    Classic<br>
    Please ensure you have completed the following before you continue:<br>
    <ul>
      <li>Downloaded latest official build from the <a href='http://www.torrenttrader.org'>official website</a></li>
      <li>Unpacked the compressed archive</li>
      <li>Read the documentation contained in it</li>
      <li>Uploaded all neccessary files of TorrentTrader to your web server retaining the
      directory structure of the archive. TorrentTrader can be unpacked to a subfolder or
      subdomain with no additional configuration.</li>
      <li>backend/config.php and install/includes/config.php must both be CHMOD'ed to 777.</li>
      <li>Run The install wizard (well you must be if your reading this)</li>
    </ul>
    You should also make sure that your server meets all the requirements of
    TorrentTrader, the installer will check these when you continue.<br>
    <ul>
      <li>PHP Version 7</li>
      <li>MySQL</li>
    </ul>
    <br>
    <form action='$PHP_SELF' method='post'>
      <input type='hidden' name='page' value='environment'>
      <input type='submit' name='submit' value='Continue'>
    </form>
EOD;
           end_frame();
        break;
}

foot();

