<?php

defined('TYPO3_MODE') || die();

call_user_func(function()
{
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        'webfonts',
        'setup',
        "@import 'EXT:webfonts/Configuration/TypoScript/setup.typoscript'"
    );


    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'WEBFONTS.webfonts',
        'WebfontsPlugin',
        [
            \WEBFONTS\Webfonts\Controller\AutoSetupController::class => 'autoSetup',
        ],
        // non-cacheable actions
        [
            \WEBFONTS\Webfonts\Controller\AutoSetupController::class => 'autoSetup',
        ]
    );
});
