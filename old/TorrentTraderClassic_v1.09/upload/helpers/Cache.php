<?php

use \Doctrine\Common\Cache\FilesystemCache;

class Cache
{
    public static $cacheDir = null;

    public static function clean_dir($dir)
    {
        $di = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
        $ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ( $ri as $file ) {
            // echo $file, '<br>';
            if ($file->getFilename() === '.htaccess') { continue; }
            $file->isDir() ? rmdir($file) : unlink($file);
        }
    }

    public static function cacheDriver()
    {
        $cacheDriver = new FilesystemCache(self::$cacheDir . '/', '.foo');

        return $cacheDriver;
    }

    // http://doctrine-orm.readthedocs.io/projects/doctrine-orm/en/latest/reference/caching.html#all
    public static function clean_cache($cacheKey)
    {
        // file_put_contents(__DIR__ . '/test.txt', "\n" . $cacheKey, FILE_APPEND); // test
        return (null === $cacheKey ? self::cacheDriver()->deleteAll() : self::cacheDriver()->delete($cacheKey));
    }

    public static function rise($id, $func, $timecache=86400)
    {
        // echo self::$cacheDir;
        $cache = self::cacheDriver();
        if ( $cache->contains($id) ) {
             $cdata = $cache->fetch($id);
        } else {
            try {
                $cdata = is_callable($func) ? $func() : $func;
            } catch (\Exception $e) {
                // echo '<h4>Ошибка: ', $e, '<h4>';
                $cdata = [['error' => (string) $e]];
            }
            $cache->save($id, $cdata, $timecache);
        }

        return $cdata;
    }
}

