<?php

use Doctrine\DBAL;

class DB
{
    private static $_dbal_conn = null;

    private function __construct(){}

    public static function __callStatic($name, $args)
    {
        // return call_user_func_array([$dbal_conn, $name], $args);
        // dump($args);
        // dump('Вход', $args);
        $methods = [
            'executeQuery', 'executeUpdate', 'fetchAssoc', 'fetchArray', 'fetchColumn', 'fetchAll',
            'lastInsertId'
        ];
        if (count($args) > 1 && in_array($name, $methods)) {
            $sql = array_shift($args);

            // dump('Вход', $sql, $args);
            [$sql, $args2] = self::prepareSql($sql, $args[0]);

            // dump('Выход', $sql, $args2, [$sql, $args2]);
            // dump($sql, $args2);
            return call_user_func_array([self::$_dbal_conn, $name], [$sql, $args2 ?? []]);
        } else {
            return call_user_func_array([self::$_dbal_conn, $name], $args);
        }
    }

    public static function conn()
    {
        return self::$_dbal_conn;
    }

    public static function setConn(Doctrine\DBAL\Connection $conn)
    {
        return self::$_dbal_conn = $conn;
    }

    protected static function prepareSql($sql, array $params = [])
    {
        // dump($sql, $params);
        // \PDO::PARAM_INT;
        // \PDO::PARAM_STR;
        // echo $sql;
        // preg_match('~\{(int):(\w+)\}~', $sql, $m);
        // dump($m);

        preg_match_all('~\{(int|str):(\w+)\}~', $sql, $m, PREG_SET_ORDER);
        // dump($m);
        $types = ['int' => 'is_int', 'str' => 'is_string'];

        if (empty($m)) {
            return [$sql, $params];
        }

        // dump($params, array_keys($m), array_keys($params));
        // $params = array_unique($params);

        // dump($params, array_keys($params));
        if (count(array_keys($m)) > count(array_keys($params))) {
            throw new \InvalidArgumentException('Params size ('. count(array_keys($m)) .') < sql args! '
                . ' (' . count(array_keys($params)) . ')');
        }

        // check sql expr
        foreach ($m as $row) {
            // dump('j');
            if (empty($row[1])
                || !array_key_exists($row[1], $types)
                || empty($row[2])
                || !array_key_exists($row[2], $params)
            ) {
                    continue;
            }
            if (!$types[$row[1]]($params[$row[2]])) {
                throw new \InvalidArgumentException('"Bad type in param "' . $row[2] . 
                        '". Your type is ' . gettype($params[$row[2]]) . '. Need type: ' . $row[1] . '"');
            }
            // echo $row[1], ' ', $row[2], '<br>';
            $expr = '{' . $row[1] . ':' . $row[2] . '}';
            // echo '<br>----', $expr, '<br>';
            if ('int' === $row[1]) {
                $sql = str_replace($expr, $params[$row[2]], $sql);
                unset($params[$row[2]]);
            } elseif ('str' === $row[1]) {
                $sql = str_replace($expr, ':' . $row[2], $sql);
            }
            // echo '<hr>', $sql, '<hr>';
        }

        // echo count($params);
        if (!empty($params)) {
            return [$sql, $params];
        } else {
            return [$sql, []];
        }
    }
}

function my_pdo_connect($name, $user, $passwd, $host)
{
    if (is_null(DB::conn())) {
        $config = new DBAL\Configuration();

        $params = [
            'dbname' => $name,
            'user' => $user,
            'password' => $passwd,
            'host' => $host,
            'driver' => 'pdo_mysql',
            // 'charset' => 'utf8',
            'charset' => 'UTF8MB4',
            'port' => 3306,
        ];

        return DB::setConn(DBAL\DriverManager::getConnection($params, $config));

        // $dbal_conn->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, FALSE);

        /*
        try {
            $pdo = new PDO('mysql:host=' . $mysql_host . ';dbname=' . $mysql_db,
                $mysql_user, $mysql_pass, [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']);
            $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            // $db->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
        } catch (PDOException $e) {
            die('Houston, we have a problem. #' . __LINE__);
            # die('<br>Error!: ' . $e->getMessage() . '<br>');
        }
        $params = [
            'pdo' => $pdo,
        ];

        $dbal_conn = DBAL\DriverManager::getConnection($params, $config);
        */
    }
}
