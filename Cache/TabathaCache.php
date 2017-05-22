<?php


namespace TabathaCache\Cache;


use Bat\FileSystemTool;

/**
 * This implementation thinks a developer only (not a web user)
 * will be using the cache system.
 *
 * If your cache ids or delete ids are named after an external user input,
 * you would have security leaks (basically, you want to remove .. in your path
 * if you want to secure this class, but we didn't do it, as I just said).
 *
 *
 *
 */
class TabathaCache implements TabathaCacheInterface
{
    private $dir;
    private $_wildcard;
    private $_privateDir;


    public function __construct()
    {
        $this->dir = '/tmp/tabatha';

        // shouldn't touch the one below
        $this->_wildcard = '__wildcard__';
        $this->_privateDir = '_private_xxx_';
    }

    public static function create()
    {
        return new static();
    }

    public function setDir($dir)
    {
        $this->dir = $dir;
        return $this;
    }


    public function get($cacheId, callable $generateCallback, $deleteIds)
    {
        if (!is_array($deleteIds)) {
            $deleteIds = [$deleteIds];
        }
        $path = $this->dir . "/" . $cacheId . ".txt";
        if (file_exists($path)) {
//            a("from cache");
            $c = file_get_contents($path);
            return unserialize($c);
        } else {
//            a("new content");
            $c = call_user_func($generateCallback);
            FileSystemTool::mkfile($path, serialize($c));

            $this->setListeners($deleteIds, $cacheId);
            FileSystemTool::mkfile($path, serialize($c));


            return $c;
        }
    }

    public function clean($deleteIds)
    {
        if (!is_array($deleteIds)) {
            $deleteIds = [$deleteIds];
        }
        $dir = $this->dir . "/" . $this->_privateDir;
        $entries = [];
        foreach ($deleteIds as $deleteId) {
            $p = explode('.', $deleteId);
            while (null !== array_pop($p)) {
                if (count($p) > 0) {
                    $entries[] = implode('.', $p) . '.' . $this->_wildcard;

                } else {
                    $entries[] = $this->_wildcard;
                    break;
                }
            }
        }
        foreach ($entries as $entry) {
            $f = $dir . "/" . $entry;
            $f .= '.txt';
            $this->cleanEntry($f);
        }
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function setListeners(array $deleteIds, $cacheId)
    {
        $dir = $this->dir . "/" . $this->_privateDir;
        foreach ($deleteIds as $deleteId) {
            $deleteId = str_replace('*', $this->_wildcard, $deleteId);
            $deleteId .= '.txt';
            $f = $dir . "/" . $deleteId;
            $listeners = [];
            if (file_exists($f)) {
                $listeners = unserialize(file_get_contents($f));
            }
            $listeners[] = $cacheId;
            $c = serialize($listeners);
            FileSystemTool::mkfile($f, $c);
        }
    }


    private function cleanEntry($entry)
    {
        if (file_exists($entry)) {
            $listeners = unserialize(file_get_contents($entry));
            foreach ($listeners as $cacheId) {
                $path = $this->dir . "/" . $cacheId . ".txt";
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }
    }
}