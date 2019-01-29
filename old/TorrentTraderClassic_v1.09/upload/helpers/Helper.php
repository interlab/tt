<?php

class Helper
{
    // todo: cache result
    public static function getStylesheets($html=true)
    {
        global $CURUSER;

        $res = DB::query('SELECT * FROM stylesheets ORDER BY name LIMIT 100');
        if ($html) {
            $styles = '';
            while ($row = $res->fetch()) {
              $styles .= '<option value=' . $row['id'] .
                      ($row['id'] == $CURUSER['stylesheet'] ? ' selected' : '')
                      . '>' . $row['name'] . '</option>';
            }
        } else {
            $styles = [];
            while ($row = $res->fetch()) {
              $styles[$row['id']] = [
                  'id' => $row['id'],
                  'selected' => $row['id'] == $CURUSER['stylesheet'],
                  'name' => $row['name']];
            }
        }

        return $styles;
    }

    // todo: cache result
    public static function getLanguages($html=true)
    {
        global $CURUSER;

        $res = DB::query('SELECT * FROM languages ORDER BY name LIMIT 100');
        if ($html) {
            $langs = '';
            while ($row = $res->fetch()) {
              $langs .= '<option value=' . $row['id'] . ($row['id'] == $CURUSER['language']
                        ? ' selected' : '') . '>' . $row['name'] . '</option>';
            }
        } else {
            $langs = [];
            while ($row = $res->fetch()) {
                $langs[$row['id']] = [
                    'id' => $row['id'],
                    'selected' => $row['id'] == $CURUSER['language'],
                    'name' => $row['name']
                ];
            }
        }

        return $langs;
    }
}
