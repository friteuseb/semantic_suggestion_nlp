<?php
defined('TYPO3') or die();

(function () {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['semantic_suggestion']['nlpAnalysis'][] = 
        \Vendor\SemanticSuggestionNlp\Hooks\PageAnalysisHook::class . '->analyze';

    // Register backend module for NLP configuration
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'SemanticSuggestionNlp',
        'NlpConfig',
        [\Vendor\SemanticSuggestionNlp\Controller\ConfigurationController::class => 'index,save'],
        [\Vendor\SemanticSuggestionNlp\Controller\ConfigurationController::class => 'index,save']
    );
})();