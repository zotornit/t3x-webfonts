<?php
declare(strict_types=1);

namespace WEBFONTS\Webfonts\Manager;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WEBFONTS\Webfonts\Font\Font;

abstract class InstallationManager
{
    protected const CONFIG_FILE = 'tx_webfonts_installed.json';

    protected static $config;
    protected $CONFIG_DIR;
    protected $FONT_DIR;

    public function __construct()
    {
        $fileadminAbsolute = rtrim(Environment::getPublicPath(), "/") . '/' . trim($GLOBALS['TYPO3_CONF_VARS']['BE']['fileadminDir'], "/") . "/";
        $this->CONFIG_DIR = $fileadminAbsolute . "tx_webfonts/"; // with ending /
        $this->FONT_DIR = $fileadminAbsolute . "tx_webfonts/fonts/"; // with ending /

        GeneralUtility::mkdir_deep($this->CONFIG_DIR);
        GeneralUtility::mkdir_deep($this->FONT_DIR);

        if (!file_exists($this->CONFIG_DIR . self::CONFIG_FILE)) {
            file_put_contents($this->CONFIG_DIR . self::CONFIG_FILE, json_encode([]));
        }

        self::$config = array_values(json_decode(file_get_contents($this->CONFIG_DIR . self::CONFIG_FILE), true));
    }

    final public function getInstalledFonts(): array
    {
        return self::$config;
    }

    function deleteFont(Font $font)
    {
        if (!$this->hasInstalled($font)) {
            return;
        }

        $this->deleteFontImpl($font);

        $this->saveConfig();
    }


    public function installFont(Font $font)
    {
        $this->deleteFont($font);
        $this->installFontImpl($font);
        $this->saveConfig();
        $this->createCssImportFile($font);
    }

    private function saveConfig()
    {
        file_put_contents($this->CONFIG_DIR . self::CONFIG_FILE, json_encode(array_values(self::$config)));
    }

    public function installDetails($fontId, $provider)
    {
        foreach (self::$config as $j) {
            if ($j['id'] === $fontId && $j['provider'] == $provider) {
                return $j;
            }
        }
        return null;
    }

    abstract protected function deleteFontImpl(Font $font);

    abstract protected function installFontImpl(Font $font);

    abstract protected function createCssImportFile(Font $font);

    abstract public function hasInstalled(Font $font): bool;

}
