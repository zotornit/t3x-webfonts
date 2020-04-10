<?php
declare(strict_types=1);

namespace WEBFONTS\Webfonts\Controller;

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

class WebfontsController extends \VUEJS\Vuejs\Controller\VueBackendController
{

    protected function initializeView(ViewInterface $view)
    {
        parent::initializeView($view);

        // register view as requireJS module
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);

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
