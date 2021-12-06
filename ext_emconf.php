<?php

$EM_CONF['webfonts'] = array(
    'title' => 'Download webfonts for self-hosting',
    'description' => 'Easy way to use self-hosted webfonts on your website.',
    'category' => 'plugin',
	'author' => 'Thomas Pronold',
    'author_email' => 'tp@zotorn.de',
	'author_company' => 'Zotorn IT | zotorn.de',
    'state' => 'beta',
    'uploadfolder' => false,
    'clearCacheOnLoad' => 1,
    'version' => '0.2.0',
    'constraints' =>
        array(
            'depends' =>
                array(
                    'typo3' => '10.4.0-11.5.99',
                    'php' => '7.2.0-7.4.99',
                    'vuejs' => '1.1.0-1.1.99',
                ),
            'conflicts' =>
                array(),
            'suggests' =>
                array(),
        ),
    'clearcacheonload' => true,
    'autoload' => [
        'psr-4' => [
            'WEBFONTS\\Webfonts\\' => 'Classes'
        ]
    ]
);

