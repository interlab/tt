<?php

use Symfony\Component\Yaml\Yaml as SymfonyYaml;

class Yaml
{
    public static function parse_yaml($file)
    {
        if (file_exists($file)) {
            $yml = file_get_contents($file);
        }
        else {
            trigger_error($file . ' not found!');

            return false;
        }

        try {
            $data = self::yaml2php($yml);
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

    public static function php2yaml($val)
    {
        return SymfonyYaml::dump($val);
    }

    public static function yaml2php($str)
    {
        return SymfonyYaml::parse($str);
        # return Spyc::YAMLLoadString($str);
    }
}

