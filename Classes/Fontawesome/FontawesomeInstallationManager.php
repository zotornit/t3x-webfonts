<?php
declare(strict_types=1);

namespace WEBFONTS\Webfonts\Fontawesome;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use WEBFONTS\Webfonts\Font\Font;
use WEBFONTS\Webfonts\Manager\InstallationManager;
use WEBFONTS\Webfonts\Utilities\ZipUtilities;

class FontawesomeInstallationManager extends InstallationManager
{

    /**
     * Gets a singleton instance of this class.
     *
     * @return FontawesomeInstallationManager
     */
    public static function getInstance(): FontawesomeInstallationManager
    {
        return GeneralUtility::makeInstance(__CLASS__);
    }

    public function deleteFontImpl(Font $font)
    {
        if (!$font instanceof FontawesomeFont) {
            return;
        }
        /** @var FontawesomeFont $font */
        GeneralUtility::rmdir($this->FONT_DIR . $font->getProvider() . '/' . $font->getVersion(), true);

        foreach (self::$config as $k => $font) {
            if ($font->getProvider() === 'fontawesome') {
                unset(self::$config[$k]);
                break;
            }
        }
    }

    protected function installFontImpl(Font $font)
    {
        if (!$font instanceof FontawesomeFont) {
            return;
        }
        /** @var FontawesomeFont $font */

        $storageFolder = $this->FONT_DIR . $font->getProvider() . '/' . $font->getVersion();
        GeneralUtility::mkdir_deep($storageFolder);

        // download font
        $zipStoragePath = FontawesomeHelperClient::downloadZIP($font);

        // unzip font
        $unzipped = ZipUtilities::unzip($zipStoragePath, $storageFolder);

        if ($unzipped) {
            self::$config[] = [
                'provider' => 'fontawesome'
            ];
        }
    }

    public function hasInstalled(Font $font): bool
    {
        // Since Fontawesome ZIP contains everything, to specification is required.
        foreach (self::$config as $installedFont) {
            if ($installedFont['provider'] === 'fontawesome') {
                return true;
            }
        }
        return false;
    }
}
