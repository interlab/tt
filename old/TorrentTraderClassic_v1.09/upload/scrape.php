<?php

// https://wiki.theory.org/BitTorrentSpecification#Tracker_.27scrape.27_Convention

require_once 'backend/config.php';

ignore_user_abort(1);

const TT_EXCEPTIONS_FILE = __DIR__ . '/errors/unknown-exceptions.txt';
const TT_ERRORS_FILE = __DIR__ . '/errors/unknown-errors.txt';
const TT_DB_ERRORS_FILE = __DIR__ . '/errors/db-errors.txt';
require_once __DIR__ . '/helpers/bencode.php';
require_once __DIR__ . '/helpers/errors-helper.php';
set_exception_handler('unknown_exception_handler');
set_error_handler('unknown_error_handler');

function dbconn()
{
    global $mysql_host, $mysql_user, $mysql_pass, $mysql_db;

    $db_type = 'mysql';
    $db_server = $mysql_host;
    $db_name = $mysql_db;
    $db_user = $mysql_user;
    $db_passwd = $mysql_pass;

    try {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            PDO::ATTR_EMULATE_PREPARES => 0,
        ];
        $db = new PDO($db_type . ':host=' . $db_server . ';dbname=' . $db_name,
            $db_user, $db_passwd, $options
        );
        unset($options);
    } catch (PDOException $e) {
        db_error('Houston, we have a problem. #' . __LINE__, $e);
    }

    return $db;
}

function db_run($db, $sql, array $params=[])
{
    try {
        $q = $db->prepare($sql);
        $q->execute($params);
    } catch (PDOException $e) {
        db_error('DB Error! #' . __LINE__, $e);
    }

    return $q;
}

// check if client can handle gzip
if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])
    && stristr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')
    && extension_loaded('zlib')
    && ini_get('zlib.output_compression') == 0
    && ini_get('output_handler') != 'ob_gzhandler'
) {
    ob_start('ob_gzhandler');
} else {
    ob_start();
}
// end gzip controll

$db = dbconn();

$_SERVER['QUERY_STRING'] = $_SERVER['QUERY_STRING'] ?? '';
$infohash = [];
foreach (explode('&', $_SERVER['QUERY_STRING']) as $item) {
    if (preg_match('~^info_hash=(.+)$~', $item, $m)) {
        $hash = urldecode($m[1]);
        if (strlen($hash) !== 20)
            continue;
        $infohash[] = $hash;
    }
}

if (!count($infohash)) {
    error('Invalid infohash.');
}

$query = db_run($db, '
    SELECT info_hash, seeders, leechers, times_completed, filename
    FROM ' . $db_prefix_tor . 'torrents
    WHERE info_hash IN ('.join(',', array_fill(0, count($infohash), '?')).')',
    $info_hash
);

echo 'd5:filesd';

while ($row = $query->fetch()) {
	$hash = hex2bin($row[0]);
	echo '20:'.$hash.'d';
	echo '8:completei'.$row[1].'e';
	echo '10:downloadedi'.$row[3].'e';
	echo '10:incompletei'.$row[2].'e';
	echo '4:name'.strlen($row[4]).':'.$row[4];
    echo 'e';
}

echo 'ee';
header('Content-Type: text/plain');
ob_end_flush();

die();
