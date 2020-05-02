<?php
declare(strict_types=1);

namespace WEBFONTS\Webfonts\Controller;

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use WEBFONTS\Webfonts\Exception\WebfontsException;
use WEBFONTS\Webfonts\Fontawesome\FontawesomeFont;
use WEBFONTS\Webfonts\Fontawesome\FontawesomeInstallationManager;
use WEBFONTS\Webfonts\Google\GoogleFont;
use WEBFONTS\Webfonts\Google\GoogleFontInstallationManager;

class AutoSetupController extends ActionController
{


    function autoSetupAction()
    {
        if (!isset($this->settings['fonts']) || !is_array($this->settings['fonts'])) {
            return '';
        }

        foreach ($this->settings['fonts'] as $font) {
            $this->installFont($font);
        }

        return '';
    }

    private function installFont($font)
    {
        if (!isset($font['provider'])) {
            throw new WebfontsException('Cannot load font parameter \'provider\' is missing.', 1586327404);
        }

        $provider = $font['provider'];

        if ($provider === 'google_webfonts') {
            $gfont = new GoogleFont($font);
            $installManager = GoogleFontInstallationManager::getInstance();
            if (!$installManager->hasInstalled($gfont)) {
                $installManager->installFont($gfont);
            }
            /** @var PageRenderer $pageRenderer */
            $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
            $pageRenderer->addCssLibrary('fileadmin/tx_webfonts/fonts/google_webfonts/' . $gfont->getId() . '/import.css');
        }

        if ($provider === 'fontawesome') {
            $faFont = new FontawesomeFont($font);
            $installManager = FontawesomeInstallationManager::getInstance();
            if (!$installManager->hasInstalled($faFont)) {
                $installManager->installFont($faFont);
            }
            /** @var PageRenderer $pageRenderer */
            $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
            $minified = filter_var($font['minified'] ?? true, FILTER_VALIDATE_BOOLEAN);

            foreach ($faFont->getMethods() as $method) {
                $method = strtolower($method);
                if (in_array($method, ['css', 'js'])) {
                    foreach ($faFont->getStyles() as $style) {
                        $style = strtolower($style);
                        $pageRenderer->addCssLibrary('fileadmin/tx_webfonts/fonts/fontawesome/'
                            . $faFont->getVersion() . '/fontawesome-free-'
                            . $faFont->getVersion() . '-web/' . $method . '/' . $style . '.' . ($minified ? 'min.' : '') . $method);
                    }
                }
            }
        }
    }
}
