<?php
defined('TYPO3') or die();

(function () {
    // Register the NLP analysis hook
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['semantic_suggestion']['nlpAnalysis'][] = 
        \TalanHdf\SemanticSuggestionNlp\Hooks\PageAnalysisHook::class . '->analyze';
    
        // Enregistrement du hook pour les statistiques NLP
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['semantic_suggestion']['nlpStatistics'][] = 
    \TalanHdf\SemanticSuggestionNlp\Hooks\NlpStatisticsHook::class;

    
    // Register backend module for NLP configuration
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'SemanticSuggestionNlp',
        'NlpConfig',
        [
            \TalanHdf\SemanticSuggestionNlp\Controller\ConfigurationController::class => 'index,save'
        ],
        [
            \TalanHdf\SemanticSuggestionNlp\Controller\ConfigurationController::class => 'index,save'
        ]
    );

    // Add TypoScript setup
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
        '@import "EXT:semantic_suggestion_nlp/Configuration/TypoScript/setup.typoscript"'
    );
})();