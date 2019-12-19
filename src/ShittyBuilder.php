<?php
namespace Dasauser;

class ShittyBuilder
{
    public static $assets_dir;
    public static $built_dir;
    public static $built_map;

    public static function check(string $assets_dir, array $build_map_dir = [], string $built_dir = 'public/build') : void
    {
        self::$assets_dir = $assets_dir;
        self::$built_dir = $built_dir;
        if (!file_exists(self::$built_dir)) {
            mkdir(self::$built_dir, 0755, true);
        }
        $build_map_array = $build_map_dir === [] ? self::getFilesMap() : $build_map_dir;
        $built_map = self::getBuildedMap();
        $updated = false;
        foreach ($build_map_array as $item) {
            $file_name = self::$assets_dir . "/$item";
            if (file_exists($file_name)) {
                $md_hash = md5_file($file_name);
                if ($md_hash !== $built_map[$item]['hash']) {
                    $built_map[$item]['hash'] = $md_hash;
                    if (isset($item['built_file'])) {
                        self::deleteOld($item['built_file']);
                    }
                    $updated = true;
                } else {
                    if (empty($built_map[$item]['built_file'])) {
                        $updated = true;
                    } else {
                        $built_file = self::$built_dir . '/' . $built_map[$item]['built_file'];
                        if (!file_exists($built_file)) {
                            self::copy($file_name, $built_map[$item]['built_file']);
                        }
                    }
                }
                if ($updated) {
                    if (($built_map[$item]['built_file'] = self::copy($file_name)) !== '') {
                        self::updateContent(self::$built_dir . '/' . $built_map[$item]['built_file']);
                    } else {
                        $updated = false;
                    }
                }
            } else {
                throw new \Exception('File ' . $file_name . ' not found', 400);
            }
        }
        if ($updated) {
            self::write($built_map);
        }
    }

    public static function getFilesMap() : array
    {
        $file_map = self::$assets_dir . '/map.php';
        if (file_exists($file_map)) {
            return require_once($file_map);
        }
        throw new \Exception('Set custom map file or create ' . self::$assets_dir . '/map.php', 400);
    }

    public static function getBuildedMap() : array
    {
        self::$built_map = self::$built_dir . '/map.json';
        if (file_exists(self::$built_map)) {
            return json_decode(file_get_contents(self::$built_map), true);
        }
        return [];
    }

    public static function copy(string $file_name, string $new_file = '') : string
    {
        $new_file = $new_file === '' ? substr(md5(uniqid()), -15) . substr($file_name, strripos($file_name, '.')) : $new_file;
        $new_file_path = self::$built_dir . '/' . $new_file;
        if (!copy($file_name, $new_file_path)) {
            return '';
        }
        do {
            $file_exist = file_exists($new_file_path);
        } while (!$file_exist);
        return $new_file;
    }

    public static function updateContent(string $file) : void
    {
        $file_content = file_get_contents($file);
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://minify.minifier.org/',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => 'source=' . $file_content . '&type=' . substr($file, strripos($file, '.') + 1),
            CURLOPT_RETURNTRANSFER => true
        ]);
        $result = curl_exec($curl);
        file_put_contents($file, json_decode($result, true)['minified']);
    }

    public static function write(array $data) : void
    {
        file_put_contents(self::$built_map, json_encode($data));
    }

    public static function deleteOld(string $old_file_name) : void
    {
        $old_file_name = self::$built_dir . "/$old_file_name";
        if (file_exists($old_file_name)) {
            unlink($old_file_name);
        }
    }
}