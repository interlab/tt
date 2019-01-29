<?php

require_once __DIR__ . '/../backend/functions.php';

dbconn();
loggedinorreturn();

$set = [];
$style = (int) ($_POST["stylesheet"] ?? 0);
$lang = (int) ($_POST["language"] ?? 0);

$styles = Helper::getStylesheets(false);
$langs = Helper::getLanguages(false);

if ($style > 0 && in_array($style, array_keys($styles))) {
    $set['stylesheet'] = $style;
}

if ($lang > 0 && in_array($lang, array_keys($langs))) {
    $set['language'] = $lang;
}

if ($set) {
    DB::update('users', $set, ['id' => $CURUSER['id']]);
}

header('Location: index.php');
