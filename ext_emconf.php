<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Semantic Suggestion NLP',
    'description' => 'Extends semantic_suggestion with Natural Language Processing capabilities',
    'category' => 'plugin',
    'author' => 'Cyril Wolfangel',
    'author_email' => 'cyril.wolfangel@gmail.com',
    'state' => 'alpha',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-13.9.99',
            'semantic_suggestion' => '',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];