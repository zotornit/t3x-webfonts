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
    'version' => '0.0.4',
    'constraints' =>
        array(
            'depends' =>
                array(
                    'typo3' => '9.5.0-10.4.99',
                    'php' => '7.2.0-7.4.99',
                    'vuejs' => '1.0.2-1.0.99',
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

