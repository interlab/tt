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

    public static function sortMod($sort, $type)
    {
        if (! empty($sort)) {
            $column = '';
            $ascdesc = '';
            $sort = (int) $sort;
            $type = strtolower($type) === 'asc' ? 'asc' : 'desc';

            switch ($sort) {
                case 1: $column = 'name'; break;
                case 2: $column = 'nfo'; break;
                case 3: $column = 'comments'; break;
                case 4: $column = 'size'; break;
                case 5: $column = 'times_completed'; break;
                case 6: $column = 'seeders'; break;
                case 7: $column = 'leechers'; break;
                case 8: $column = 'category'; break;
                default: $column = 'id'; break;
            }

            switch ($type) {
                case 'asc': $ascdesc = 'ASC'; break;
                case 'desc': $ascdesc = 'DESC'; break;
                default: $ascdesc = 'DESC'; break;
            }

            $orderby = 'ORDER BY torrents.' . $column . ' ' . $ascdesc;
            $pagerlink = 'sort=' . $sort . '&type=' . $type . '&';
        } else {
            $column = 'id';
            $ascdesc = 'DESC';
            $pagerlink = '';
            $orderby = "ORDER BY torrents.id DESC";
        }

        return [
            'orderby' => $orderby, 'pagerlink' => $pagerlink,
            'column' => $column, 'by' => $ascdesc,
        ];
    }
}
