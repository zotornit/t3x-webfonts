<?php
declare(strict_types=1);

namespace WEBFONTS\Webfonts\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Attribute\Controller;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use WEBFONTS\Webfonts\Fontawesome\FontawesomeFont;
use WEBFONTS\Webfonts\Fontawesome\FontawesomeInstallationManager;
use WEBFONTS\Webfonts\Google\GoogleFont;
use WEBFONTS\Webfonts\Google\GoogleFontInstallationManager;

#[Controller]
class TypescriptSetupController extends ActionController
{
    public function setupAction(): ResponseInterface
    {
        if (!isset($this->settings['fonts']) || !is_array($this->settings['fonts'])) {
            return $this->responseFactory->createResponse();
        }

        foreach ($this->settings['fonts'] as $font) {
            $this->installFonts($font);
        }
        return $this->responseFactory->createResponse();
    }

    private function installFonts($font): void
    {
        $this->installGoogleWebfonts($font);
        $this->installFontawesome($font);
    }

    private function installGoogleWebfonts($font): void
    {

        if ($font['provider'] !== 'google_webfonts') {
            return;
        }

        $gfont = new GoogleFont($font);
        $installManager = GoogleFontInstallationManager::getInstance();
        if (!$installManager->hasInstalled($gfont)) {
            $installManager->installFont($gfont);
        }
        /** @var PageRenderer $pageRenderer */
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->addCssLibrary('fileadmin/tx_webfonts/fonts/google_webfonts/' . $gfont->getId() . '/import.css');
    }

    private function installFontawesome($font): void
    {
        if ($font['provider'] !== 'fontawesome') {
            return;
        }

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
                        . $faFont->getVersion()
                        . '/fontawesome-free-'
                        . $faFont->getVersion()
                        . '-web/'
                        . $method
                        . '/'
                        . $style
                        . '.'
                        . ($minified ? 'min.' : '')
                        . $method
                    );
                }
            }
        }
    }
}
