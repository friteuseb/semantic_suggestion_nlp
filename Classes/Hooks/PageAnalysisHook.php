<?php
namespace TalanHdf\SemanticSuggestionNlp\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TalanHdf\SemanticSuggestionNlp\NLP\Analyzer;
use TalanHdf\SemanticSuggestionNlp\Scenario\DefaultScenario;

class PageAnalysisHook
{
    public function analyze(array &$params)
    {
        // Check if NLP analysis is enabled in the extension configuration
        $extConf = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['semantic_suggestion_nlp'] ?? null;
        if (!$extConf || !$extConf['enableNlpAnalysis']) {
            return;
        }

        $analyzer = GeneralUtility::makeInstance(Analyzer::class);
        $scenario = GeneralUtility::makeInstance(DefaultScenario::class, $analyzer);

        $content = $params['content'];
        $nlpResults = $scenario->process($content);

        // Merge NLP results with existing analysis
        $params['analysis'] = array_merge($params['analysis'] ?? [], ['nlp' => $nlpResults]);

        // Add NLP-based suggestions
        if (isset($nlpResults['suggestions'])) {
            foreach ($nlpResults['suggestions'] as $suggestion) {
                $params['suggestions'][] = [
                    'type' => 'nlp',
                    'message' => $suggestion,
                ];
            }
        }
    }
}