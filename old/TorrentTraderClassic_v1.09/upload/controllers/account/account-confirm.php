<?php

// Confirm account Via email and send PM

require_once __DIR__ . '/../../backend/functions.php';

dbconn();

$id = (int) ($_GET['id'] ?? 0);
$md5 = $_GET['secret'] ?? '';

if (!$id || !$md5) {
    bark('Error', 'bad id or secret');
}

$row = DB::fetchAssoc('SELECT password, secret, status FROM users WHERE id = '.$id);

if (! $row) {
	bark('Error', 'user not found');
}

if ($row['status'] != 'pending') {
	header('Refresh: 0; url=account-confirm-ok.php?type=confirmed');
	exit();
}

$sec = hash_pad($row['secret']);
if ($md5 != md5($sec)) {
	bark('Error', 'bad secret');
}

$newsec = mksecret();

$aff_rows = DB::executeUpdate('
    UPDATE users SET secret = ?, status = ?
    WHERE id = ' . $id . ' AND secret = ? AND status = ?',
    [$newsec, 'confirmed', $row['secret'], 'pending']
);

if (! $aff_rows) {
	bark('Error', 'update error');
}

logincookie($id, $row['password'], $newsec);
// send welcome pm
if ($WELCOMEPMON) {
    $WELCOMEPMMSG = trim($WELCOMEPMMSG);
    DB::executeUpdate('INSERT INTO messages (poster, sender, receiver, added, msg) VALUES (?, ?, ?, ?, ?)',
        [0, 0, $id, get_date_time(), $WELCOMEPMMSG]
    );
}
header('Refresh: 0; url=account-confirm-ok.php?type=confirm');

