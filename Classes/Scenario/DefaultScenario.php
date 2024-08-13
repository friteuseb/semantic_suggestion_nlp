<?php
namespace TalanHdf\SemanticSuggestionNlp\Scenario;

class DefaultScenario extends AbstractScenario
{
    public function process(string $text): array
    {
        $analysis = $this->getAnalysis($text);
        
        $suggestions = [];
        
        if ($analysis['wordCount'] < 200) {
            $suggestions[] = 'Le contenu est relativement court. Envisagez d\'ajouter plus de détails pour améliorer le référencement.';
        }
        
        // Check if wordCount is not zero before dividing
        if ($analysis['wordCount'] > 0 && $analysis['uniqueWordCount'] / $analysis['wordCount'] < 0.4) {
            $suggestions[] = 'La diversité du vocabulaire pourrait être améliorée. Essayez d\'utiliser plus de synonymes.';
        }
        
        if ($analysis['textComplexity'] > 8) {
            $suggestions[] = 'Le texte semble complexe. Envisagez de simplifier certaines phrases pour améliorer la lisibilité.';
        } elseif ($analysis['textComplexity'] < 4) {
            $suggestions[] = 'Le texte est très simple. Vous pourriez enrichir le vocabulaire pour un public plus avancé.';
        }
        
        foreach ($analysis['topWords'] as $word => $count) {
            if ($count > $analysis['wordCount'] * 0.03) {
                $suggestions[] = "Le mot '$word' apparaît fréquemment. Pensez à utiliser des synonymes pour varier le contenu.";
            }
        }
        
        return [
            'analysis' => $analysis,
            'suggestions' => $suggestions,
        ];
    }
}