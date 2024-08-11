<?php
namespace TalanHdf\SemanticSuggestionNlp\Scenario;

class DefaultScenario extends AbstractScenario
{
    public function process(string $text): array
    {
        $analysis = $this->getAnalysis($text);
        
        // Implement default scenario logic here
        $suggestions = [];
        
        if ($analysis['wordCount'] < 100) {
            $suggestions[] = 'Consider adding more content to improve SEO.';
        }
        
        if ($analysis['uniqueWordCount'] / $analysis['wordCount'] < 0.5) {
            $suggestions[] = 'Try to use a more diverse vocabulary to enhance content quality.';
        }
        
        foreach ($analysis['topWords'] as $word => $count) {
            if ($count > 5) {
                $suggestions[] = "The word '$word' appears frequently. Consider using synonyms for variety.";
            }
        }
        
        return [
            'analysis' => $analysis,
            'suggestions' => $suggestions,
        ];
    }
}