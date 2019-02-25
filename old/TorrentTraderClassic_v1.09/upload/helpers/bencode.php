<?php

# Bencode Format
function benc($obj)
{
    if (!is_array($obj) || !isset($obj['type'], $obj['value']))
        return;

    $val = $obj['value'];
    switch ($obj['type']) {
        case 'str':   return strlen($val) . ':' . $val;
        case 'int':   return 'i' . $val . 'e';
        case 'list':  return _benc_list($val);
        case 'dict':  return _benc_dict($val);
        default:      return;
    }
}

function benc_str($s)
{
    return strlen($s) . ':' . $s;
}

function _benc_list($a)
{
    $s = 'l';
    foreach ($a as $e) {
        $s .= benc($e);
    }
    $s .= 'e';

    return $s;
}

function _benc_dict($d)
{
    $s = 'd';
    $keys = array_keys($d);
    sort($keys);
    foreach ($keys as $k) {
        $v = $d[$k];
        $s .= benc_str($k);
        $s .= benc($v);
    }
    $s .= 'e';

    return $s;
}

# Финальный вывод клиенту ошибки или ответа
function benc_resp($d)
{
    benc_resp_raw(benc(['type' => 'dict', 'value' => $d]));
}

function benc_resp_raw($x)
{
    header('Content-Type: text/plain');
    header('Pragma: no-cache');

    if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])
        && $_SERVER['HTTP_ACCEPT_ENCODING'] === 'gzip'
    ) {
        header('Content-Encoding: gzip');
        echo gzencode( $x, 9, FORCE_GZIP );
    } else {
        echo $x;
    }
}
