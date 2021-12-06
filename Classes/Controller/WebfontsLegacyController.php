<?php
declare(strict_types=1);

namespace WEBFONTS\Webfonts\Controller;

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use VUEJS\Vuejs\Controller\VueBackendLegacyController;

// only to support TYPO3 10 for while
class WebfontsLegacyController extends VueBackendLegacyController
{

    protected function initializeView(ViewInterface $view)
    {
        parent::initializeView($view);

        // register view as requireJS module
        $pageRenderer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);

        $pageRenderer->addCssFile('EXT:webfonts/Resources/Public/Css/Contrib/Pretty-Checkbox/pretty-checkbox.min.css');
        $pageRenderer->addCssFile('EXT:webfonts/Resources/Public/Css/pretty-checkbox.css');
        $pageRenderer->addCssFile('EXT:webfonts/Resources/Public/Css/modal.css');
        $pageRenderer->addCssFile('EXT:webfonts/Resources/Public/Css/styles.css');

        $this->view->getModuleTemplate()->getDocHeaderComponent()->disable();

    }


    public function mainAction()
    {

    }

}
