<?php
declare(strict_types=1);

namespace WEBFONTS\Webfonts\Fontawesome;


use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Downloads f.e.: https://use.fontawesome.com/releases/v5.13.0/fontawesome-free-5.13.0-web.zip
 * Doc: https://fontawesome.com/how-to-use/on-the-web/setup/hosting-font-awesome-yourself
 *
 * Class GoogleWebfontHelperClient
 * @package WEBFONTS\Webfonts\Google
 */
class FontawesomeHelperClient
{

    /**
     *
     *
     * @param FontawesomeFont $font
     * @return string
     */
    public static function downloadZIP(FontawesomeFont $font): string
    {
        $urlParts = [];
        $urlParts[] = 'https://use.fontawesome.com/releases/v';
        $urlParts[] = $font->getVersion();
        $urlParts[] = '/fontawesome-free-';
        $urlParts[] = $font->getVersion();
        $urlParts[] = '-web.zip';

        $content = GeneralUtility::getUrl(implode("", $urlParts)); // TODO error handling

        $zipStorageFolder = Environment::getVarPath() . '/tx_webfonts/download/fontawesome';
        $tempZipFile = $zipStorageFolder . '/fontawesome-free-' . $font->getVersion() . '-web.zip';

        GeneralUtility::mkdir_deep($zipStorageFolder);

        file_put_contents($tempZipFile, $content);

        return $tempZipFile;
    }
}
