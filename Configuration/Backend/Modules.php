<?php

//use T3docs\Examples\Controller\ModuleController;
//use T3docs\Examples\Controller\AdminModuleController;

/**
 * Definitions for modules provided by EXT:examples
 */
return [
    'webfonts' => [
        'parent' => 'site',
        'position' => ['end'],
        'access' => 'admin',
        'path' => '/module/tools/webfonts',
        'labels' => 'LLL:EXT:webfonts/Resources/Private/Language/mod_webfonts.xlf',
        'icon' => 'EXT:webfonts/Resources/Public/Icons/module-webfonts.svg',
        'extensionName' => 'Webfonts',
        'inheritNavigationComponentFromMainModule' => false, // remove page tree
        'controllerActions' => [
            \WEBFONTS\Webfonts\Controller\WebfontsController::class => [
                'listInstalledFonts',
                'listGoogleFonts',
                'manageGoogleFont',
                'installGoogleFont',
                'uninstallGoogleFont',
                'listFontawesomeFonts',
            ],
        ],
    ],
];
