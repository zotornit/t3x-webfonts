<?php
declare(strict_types=1);

namespace WEBFONTS\Webfonts\Google;

use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WEBFONTS\Webfonts\Utilities\InstallationManager;

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

    public function deleteFontImpl($fontId, $provider)
    {
        GeneralUtility::rmdir($this->FONT_DIR . $provider . '/' . $fontId, true);
    }

    protected function installFontImpl($font, $provider, $variants, $subsets)
    {
        $storageFolder = $this->FONT_DIR . $provider . '/' . $font;
        GeneralUtility::mkdir_deep($storageFolder);

        // download font
        $zipStoragePath = GoogleWebfontHelperClient::downloadZIP($font, $subsets, $variants);

        // unzip font
        $this->unzip($zipStoragePath, $storageFolder);

        self::$config[] = [
            'id' => $font,
            'provider' => $provider,
            'variants' => $variants,
            'subsets' => $subsets,
        ];
    }

    protected function createCssImportFile($fontId, $provider, $variants)
    {
        $storageFolder = $this->FONT_DIR . $provider . '/' . $fontId;

        $files = GeneralUtility::getFilesInDir($storageFolder);

        // download fontdetails
        $fontdetails = GoogleWebfontHelperClient::jsonFont($fontId);

        $rows = [];
        foreach ($variants as $variant) {


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
                    foreach ($variantObj['local'] as $local) {
                        $localArr[] = "local('" . $local . "'),";
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

    private function unzip($zipFile, $targetPath): bool
    {
        $zip = new \ZipArchive();
        $res = $zip->open($zipFile);
        if ($res === TRUE) {
            $zip->extractTo($targetPath);
            $zip->close();
            unlink($zipFile);
            return true;
        }
        return false;
    }

}
