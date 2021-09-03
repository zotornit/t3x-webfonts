<?php
declare(strict_types=1);

namespace WEBFONTS\Webfonts\Google;

use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WEBFONTS\Webfonts\Font\Font;
use WEBFONTS\Webfonts\Manager\InstallationManager;
use WEBFONTS\Webfonts\Utilities\ZipUtilities;

class GoogleFontInstallationManager extends InstallationManager
{

    /**
     * Gets a singleton instance of this class.
     *
     * @return GoogleFontInstallationManager
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(__CLASS__);
    }

    public function deleteFontImpl($fontToDelete)
    {
        assert($fontToDelete instanceof GoogleFont);

        GeneralUtility::rmdir($this->FONT_DIR . $fontToDelete->getProvider() . '/' . $fontToDelete->getId(), true);

        foreach (self::$config as $k => $font) {
            if ($font['id'] === $fontToDelete->getId() && $font['provider'] === $fontToDelete->getProvider()) {
                unset(self::$config[$k]);
                break;
            }
        }
    }

    protected function installFontImpl(Font $font)
    {
        assert($font instanceof GoogleFont);

        $storageFolder = $this->FONT_DIR . $font->getProvider() . '/' . $font->getId();
        GeneralUtility::mkdir_deep($storageFolder);

        // download font
        $zipStoragePath = GoogleWebfontHelperClient::downloadZIP($font);

        // unzip font
        $unzipped = ZipUtilities::unzip($zipStoragePath, $storageFolder);

        if ($unzipped) {
            self::$config[] = [
                'id' => $font->getId(),
                'provider' => $font->getProvider(),
                'variants' => $font->getVariants(),
                'subsets' => $font->getCharsets(),
            ];
        }

        $this->createCssImportFile($font);
    }

    private function createCssImportFile(Font $font)
    {
        assert($font instanceof GoogleFont);

        $storageFolder = $this->FONT_DIR . $font->getProvider() . '/' . $font->getId();
        $files = GeneralUtility::getFilesInDir($storageFolder);
        // download font details
        $fontdetails = GoogleWebfontHelperClient::jsonFont($font->getId());

        $rows = [];
        foreach ($font->getVariants() as $variant) {


            foreach ($fontdetails['variants'] as $detail) {
                if ($detail['id'] === $variant) {
                    $variantObj = $detail;

                    $rows[] = '@font-face {';
                    $rows[] = "\tfont-family: " . $variantObj['fontFamily'] . ";";
                    $rows[] = "\tfont-style: " . $variantObj['fontStyle'] . ";";
                    $rows[] = "\tfont-weight: " . $variantObj['fontWeight'] . ";";

                    // src: url('../fonts/open-sans-v17-latin_cyrillic-ext_vietnamese_greek-regular.eot'); /* IE9 Compat Modes */
                    $rows[] = "\tsrc: url('./" . $this->getFilenameForFontId($variant, "eot", $files) . "'); /* IE9 Compat Modes */";

                    // src: local('Open Sans Regular'), local('OpenSans-Regular'),
                    $localArr = [];
                    if (isset($variantObj['local']) && is_array($variantObj['local'])) {
                        foreach ($variantObj['local'] ?? [] as $local) {
                            $localArr[] = "local('" . $local . "'),";
                        }
                    } else {
                        $localArr[] = "local(''),";
                    }
                    $rows[] = "\tsrc: " . implode(" ", $localArr);

                    // url('../fonts/open-sans-v17-latin_cyrillic-ext_vietnamese_greek-regular.eot?#iefix') format('embedded-opentype'), /* IE6-IE8 */
                    $rows[] = "\t\turl('./" . $this->getFilenameForFontId($variant, "eot", $files) . "?#iefix') format('embedded-opentype'), /* IE6-IE8 */";

                    // url('../fonts/open-sans-v17-latin_cyrillic-ext_vietnamese_greek-regular.woff2') format('woff2'), /* Super Modern Browsers */
                    $rows[] = "\t\turl('./" . $this->getFilenameForFontId($variant, "woff2", $files) . "') format('woff2'), /* Super Modern Browsers */";

                    // url('../fonts/open-sans-v17-latin_cyrillic-ext_vietnamese_greek-regular.woff') format('woff'), /* Modern Browsers */
                    $rows[] = "\t\turl('./" . $this->getFilenameForFontId($variant, "woff", $files) . "') format('woff'), /* Modern Browsers */";

                    // url('../fonts/open-sans-v17-latin_cyrillic-ext_vietnamese_greek-regular.ttf') format('truetype'), /* Safari, Android, iOS */
                    $rows[] = "\t\turl('./" . $this->getFilenameForFontId($variant, "ttf", $files) . "') format('truetype'), /* Safari, Android, iOS */";

                    // url('../fonts/open-sans-v17-latin_cyrillic-ext_vietnamese_greek-regular.svg#OpenSans') format('svg'); /* Legacy iOS */
                    preg_match('/^.*(#.+)$/', $variantObj['svg'], $m);
                    $rows[] = "\t\turl('./" . $this->getFilenameForFontId($variant, "svg", $files) . $m[1] . "') format('svg'); /* Legacy iOS */"; // <--- semicolon at the end!!


                    $rows[] = '}';


                }
            }
        }

        GeneralUtility::mkdir_deep($storageFolder);
        file_put_contents($storageFolder . '/import.css', implode("\n", $rows));

    }

    private function getFilenameForFontId($variantId, $fileExtension, $fileNames)
    {
        foreach ($fileNames as $file) {
            if (strpos($file, '-' . $variantId . '.' . $fileExtension) !== false) {
                return $file;
            }
        }
        throw new Exception("No font file found. Should never happen, when files were downloaded properly. ");
    }

    public function hasInstalled(Font $font): bool
    {
        assert($font instanceof GoogleFont);

        foreach (self::$config as $installedFont) {
            if ($installedFont['provider'] === 'google_webfonts' && $installedFont['id'] === $font->getId()) {
                foreach ($font->getVariants() as $variant) {
                    if (!in_array($variant, $installedFont['variants'])) {
                        return false;
                    }
                }
                foreach ($font->getCharsets() as $charset) {
                    if (!in_array($charset, $installedFont['subsets'])) {
                        return false;
                    }
                }
                return true;
            }
        }
        return false;
    }
}
