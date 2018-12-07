<?php
/**
 * Created by Jiří Zapletal.
 * Date: 25/12/2016
 * Time: 17:42
 */

namespace NetteImages;


use Nette\DI\Container;
use Nette\Utils\FileSystem;
use Nette\Utils\Image;

class NetteImages
{
    const
        ASSETS_DIR = '/assets/dynamic/',
        TEMP_DIR = '/www/temp/dynamic/',
        WWW_TEMP = 'temp/dynamic/';

    const ALLOWED_DIRECTIVES = [
        'fit' => Image::FIT,
        'fill' => Image::FILL,
        'exact' => Image::EXACT,
        'shrink_only' => Image::SHRINK_ONLY,
        'stretch' => Image::STRETCH
    ];

    private static $root;

    public static function setup(Container $container)
    {
        self::$root = realpath($container->parameters['appDir'] . '/../');
    }

    public static function createThumb($name, $size, $directives)
    {
        list($width, $height) = explode('x', $size);

        $width = $width == 'NULL' ? NULL : $width;
        $height = $height == 'NULL' ? NULL : $height;

        $directives = explode('|', $directives);
        $directiveCodes = NULL;

        foreach ($directives as $value)
        {
            if (!key_exists($value, self::ALLOWED_DIRECTIVES)) {
                throw new \NetteImageException("Directive $value does not allowed");
            }

            $directiveCodes |= self::ALLOWED_DIRECTIVES[$value];
        }

        $assetsOriginalDirectory = self::$root . self::ASSETS_DIR . $name;
        $assetsOriginalSplFileInfo = new \SplFileInfo($assetsOriginalDirectory);

        $extension = '.'.$assetsOriginalSplFileInfo->getExtension();

        $size = (!$width && !$height) ? '' : '-'.$size;
        $size = str_replace('NULL', '', $size);

        $thumbName = $assetsOriginalSplFileInfo->getBasename($extension).$size.$extension;
        $wwwThumbSplInfo = new \SplFileInfo($name);
        $wwwThumbPath =  $wwwThumbSplInfo->getPath().'/'.$thumbName;

        if (!file_exists($assetsOriginalDirectory)) {
            return 'missing-asset-image.jpg';
        }

        $wwwAbsoluteThumbPath = self::$root.self::TEMP_DIR;

        if (!file_exists($wwwAbsoluteThumbPath.$wwwThumbPath))
        {
            if (!file_exists($wwwAbsoluteThumbPath.$wwwThumbSplInfo->getPath())) {
                FileSystem::createDir($wwwAbsoluteThumbPath.$wwwThumbSplInfo->getPath(), 0777);
            }

            $image = Image::fromFile($assetsOriginalDirectory);

            if ($width || $height) {
                $image->resize($width, $height, $directiveCodes);
            }

            $image->save($wwwAbsoluteThumbPath.$wwwThumbPath, 75);
        }

        return self::WWW_TEMP.$wwwThumbPath;
    }
}