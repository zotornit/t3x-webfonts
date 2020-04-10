<?php
declare(strict_types=1);

namespace WEBFONTS\Webfonts\Controller;

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WEBFONTS\Webfonts\Exception\WebfontsException;
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
        if (!isset($font['id'])) {
            throw new WebfontsException('Cannot load font parameter \'id\' is missing.', 1586327403);
        }
        if (!isset($font['provider'])) {
            throw new WebfontsException('Cannot load font parameter \'provider\' is missing.', 1586327404);
        }
        if (!isset($font['charsets'])) {
            throw new WebfontsException('Cannot load font parameter \'charsets\' is missing.', 1586327405);
        }
        if (!isset($font['variants'])) {
            throw new WebfontsException('Cannot load font parameter \'subsets\' is missing.', 1586327406);
        }

        $id = $font['id'];
        $provider = $font['provider'];
        $charsets = $font['charsets'];
        $variants = $font['variants'];

        if ($provider === 'google_webfonts') {

            $installManager = GoogleFontInstallationManager::getInstance();
            if (!$installManager->hasInstalled($id, $provider, $variants, $charsets)) {

                $requiredVariants = array_map('trim', explode(',', $variants));
                $requiredSubsets = array_map('trim', explode(',', $charsets));

                $installManager->installFont(
                    $id,
                    $provider,
                    $requiredVariants,
                    $requiredSubsets
                );

            }

            $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
            $pageRenderer->addCssLibrary('fileadmin/tx_webfonts/fonts/google_webfonts/' . $id . '/import.css');
        }
    }
}
