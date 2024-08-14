<?php
namespace TalanHdf\SemanticSuggestionNlp\Hooks;

use TalanHdf\SemanticSuggestionNlp\NLP\Analyzer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class NlpStatisticsHook
{
    protected $analyzer;

    public function __construct(Analyzer $analyzer = null)
    {
        $this->analyzer = $analyzer ?? GeneralUtility::makeInstance(Analyzer::class);
    }

    public function getNlpStatistics(array $analysisResults): array
    {
        return $this->analyzer->calculateNlpStatistics($analysisResults);
    }
}