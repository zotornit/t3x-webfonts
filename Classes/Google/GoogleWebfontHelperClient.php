<?php
declare(strict_types=1);

namespace WEBFONTS\Webfonts\Google;


use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Consumes: https://gwfh.mranftl.com/api/fonts
 * Doc: https://github.com/majodev/google-webfonts-helper
 *
 * Class GoogleWebfontHelperClient
 * @package WEBFONTS\Webfonts\Google
 */
class GoogleWebfontHelperClient
{

    private const WEBFONTS_API_URL = 'https://gwfh.mranftl.com/api/fonts'; // no trailing /

    public static function jsonFontList($forceRefresh = false)
    {
        $cacheFile = Environment::getVarPath() . '/tx_webfonts/cache/google_webfonts.json';

        $content = self::getCachedFile($cacheFile, 60 * 60 * 12); // 12h
        $arr = null;
        if ($content !== null) {
            $arr = json_decode($content, true);
        }

        $refresh = $content === null || $forceRefresh || !is_array($arr);

        if ($refresh) {
            $content = GeneralUtility::getUrl(GoogleWebfontHelperClient::WEBFONTS_API_URL); // TODO error handling
            GeneralUtility::mkdir_deep(dirname($cacheFile));
            file_put_contents($cacheFile, $content);
            $arr = json_decode($content, true);
        }

        usort($arr, function ($a, $b) {
            return $a['family'] <=> $b['family'];
        });

        return $arr;
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

    public static function jsonFont(string $id, $forceRefresh = false)
    {
        $cacheFile = Environment::getVarPath() . '/tx_webfonts/cache/google_webfonts/' . $id . '.json';

        $content = self::getCachedFile($cacheFile, 60 * 60 * 12); // 12h

        $refresh = $content === null || $forceRefresh;

        if ($refresh) {
            $content = GeneralUtility::getUrl(GoogleWebfontHelperClient::WEBFONTS_API_URL . '/' . $id); // TODO error handling
            GeneralUtility::mkdir_deep(dirname($cacheFile));
            file_put_contents($cacheFile, $content);
        }

        return json_decode($content, true);
    }

    /**
     *
     *
     * @param GoogleFont $font
     * @param array $formats
     * @return string path to downloaded temporary zip file
     */
    public static function downloadZIP(GoogleFont $font, $formats = []): string
    {
        $urlParts = [];
        $urlParts[] = GoogleWebfontHelperClient::WEBFONTS_API_URL . '/';
        $urlParts[] = $font->getId();
        $urlParts[] = '?download=zip';

        $urlParts[] = GoogleWebfontHelperClient::comSepParams('formats', $formats);
        $urlParts[] = GoogleWebfontHelperClient::comSepParams('variants', $font->getVariants());
        $urlParts[] = GoogleWebfontHelperClient::comSepParams('subsets', $font->getCharsets());

        $content = GeneralUtility::getUrl(implode("", $urlParts)); // TODO error handling

        $zipStorageFolder = Environment::getVarPath() . '/tx_webfonts/download/' . $font->getId();
        $tempZipFile = $zipStorageFolder . '/' . $font->getId() . '.zip';

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
