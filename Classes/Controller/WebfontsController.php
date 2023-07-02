<?php
declare(strict_types=1);

namespace WEBFONTS\Webfonts\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Attribute\Controller;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use WEBFONTS\Webfonts\Google\APIGoogleFont;
use WEBFONTS\Webfonts\Google\GoogleFont;
use WEBFONTS\Webfonts\Google\GoogleFontInstallationManager;
use WEBFONTS\Webfonts\Google\GoogleWebfontHelperClient;

#[Controller]
class WebfontsController extends ActionController
{

    public function __construct(
        protected readonly ModuleTemplateFactory $moduleTemplateFactory,
        protected readonly PageRenderer          $pageRenderer
    )
    {
    }

    public function listGoogleFontsAction(): ResponseInterface
    {

        $fontsArr = GoogleWebfontHelperClient::jsonFontList();
        $apiFontsArr = array_map(function ($e) {
            return new APIGoogleFont($e);
        }, $fontsArr);
        $installationHandler = GoogleFontInstallationManager::getInstance();

        $view = $this->moduleTemplateFactory->create($this->request);
        $view->assignMultiple([
            'fontListStringified' => json_encode($apiFontsArr),
            'installedFontListStringified' => json_encode($installationHandler->getInstalledFonts()),
            'backendCssUrl' => $this->getBackendCssUrl(),
            'activeTab' => 'listGoogleFonts',
        ]);

        return $view->renderResponse('Webfonts/ListGoogleFonts');
    }

    private function getBackendCssUrl(): string
    {
        $file = 'EXT:backend/Resources/Public/Css/backend.css';
        $file = Environment::getPublicPath() . '/' . PathUtility::getPublicResourceWebPath($file, false);
        // as the path is now absolute, make it "relative" to the current script to stay compatible
        $file = PathUtility::getRelativePathTo($file) ?? '';
        $file = rtrim($file, '/');
        $file = GeneralUtility::createVersionNumberedFilename($file);
        return PathUtility::getAbsoluteWebPath($file);
    }

    public function manageGoogleFontAction(): ResponseInterface
    {


        $fontId = $this->request->getArgument('id');
        $fontsArr = GoogleWebfontHelperClient::jsonFontList();

        $font = null;
        foreach ($fontsArr as $fontItem) {
            if ($fontItem['id'] == $fontId) {
                $font = $fontItem;
                break;
            }
        }

        $font['provider'] = 'google_webfonts';
        $font['charsets'] = $font['subsets'];

        $fontObj = new APIGoogleFont($font);


        // install the font
        $installationHandler = GoogleFontInstallationManager::getInstance();
        $installationHandler->installFontPreview(new GoogleFont([
            'provider' => $fontObj->getProvider(),
            'id' => $fontObj->getId(),
            'variants' => $fontObj->getVariants(),
            'charsets' => $fontObj->getSubsets(),
        ]));


        $installedFonts = $installationHandler->getInstalledFonts();

        $alreadyInstalledFont = null;
        foreach ($installedFonts as $installedFont) {
            if ($installedFont['id'] == $fontId) {
                $alreadyInstalledFont = $installedFont;
                break;
            }
        }


        $this->pageRenderer->addCssFile(
            $installationHandler->getFontsDir()
            . '/google_webfonts/_be-preview/import.css'
        );

        $view = $this->moduleTemplateFactory->create($this->request);

        $view->assignMultiple([
            'font' => $font,
            'fontStringified' => json_encode($font),
            'selectedSubsetsStringified' =>
                json_encode($alreadyInstalledFont['subsets'] ?? [$fontObj->getDefSubset()]),
            'selectedVariantsStringified' =>
                json_encode($alreadyInstalledFont['variants'] ?? [$fontObj->getDefVariant()]),
            'backendCssUrl' => $this->getBackendCssUrl(),
            'isInstalled' => $alreadyInstalledFont != null ? 'true' : 'false',
            'activeTab' => 'listGoogleFonts',
        ]);

        return $view->renderResponse('Webfonts/ManageGoogleFont');
    }

    public function installGoogleFontAction(): ResponseInterface
    {
        $fontId = $this->request->getArgument('id');
        $fontSubsets = $this->request->getArgument('subsets');
        $fontVariants = $this->request->getArgument('variants');

        $installationHandler = GoogleFontInstallationManager::getInstance();

        $installationHandler->deleteFont(new GoogleFont([
            'provider' => 'google_webfonts',
            'id' => $fontId,
            'variants' => [],
            'charsets' => [],
        ]));
        $installationHandler->installFont(new GoogleFont([
            'provider' => 'google_webfonts',
            'id' => $fontId,
            'variants' => explode(",", $fontVariants),
            'charsets' => explode(",", $fontSubsets),
        ]));

        return $this->redirect('manageGoogleFont');
    }

    public function uninstallGoogleFontAction(): ResponseInterface
    {
        $fontId = $this->request->getArgument('id');

        $installationHandler = GoogleFontInstallationManager::getInstance();
        $installationHandler->deleteFont(new GoogleFont([
            'provider' => 'google_webfonts',
            'id' => $fontId,
            'variants' => [],
            'charsets' => [],
        ]));


        return $this->redirect('manageGoogleFont');
    }

    public function listInstalledFontsAction(): ResponseInterface
    {
        $fontsArr = GoogleWebfontHelperClient::jsonFontList();
        $apiFontsArr = array_map(function ($e) {
            return new APIGoogleFont($e);
        }, $fontsArr);
        $installationHandler = GoogleFontInstallationManager::getInstance();

        $view = $this->moduleTemplateFactory->create($this->request);
        $view->assignMultiple([
            'fontListStringified' => json_encode($apiFontsArr),
            'installedFontListStringified' => json_encode($installationHandler->getInstalledFonts()),
            'backendCssUrl' => $this->getBackendCssUrl(),
            'activeTab' => 'listInstalledFonts',
        ]);

        return $view->renderResponse('Webfonts/ListInstalledFonts');
    }

    public function listFontawesomeFontsAction(): ResponseInterface
    {
        $view = $this->moduleTemplateFactory->create($this->request);

        $view->assignMultiple([
            'backendCssUrl' => $this->getBackendCssUrl(),
            'activeTab' => 'listFontawesomeFonts',
        ]);

        return $view->renderResponse('Webfonts/ListFontawesomeFonts');
    }

    protected function initializeView($view): void
    {
        if (ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend()) {
            return;
        }

        $this->pageRenderer->addCssFile('EXT:webfonts/Resources/Public/Css/styles.css');

        $this->pageRenderer->loadJavaScriptModule(
            '@WEBFONTS/webfonts/webfonts.js'
        );
    }

}
