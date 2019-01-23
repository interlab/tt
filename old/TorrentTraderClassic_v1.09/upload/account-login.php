<?php
//
// CSS and Language updated 30.11.05
//
ob_start();
require_once("backend/functions.php");
dbconn();

unset($returnto);
if (!empty($_GET["returnto"])) {
	$returnto = $_GET["returnto"];
	if (!isset($_GET["nowarn"])) {
		$message = "Sorry this page is only for members.";
	}
}

$user_name = $_POST['username'] ?? false;
$user_password = $_POST['password'] ?? false;

if ($user_name && $user_password) {
    $password = md5($user_password);

    $row = DB::fetchAssoc('
    SELECT id, password, secret, enabled
    FROM users
    WHERE username = ?
        AND status = ?', [$user_name, 'confirmed']);

	if (!$row)
		$message = "Username Incorrect";
	elseif ($row["password"] != $password)
		$message = "Password Incorrect";
	elseif ($row["enabled"] == "no")
		$message = "This account has been disabled by an administrator.";
	else {
		logincookie($row["id"], $row["password"], hash_pad($row["secret"], 20));
		if (!empty($_POST["returnto"])) {
			header("Refresh: 0; url=" . $_POST["returnto"]);
			die();
		}
		else {
			header("Refresh: 0; url=index.php");
			die();
		}
	}
}

logoutcookie();

stdhead("Login");

begin_frame($txt['LOGIN']);

if ($message != '') {
	bark2("Access Denied", $message);
}
?>

<form method="post" action="account-login.php">
	<div align="center">
	<table border="0" cellpadding=5>
		<tr><td><B><?= $txt['USERNAME'] ?>:</B></td><td align=left><input type="text" size=40 name="username" /></td></tr>
		<tr><td><B><?= $txt['PASSWORD'] ?>:</B></td><td align=left><input type="password" size=40 name="password" /></td></tr>
		<tr><td colspan="2" align="center"><input type="submit" value="<?= $txt['LOGIN'] ?>" class="btn"><BR><BR><i><?= $txt['COOKIES'] ?></i></td></tr>
	</table>
	</div>
<?php

if (isset($returnto))
	echo '<input type="hidden" name="returnto" value="' . h($returnto) . '" />';

?>

</form>
<p align="center">
    <a href="account-signup.php"><?= $txt['REGISTERNEW'] ?></a> | 
    <a href="account-recover.php"><?= $txt['RECOVER_ACCOUNT'] ?></a> | <a href="account-delete.php"><?= $txt['DELETE_ACCOUNT'] ?></a>
</p>

<?php
end_frame();
stdfoot();
?>

