<?php
declare(strict_types=1);

namespace WEBFONTS\Webfonts\Google;


use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Consumes: https://google-webfonts-helper.herokuapp.com/api/fonts
 * Doc: https://github.com/majodev/google-webfonts-helper
 *
 * Class GoogleWebfontHelperClient
 * @package WEBFONTS\Webfonts\Google
 */
class GoogleWebfontHelperClient
{
    public static function jsonFontList($forceRefresh = false)
    {
        $cacheFile = Environment::getVarPath() . '/tx_webfonts/cache/google_webfonts.json';

        $content = self::getCachedFile($cacheFile, 60 * 60 * 12); // 12h

        $refresh = $content === null || $forceRefresh;

        if ($refresh) {
            $content = GeneralUtility::getUrl('https://google-webfonts-helper.herokuapp.com/api/fonts', 0, null, $report); // TODO error handling
            GeneralUtility::mkdir_deep(dirname($cacheFile));
            file_put_contents($cacheFile, $content);
        }

        $arr = json_decode($content, true);

        usort($arr, function ($a, $b) {
            return $a['family'] <=> $b['family'];
        });

        return $arr;
    }

    public static function jsonFont($id, $forceRefresh = false)
    {
        $cacheFile = Environment::getVarPath() . '/tx_webfonts/cache/google_webfonts/' . $id . '.json';

        $content = self::getCachedFile($cacheFile, 60 * 60 * 12); // 12h

        $refresh = $content === null || $forceRefresh;

        if ($refresh) {
            $content = GeneralUtility::getUrl('https://google-webfonts-helper.herokuapp.com/api/fonts/' . $id, 0, null, $report); // TODO error handling
            GeneralUtility::mkdir_deep(dirname($cacheFile));
            file_put_contents($cacheFile, $content);
        }

        return json_decode($content, true);
    }

    private static function getCachedFile($file, $seconds)
    {
        if (is_file($file)) {
            $lastUpdate = filemtime($file);
            if ($lastUpdate !== FALSE && $lastUpdate >= time() - $seconds) {
                return file_get_contents($file);
            }
        }
        return null;
    }

    /**
     *
     *
     * @param string $font
     * @param array $subsets
     * @param array $variants when empty then ALL variants will be requests
     * @param array $formats when empty then ALL formats will be requests
     * @return string path to downloaded temporary zip file
     */
    public static function downloadZIP($font, $subsets, $variants = [], $formats = []): string
    {
        $urlParts = [];
        $urlParts[] = 'https://google-webfonts-helper.herokuapp.com/api/fonts/';
        $urlParts[] = $font;
        $urlParts[] = '?download=zip';

        $urlParts[] = GoogleWebfontHelperClient::comSepParams('formats', $formats);
        $urlParts[] = GoogleWebfontHelperClient::comSepParams('variants', $variants);
        $urlParts[] = GoogleWebfontHelperClient::comSepParams('subsets', $subsets);

        $content = GeneralUtility::getUrl(implode("", $urlParts), 0, null, $report); // TODO error handling

        $zipStorageFolder = Environment::getVarPath() . '/tx_webfonts/download/' . $font;
        $tempZipFile = $zipStorageFolder . '/' . $font . '.zip';

        GeneralUtility::mkdir_deep($zipStorageFolder);

        file_put_contents($tempZipFile, $content);

        return $tempZipFile;
    }

    private static function comSepParams($key, $values)
    {
        if (is_array($values) && count($values) > 0) {
            $parts[] = '&' . $key . '=';
            foreach ($values as $val) {
                $parts[] = $val . ",";
            }
            return rtrim(implode("", $parts), ",");
        }
        return "";
    }

}
