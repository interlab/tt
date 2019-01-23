<?php

use Symfony\Component\Yaml\Yaml;

function st_parse_yaml($file)
{
    if (file_exists($file)) {
        $yml = file_get_contents($file);
    }
    else {
        trigger_error($file . ' not found!');

        return false;
    }

    try {
        $data = st_yaml2php($yml);
    } catch (\Exception $e) {
        die($e->getMessage());
    }

    if (null === $data) {
        trigger_error('Bad yaml sintax');

        return false;
    }

    // todo : cache data

    // $txt = array_merge($txt, $data);

    // return array_merge($txt, $data);

    return $data;
}

function st_php2yaml($val)
{
    return Yaml::dump($val);
}

function st_yaml2php($str)
{
    return Yaml::parse($str);
    # return Spyc::YAMLLoadString($str);
}
