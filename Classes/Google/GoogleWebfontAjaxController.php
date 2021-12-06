<?php
declare(strict_types=1);

namespace WEBFONTS\Webfonts\Google;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WEBFONTS\Webfonts\Controller\AjaxJsonController;

class GoogleWebfontAjaxController extends AjaxJsonController
{

    /**
     *  Provides the HTML template for the modal and settings for the requested font
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function listAction(ServerRequestInterface $request): ResponseInterface
    {
        $fontsArr = GoogleWebfontHelperClient::jsonFontList();

        $list = [];
        foreach ($fontsArr as $font) {
            $list[] = new APIGoogleFont($font);
        }

        return $this->webfontsJsonResponse(
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
     * @throws \WEBFONTS\Webfonts\Exception\WebfontsException
     */
    public function installAction(ServerRequestInterface $request): ResponseInterface
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
            $installationHandler->deleteFont(new GoogleFont([
                'provider' => $provider,
                'id' => $id,
                'variants' => [],
                'charsets' => [],
            ]));
            $state = 2;
        } else {
            $installationHandler->installFont(new GoogleFont([
                'provider' => $provider,
                'id' => $id,
                'variants' => $variants,
                'charsets' => $subsets,
            ]));
            $state = 1;
        }

        // send back the installed fonts
        $fontsArr = GoogleWebfontHelperClient::jsonFontList();

        $list = [];
        foreach ($fontsArr as $font) {
            if ($font['id'] === $id) {
                $list[] = new APIGoogleFont($font);
                break;
            }
        }

        return $this->webfontsJsonResponse(
            [
                'fonts' => $list,
                'state' => $state
            ]
        );
    }
}
