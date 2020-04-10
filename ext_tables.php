<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function () {

        // extbase approach
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'WEBFONTS.webfonts', // ext key
            'tools', // as child of site main module
            'webfonts', // no _ allowed, submodule key
            '',
            [
                'Webfonts' => 'main',
            ],
            [
                'routeTarget' => \WEBFONTS\Webfonts\Controller\WebfontsController::class . '::mainAction',
                'access' => 'admin',
                'icon' => 'EXT:webfonts/Resources/Public/Icons/module-webfonts.svg',
                'labels' => 'LLL:EXT:webfonts/Resources/Private/Language/mod_webfonts.xlf'
            ]
        );

    }
);
