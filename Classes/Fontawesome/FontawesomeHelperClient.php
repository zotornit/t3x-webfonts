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
     * @param string $version
     */
    public static function downloadZIP($version): string
    {
        $urlParts = [];
        $urlParts[] = 'https://use.fontawesome.com/releases/v';
        $urlParts[] = $version;
        $urlParts[] = '/fontawesome-free-';
        $urlParts[] = $version;
        $urlParts[] = '-web.zip';

        $content = GeneralUtility::getUrl(implode("", $urlParts), 0, null, $report); // TODO error handling

        $zipStorageFolder = Environment::getVarPath() . '/tx_webfonts/download/fontawesome';
        $tempZipFile = $zipStorageFolder . '/fontawesome-free-' . $version . '-web.zip';

        GeneralUtility::mkdir_deep($zipStorageFolder);

        file_put_contents($tempZipFile, $content);

        return $tempZipFile;
    }
}
