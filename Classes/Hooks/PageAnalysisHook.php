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
        try {
            $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('semantic_suggestion_nlp');

            // Vérifier si l'analyse NLP est activée
            if (!$extensionConfiguration['enableNlpAnalysis']) {
                return;
            }

            $analyzer = GeneralUtility::makeInstance(Analyzer::class);
            
            // Utiliser le scénario sélectionné ou le scénario par défaut
            $scenarioClass = $extensionConfiguration['selectedScenario'] ?? DefaultScenario::class;
            if (!class_exists($scenarioClass)) {
                $scenarioClass = DefaultScenario::class;
            }
            $scenario = GeneralUtility::makeInstance($scenarioClass, $analyzer);

            $content = $params['content'];
            $nlpResults = $scenario->process($content);

            // Fusionner les résultats NLP avec l'analyse existante
            $params['analysis'] = array_merge($params['analysis'] ?? [], ['nlp' => $nlpResults]);

            // Ajouter des suggestions basées sur l'analyse NLP
            if (isset($nlpResults['suggestions'])) {
                foreach ($nlpResults['suggestions'] as $suggestion) {
                    $params['suggestions'][] = [
                        'type' => 'nlp',
                        'message' => $suggestion,
                    ];
                }
            }

        } catch (\Exception $e) {
            // Log l'erreur ou gérez-la selon vos besoins
            GeneralUtility::makeInstance(\TYPO3\CMS\Core\Log\LogManager::class)
                ->getLogger(__CLASS__)
                ->error('Error in NLP analysis: ' . $e->getMessage(), ['exception' => $e]);
        }
    }
}