<?php

return [
    'webfonts_list' => [
        'path' => '/webfonts/list',
        'target' => \WEBFONTS\Webfonts\Google\GoogleWebfontAjaxController::class . '::listAction'
    ],
    'webfonts_install' => [
        'path' => '/webfonts/install',
        'target' => \WEBFONTS\Webfonts\Google\GoogleWebfontAjaxController::class . '::installAction'
    ],
];
