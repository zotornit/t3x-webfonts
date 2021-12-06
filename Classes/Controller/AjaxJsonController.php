<?php
declare(strict_types=1);

namespace WEBFONTS\Webfonts\Controller;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Fluid\View\StandaloneView;

abstract class AjaxJsonController extends ActionController
{

    /**
     * Helper method to initialize a standalone view instance.
     *
     * @param ServerRequestInterface $request
     * @param string $templatePath
     * @return StandaloneView
     * @internal param string $template
     */
    protected function initializeStandaloneView(ServerRequestInterface $request, string $templatePath): StandaloneView
    {
        $viewRootPath = GeneralUtility::getFileAbsFileName('EXT:webfonts/Resources/Private/');
        /** @var StandaloneView $view */
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->getRequest()->setControllerExtensionName('Webfonts');
        $view->setTemplatePathAndFilename($viewRootPath . 'Templates/' . $templatePath);
        $view->setLayoutRootPaths([$viewRootPath . 'Layouts/']);
        $view->setPartialRootPaths([$viewRootPath . 'Partials/']);
        return $view;
    }


    protected function webfontsJsonResponse(array $payload, int $httpStatus = 200, string $message = 'OK'): \Psr\Http\Message\ResponseInterface
    {
        $response = new JsonResponse([
            'status' => $httpStatus,
            'message' => $message,
            'time' => time(),
            'payload' => $payload
        ]);
        return $response->withStatus($httpStatus);
    }

    protected function errorResponse(int $error, int $httpStatus, string $message)
    {
        $response = new JsonResponse([
            'status' => $httpStatus,
            'message' => $message,
            'time' => time(),
            'error' => $error
        ]);

        return $response->withStatus($httpStatus);
    }
}
