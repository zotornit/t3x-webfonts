<?php
declare(strict_types=1);

namespace WEBFONTS\Webfonts\Fontawesome;

use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WEBFONTS\Webfonts\Utilities\InstallationManager;
use WEBFONTS\Webfonts\Utilities\ZipUtilities;

class FontawesomeInstallationManager extends InstallationManager
{

    /**
     * Gets a singleton instance of this class.
     *
     * @return FontawesomeInstallationManager
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(__CLASS__);
    }

    public function deleteFontImpl($fontId, $provider)
    {
//        GeneralUtility::rmdir($this->FONT_DIR . $provider . '/' . $fontId, true);
    }

    protected function installFontImpl($versionAsId, $provider, $variants, $subsets)
    {
        $storageFolder = $this->FONT_DIR . $provider . '/' . $versionAsId;
        GeneralUtility::mkdir_deep($storageFolder);

        // download font
        $zipStoragePath = FontawesomeHelperClient::downloadZIP($versionAsId);

        // unzip font
        $unzipped = ZipUtilities::unzip($zipStoragePath, $storageFolder);

        if ($unzipped) {
            self::$config[] = [
                'id' => $versionAsId,
                'provider' => $provider,
                'variants' => $variants,
                'subsets' => $subsets,
            ];
        } else {
            // TODO handle error
        }
    }

    protected function createCssImportFile($fontId, $provider, $variants)
    {
        // Not necessary for fontawesome
    }
}