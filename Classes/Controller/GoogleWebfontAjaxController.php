<?php
declare(strict_types=1);

namespace WEBFONTS\Webfonts\Controller;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use WEBFONTS\Webfonts\Google\GoogleFont;
use WEBFONTS\Webfonts\Google\GoogleFontInstallationManager;
use WEBFONTS\Webfonts\Google\GoogleWebfontHelperClient;

class GoogleWebfontAjaxController extends AjaxJsonController
{

    /**
     *  Provides the HTML template for the modal and settings for the requested font
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function listAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $fontsArr = GoogleWebfontHelperClient::jsonFontList();

        $list = [];
        foreach ($fontsArr as $font) {
            $list[] = new GoogleFont($font);
        }

        return $this->jsonResponse(
            [
                'fonts' => $list,
                'state' => 0,
            ]
        );
    }

    /**
     * Install and uninstall fonts
     * Providing an empty list as body, will result in font uninstallation
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function installAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!isset($request->getQueryParams()['id'])) {
            return $this->errorResponse(1586072448, 403, 'Font not provided');
        }

        $id = $request->getQueryParams()['id'];
        $provider = $request->getQueryParams()['provider'];
        $body = $request->getParsedBody();

        $variants = $body['variants'];
        $subsets = $body['charsets'];

        $installationHandler = GoogleFontInstallationManager::getInstance();

        if ($subsets === null || $variants === null || count($variants) === 0 || count($subsets) === 0) { // uninstall
            $installationHandler->deleteFont($id, $provider);
            $state = 2;
        } else {
            $installationHandler->installFont($id, $provider, $variants, $subsets);
            $state = 1;
        }

        // send back the installed fonts
        $fontsArr = GoogleWebfontHelperClient::jsonFontList();

        $list = [];
        foreach ($fontsArr as $font) {
            if ($font['id'] === $id) {
                $list[] = new GoogleFont($font);
                break;
            }
        }

        return $this->jsonResponse(
            [
                'fonts' => $list,
                'state' => $state
            ]
        );
    }
}
