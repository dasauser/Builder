<?php
namespace Dasauser;

use Dasauser\Exceptions\FileNotFoundException;
use Dasauser\Exceptions\UnknownException;

/**
 * Class ShittyConnector
 * @package Dasauser
 */
class ShittyConnector
{
    /**
     * Function getting map
     * @param string $map
     * @return array
     * @throws FileNotFoundException
     */
    protected static function getMap(string $map) : array
    {
        if (file_exists($map)) {
            return require_once($map);
        }
        throw new FileNotFoundException("File $map not found");
    }

    /**
     * Function connecting style and script files
     * @param string $built_dir
     * @param string $map
     * @throws FileNotFoundException
     * @throws UnknownException
     */
    public static function connect(string $built_dir = 'public/build', string $map = 'public/build/map.json')
    {
        if (!file_exists($map)) {
            throw new FileNotFoundException("File $map not found");
        }
        $map = static::getMap($map);
        foreach ($map as $file) {
            $file = "$built_dir/{$file['built_file']}";
            if (file_exists($file)) {
                $ext = substr($file, strripos($file, '.') + 1);
                switch ($ext) {
                    case 'css':
                        echo "<script type='text/javascript' src='$file'></script>";
                        break;
                    case 'js':
                        echo "<link rel='stylesheet' href='$file'>";
                        break;
                    default:
                        throw new UnknownException('Unknown file extension: ' . $ext);
                        break;
                }
            }
        }
    }
}