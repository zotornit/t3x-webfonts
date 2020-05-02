<?php
declare(strict_types=1);

namespace WEBFONTS\Webfonts\Controller;

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WEBFONTS\Webfonts\Exception\WebfontsException;
use WEBFONTS\Webfonts\Fontawesome\FontawesomeInstallationManager;
use WEBFONTS\Webfonts\Google\GoogleFontInstallationManager;

class AutoSetupController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
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
            if (!isset($font['id'])) {
                throw new WebfontsException('Cannot load font parameter \'id\' is missing.', 1586327403);
            }
            if (!isset($font['charsets'])) {
                throw new WebfontsException('Cannot load font parameter \'charsets\' is missing.', 1586327405);
            }
            if (!isset($font['variants'])) {
                throw new WebfontsException('Cannot load font parameter \'subsets\' is missing.', 1586327406);
            }
            $id = $font['id'];
            $charsets = $font['charsets'];
            $variants = $font['variants'];

            $installManager = GoogleFontInstallationManager::getInstance();
            if (!$installManager->hasInstalled($font)) {
                $requiredVariants = array_map('trim', explode(',', $variants));
                $requiredSubsets = array_map('trim', explode(',', $charsets));

                $installManager->installFont(
                    $id,
                    $provider,
                    $requiredVariants,
                    $requiredSubsets
                );

            }

            /** @var PageRenderer $pageRenderer */
            $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
            $pageRenderer->addCssLibrary('fileadmin/tx_webfonts/fonts/google_webfonts/' . $id . '/import.css');
        }

        if ($provider === 'fontawesome') {
            if (!isset($font['version'])) {
                throw new WebfontsException('Cannot load font parameter \'version\' is missing.', 1588409870);
            }
            if (!isset($font['styles'])) {
                throw new WebfontsException('Cannot load font parameter \'subsets\' is missing.', 1588409871);
            }

            $version = $font['version'];
            $styles = $font['styles'] ?? 'all';
            $styles = array_map('trim', explode(',', $styles));
            $installManager = FontawesomeInstallationManager::getInstance();


            if (!$installManager->hasInstalled($font)) {
                $installManager->installFont($version, $provider, [], []);
            } else {
                // TODO error handling
            }
            /** @var PageRenderer $pageRenderer */
            $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
            $methods = $font['methods'] ?? 'css';
            $methods = array_map('trim', explode(',', $methods));

            $minified = filter_var($font['minified'] ?? true, FILTER_VALIDATE_BOOLEAN);

            foreach ($methods as $method) {
                $method = strtolower($method);
                if (in_array($method, ['css', 'js'])) {
                    foreach ($styles as $style) {
                        $style = strtolower($style);
                        $pageRenderer->addCssLibrary('fileadmin/tx_webfonts/fonts/fontawesome/'
                            . $version . '/fontawesome-free-'
                            . $version . '-web/' . $method . '/' . $style . '.' . ($minified ? 'min.' : '') . $method);
                    }
                }
            }
        }
    }
}
