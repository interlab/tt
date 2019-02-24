<?php

// if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    // die('Bad request');
    // die(json_encode(['fail' => 'Bad request'], JSON_UNESCAPED_UNICODE));
// }

require_once __DIR__ . '/../backend/functions.php';

dbconn(false);

if (isset($_GET['filelist'], $_GET['id'])) {
    echo tt_file_list($_GET['id']);
}


function tt_file_list(int $id)
{
    $data = Cache::rise('tt-filelist-'.$id, function() use ($id) {
        $d = [];
        $res = DB::query('SELECT * FROM files WHERE torrent = ' . $id . ' ORDER BY filename ASC');
        $i = 0;
        $allsize = 0;
        while ($row = $res->fetch()) {
            $d[$i++] = [$i, $row['filename'], mksize($row['size'])];
            $allsize += $row['size'];
        }
        $ret = [$d, mksize($allsize), $allsize];

        return $ret;
    });
    // dump($data);

    // sleep(5);

    return json_encode($data, JSON_UNESCAPED_UNICODE);
}

