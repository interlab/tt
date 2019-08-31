<?php

function _write_error_to_file($file, Throwable $exc)
{
    $err_msg = "\n" .
        'Message: ' . $exc->getMessage() . "\n" .
        'Date: ' . date('d M Y H:i:s') . "\n" .
        'Line: ' . $exc->getLine() . "\n" .
        'File: ' . $exc->getFile() . "\n" .
        (function_exists('get_current_url')
            ? 'Url: ' . get_current_url() . "\n"
            : '');
    file_put_contents($file, $err_msg, FILE_APPEND);
}

function unknown_exception_handler(Throwable $exc)
{
    _write_error_to_file(TT_EXCEPTIONS_FILE, $exc);
    error('Error #' . __LINE__);
}

function unknown_error_handler($errno, $errstr, $errfile, $errline)
{
    /*
    if (!(error_reporting() & $errno)) {
        // Этот код ошибки не включен в error_reporting,
        // так что пусть обрабатываются стандартным обработчиком ошибок PHP
        return false;
    }
    */
    $date = date('d M Y H:i:s');

    switch ($errno) {
    case E_USER_ERROR:
        $err_msg = 'Пользовательская ОШИБКА [' . $errno . '] ' . $errstr . "\n" .
            ' Фатальная ошибка! PHP ' . PHP_VERSION . " (" . PHP_OS . ")\n" .
            ' Завершение работы...';
        // exit(1);
        break;

    case E_USER_WARNING:
        $err_msg =  'Пользовательское ПРЕДУПРЕЖДЕНИЕ [' . $errno . '] ' . $errstr;
        break;

    case E_USER_NOTICE:
        $err_msg =  'Пользовательское УВЕДОМЛЕНИЕ [' . $errno . '] ' . $errstr;
        break;

    default:
        $err_msg = 'Message: Неизвестная ошибка: [' . $errno . '] ' . $errstr;
        break;
    }

    $err_msg = "\n" .
        'Message: ' . $err_msg . "\n" .
        'Date: ' . $date . "\n" .
        'Line: ' . $errline . "\n" .
        'File: ' . $errfile . "\n" .
        (function_exists('get_current_url')
            ? 'Url: ' . get_current_url() . "\n"
            : '');

    file_put_contents(TT_ERRORS_FILE, $err_msg, FILE_APPEND);

    if ($errno === E_USER_ERROR) {
        exit(1);
    }

    // Не запускаем внутренний обработчик ошибок PHP
    return true;
}

function db_error($msg, Exception $exc)
{
    _write_error_to_file(TT_DB_ERRORS_FILE, $exc);
    error($msg);
}

function error($msg)
{
    // error_log($e->getMessage(), 1, $webmaster_email);
    benc_resp(['failure reason' => ['type' => 'str', 'value' => $msg]]);
    // echo $msg;
    exit();
}

function tt_exception_handler(Throwable $exc)
{
    global $CURUSER;

    _write_error_to_file(TT_EXCEPTIONS_FILE, $exc);

    // if ($CURUSER['is_admin']) {
    if (TT_DEBUG_ON) {
        $err_msg = "\n" .
            '<h4>Message: </h4>' . $exc->getMessage() .
            '<h4>Date: </h4>' . date('d M Y H:i:s') .
            '<h4>Line: </h4>' . $exc->getLine() .
            '<h4>File: </h4>' . $exc->getFile() .
            '<h4>Trace: </h4>' . nl2br($exc->getTraceAsString()) .
            '<h4>Url: </h4>' . get_current_url();

        echo $err_msg;
    }
    // }
    die('Exception detected.');
}

function tt_error_handler($errno, $errstr, $errfile, $errline)
{
    return unknown_error_handler($errno, $errstr, $errfile, $errline);
}
