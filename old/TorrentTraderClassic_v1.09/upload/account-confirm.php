<?php

// Confirm account Via email and send PM

require_once("backend/functions.php");

$id = (int) ($_GET["id"] ?? 0);
$md5 = $_GET["secret"];

if (! $id) {
    httperr();
}

dbconn();

$row = DB::fetchAssoc('SELECT password, secret, status FROM users WHERE id = '.$id);

if (! $row) {
	httperr();
}

if ($row["status"] != "pending") {
	header("Refresh: 0; url=account-confirm-ok.php?type=confirmed");
	exit();
}

$sec = hash_pad($row["secret"]);
if ($md5 != md5($sec)) {
	httperr();
}

$newsec = mksecret();

$aff_rows = DB::executeUpdate('
    UPDATE users SET secret = ?, status = ?
    WHERE id = ' . $id . ' AND secret = ? AND status = ?',
    [$newsec, 'confirmed', $row["secret"], 'pending']
);

if (! $aff_rows) {
	httperr();
}

logincookie($id, $row["password"], $newsec);
// send welcome pm
if ($WELCOMEPMON) {
    $WELCOMEPMMSG = trim($WELCOMEPMMSG);
    DB::executeUpdate('INSERT INTO messages (poster, sender, receiver, added, msg) VALUES (?, ?, ?, ?, ?)',
        [0, 0, $id, get_date_time(), $WELCOMEPMMSG]
    );
}
header("Refresh: 0; url=account-confirm-ok.php?type=confirm");

