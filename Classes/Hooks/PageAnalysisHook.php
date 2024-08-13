<?php
namespace TalanHdf\SemanticSuggestionNlp\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TalanHdf\SemanticSuggestionNlp\NLP\Analyzer;
use TalanHdf\SemanticSuggestionNlp\Scenario\DefaultScenario;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class PageAnalysisHook
{
    public function analyze(array &$params)
    {
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('semantic_suggestion_nlp');

        // Vérifier si l'analyse NLP est activée
        if (!$extensionConfiguration['enableNlpAnalysis']) {
            return;
        }

        $analyzer = GeneralUtility::makeInstance(Analyzer::class);
        
        // Utiliser le scénario sélectionné ou le scénario par défaut
        $scenarioClass = $extensionConfiguration['selectedScenario'] ?? DefaultScenario::class;
        $scenario = GeneralUtility::makeInstance($scenarioClass, $analyzer);

        $content = $params['content'];
        $nlpResults = $scenario->process($content);

        // Fusionner les résultats NLP avec l'analyse existante
        $params['analysis'] = array_merge($params['analysis'] ?? [], ['nlp' => $nlpResults]);
    }
}