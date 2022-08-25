<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 19.08.2022
 * Time: 10:21
 */

namespace common\helpers;

use Yii;
use yii\imagine\Image;

class ImageHelper
{
    /**
     * @param $id
     * @param null $class
     * @param bool $checkExists
     *
     * @return string
     */
    public static function show_svg($id, $class = null, $checkExists = false)
    {
        $fileUri = '/images/svg/map.svg';
        $fileName = Yii::getAlias('@frontend/web' . $fileUri);

        if ($checkExists) {
            $doc = new \DOMDocument();
            $doc->load($fileName);

            $xpath = new \DOMXPath($doc);
            $rootNamespace = $doc->lookupNamespaceUri($doc->namespaceURI);
            $xpath->registerNamespace('svg', $rootNamespace);

            $nodes = $xpath->query("//svg:symbol[@id='" . $id . "']");

            if (!$nodes || $nodes->length == 0) {
                return '';
            }
        }
        ob_start();
        ?>
        <svg class="<?= $class ?? $id ?>" role="img">
            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                 xlink:href="<?= $fileUri ?>?v=<?= filemtime($fileName) ?>#<?= $id ?>"></use>
        </svg>
        <?php
        return ob_get_clean();
    }

    /**
     * Создает превью нужного размера.
     *
     * @param string $filename
     * @param string $imagePath
     * @param int $w
     * @param int $h
     * @param int $quality
     *
     * @return string
     */
    protected static function resizedImage(string $filename, string $imagePath, int $w, int $h, int $quality) : string
    {
        $substr = substr($filename, 0, 2);
        $resizePath = "/upload/resize/{$substr}/{$w}_{$h}_{$quality}";
        $resizedUrl = "{$resizePath}/{$filename}";
        $resizedPath = Yii::getAlias("@resize/{$substr}/{$w}_{$h}_{$quality}");
        $resizedFile = "{$resizedPath}/{$filename}";

        if (!file_exists($imagePath)) {
            if (YII_ENV_PROD) {
                return false;
            } else {
                return Yii::$app->params['prodUrl'] . $resizedUrl;
            }
        }

        FileHelper::createDirectory($resizedPath);
        if (!file_exists($resizedFile)) {
            self::resizeImage($imagePath, $resizedFile, $w, $h, $quality);
        }
        return $resizedUrl;
    }

    /**
     * Получить фото (превью) нужного размера.
     *
     * @param string $file Путь от frontend/web/images/
     * @param int $w Ширина
     * @param int $h Высота
     * @param int $quality Качество
     *
     * @return string
     */
    public static function getResizedImageStatic(string $file, int $w, int $h, int $quality = 100) : string
    {
        if (!$file) {
            return false;
        }

        $file = '/images/' . $file;

        $filename = basename($file);
        $imagePath = Yii::getAlias("@frontend/web{$file}");

        return self::resizedImage($filename, $imagePath, $w, $h, $quality) . '?v=' . filemtime($imagePath);
    }

    /**
     * Создает фото (превью) нужного размера.
     *
     * @param string $imagePath
     * @param string $resizedFile
     * @param int $w
     * @param int $h
     * @param int $quality
     */
    public static function resizeImage($imagePath, $resizedFile, $w, $h, $quality)
    {
        Image::thumbnail($imagePath, $w, $h)->save($resizedFile, ['quality' => $quality]);
    }
}