<?php
namespace TalanHdf\SemanticSuggestionNlp\Scenario;

use TalanHdf\SemanticSuggestionNlp\NLP\Analyzer;

abstract class AbstractScenario
{
    protected $analyzer;

    public function __construct(Analyzer $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    abstract public function process(string $text): array;

    protected function getAnalysis(string $text): array
    {
        return $this->analyzer->analyze($text);
    }
}