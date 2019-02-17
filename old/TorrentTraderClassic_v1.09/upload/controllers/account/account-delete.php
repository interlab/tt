<?php

require_once '../../backend/functions.php';
dbconn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!$username || !$password) {
        bark('Couldn\'t delete account', 'Please fill out the form correctly.');
    }

    $arr = DB::fetchAssoc('SELECT * FROM users WHERE username = ?', [$username]);
    if (! $arr || (! password_verify($password, $arr['password']))) {
        bark('Couldn\'t delete account', 'Bad user name or password. Please verify that all entered information is correct.');
    }

    $id = $arr['id'];
    $res = DB::executeUpdate('DELETE FROM users WHERE id = '. $id);
    if (! $res) {
        bark('Couldn\'t delete account', 'Unable to delete the account.');
    }

    bark('delete completed', 'The account <b>'.$username.'</b> were deleted.', 'Success');
}

stdhead('Delete account');

begin_frame('Delete Account');
?>
Thanks for using <b><?= $SITENAME ?></b>. If you are no longer in need of your 
account you can delete it using the form below. All your account details 
will be deleted but any .torrents which you uploaded will remian in 
place until all users have finished downloading from them.
Please be aware that deleted accounts cannot be recovered and that any 
priviledges accumulated on one account cannot be transferred if you choose 
to sign up again.<br><br>
<table border=0 cellspacing=0 cellpadding=5>
<form method=post action=account-delete.php>
<tr><td class=rowhead><b>Username:</b></td>
<td><input size=40 name=username></td></tr>
<tr><td class=rowhead><b>Password:</b></td>
<td><input type=password size=40 name=password></td></tr>
<tr><td colspan=2 align="right"><input type=submit value='Delete Account'></td></tr>
</table>
</form>

<?php
end_frame();
stdfoot();
