<?php
namespace TalanHdf\SemanticSuggestionNlp\Hooks;

use TalanHdf\SemanticSuggestionNlp\Service\NlpAnalysisService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class NlpAnalysisHook
{
    protected $nlpAnalysisService;

    public function __construct(NlpAnalysisService $nlpAnalysisService = null)
    {
        $this->nlpAnalysisService = $nlpAnalysisService ?? GeneralUtility::makeInstance(NlpAnalysisService::class);
    }

    public function analyze(array &$params)
    {
        $content = $params['content'];
        $nlpResults = $this->nlpAnalysisService->analyzeContent($content);
        
        // Merge NLP results with existing analysis
        $params['analysis'] = array_merge($params['analysis'] ?? [], ['nlp' => $nlpResults]);
    }
}