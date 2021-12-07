<?php


defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function () {

        $typo3VersionArray = \TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionStringToArray(\TYPO3\CMS\Core\Utility\VersionNumberUtility::getCurrentTypo3Version());

        //
        if ($typo3VersionArray['version_main'] >= 11 && $typo3VersionArray['version_sub'] >= 5) {
            // extbase approach
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'WEBFONTS.webfonts', // ext key
                'tools', // as child of site main module
                'webfonts', // no _ allowed, submodule key
                '',
                [
                    \WEBFONTS\Webfonts\Controller\WebfontsController::class => 'main',
                ],
                [
                    'routeTarget' => \WEBFONTS\Webfonts\Controller\WebfontsController::class . '::mainAction',
                    'access' => 'admin',
                    'icon' => 'EXT:webfonts/Resources/Public/Icons/module-webfonts.svg',
                    'labels' => 'LLL:EXT:webfonts/Resources/Private/Language/mod_webfonts.xlf'
                ]
            );
        } else {

            // LEGACY for TYPO3 10

            // extbase approach
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'WEBFONTS.webfonts', // ext key
                'tools', // as child of site main module
                'webfonts', // no _ allowed, submodule key
                '',
                [
                    \WEBFONTS\Webfonts\Controller\WebfontsLegacyController::class => 'main',
                ],
                [
                    'routeTarget' => \WEBFONTS\Webfonts\Controller\WebfontsLegacyController::class . '::mainAction',
                    'access' => 'admin',
                    'icon' => 'EXT:webfonts/Resources/Public/Icons/module-webfonts.svg',
                    'labels' => 'LLL:EXT:webfonts/Resources/Private/Language/mod_webfonts.xlf'
                ]
            );
        }

    }
);
