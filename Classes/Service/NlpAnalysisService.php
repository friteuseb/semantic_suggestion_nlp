<?php
namespace TalanHdf\SemanticSuggestionNlp\Service;

use TalanHdf\SemanticSuggestionNlp\NLP\Analyzer;

class NlpAnalysisService
{
    protected $analyzer;

    public function __construct(Analyzer $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    public function analyzeContent(array $analysisResults): array
    {
        $nlpStatistics = [
            'totalWordCount' => 0,
            'totalUniqueWordCount' => 0,
            'totalComplexity' => 0,
            'allTopWords' => [],
            'allEntities' => [],
            'sentimentDistribution' => ['positive' => 0, 'neutral' => 0, 'negative' => 0],
            'complexityDistribution' => ['simple' => 0, 'medium' => 0, 'complex' => 0],
            'totalReadingTime' => 0,
        ];

        foreach ($analysisResults as $pageData) {
            $content = $pageData['content']['content'] ?? '';
            $nlpData = $this->analyzer->analyze($content);

            $nlpStatistics['totalWordCount'] += $nlpData['wordCount'];
            $nlpStatistics['totalUniqueWordCount'] += $nlpData['uniqueWordCount'];
            $nlpStatistics['totalComplexity'] += $nlpData['textComplexity'];
            
            foreach ($nlpData['topWords'] as $word => $count) {
                if (!isset($nlpStatistics['allTopWords'][$word])) {
                    $nlpStatistics['allTopWords'][$word] = 0;
                }
                $nlpStatistics['allTopWords'][$word] += $count;
            }

            // Ajoutez ici la logique pour les entités nommées si disponible
            
            if ($nlpData['sentiment'] > 0.1) {
                $nlpStatistics['sentimentDistribution']['positive']++;
            } elseif ($nlpData['sentiment'] < -0.1) {
                $nlpStatistics['sentimentDistribution']['negative']++;
            } else {
                $nlpStatistics['sentimentDistribution']['neutral']++;
            }

            if ($nlpData['textComplexity'] < 5) {
                $nlpStatistics['complexityDistribution']['simple']++;
            } elseif ($nlpData['textComplexity'] < 8) {
                $nlpStatistics['complexityDistribution']['medium']++;
            } else {
                $nlpStatistics['complexityDistribution']['complex']++;
            }

            $nlpStatistics['totalReadingTime'] += $nlpData['wordCount'] / 200; // Assuming 200 words per minute
        }

        $pageCount = count($analysisResults);
        
        return [
            'averageWordCount' => $pageCount > 0 ? $nlpStatistics['totalWordCount'] / $pageCount : 0,
            'averageUniqueWordCount' => $pageCount > 0 ? $nlpStatistics['totalUniqueWordCount'] / $pageCount : 0,
            'averageComplexity' => $pageCount > 0 ? $nlpStatistics['totalComplexity'] / $pageCount : 0,
            'topWords' => array_slice($nlpStatistics['allTopWords'], 0, 10, true),
            'sentimentDistribution' => $nlpStatistics['sentimentDistribution'],
            'complexityDistribution' => $nlpStatistics['complexityDistribution'],
            'averageReadingTime' => $pageCount > 0 ? $nlpStatistics['totalReadingTime'] / $pageCount : 0,
            'totalPages' => $pageCount,
        ];
    }


    public function analyzeNlp(array $analysisResults): array
    {
        $nlpResults = [];
    
        foreach ($analysisResults as $pageId => $pageData) {
            $content = $pageData['content']['content'] ?? '';
    
            // Analyse NLP du contenu en utilisant MyNlpLibrary
            $nlpData = $this->analyzer->analyze($content);
    
            // Extraction des informations pertinentes de l'analyse NLP
            $nlpResults[$pageId] = [
                'topWords' => $this->extractTopWords($nlpData),
                'namedEntities' => $this->extractNamedEntities($nlpData), 
                'sentiment' => $this->extractSentiment($nlpData),
                // Ajoutez d'autres métriques NLP pertinentes ici si nécessaire
            ];
        }
    
        return $nlpResults;
    }
    
    // Méthodes d'extraction d'informations NLP (à adapter selon votre bibliothèque NLP)
    private function extractTopWords(array $nlpData): array
    {
        // ... Logique pour extraire les mots les plus fréquents de $nlpData
    }
    
    private function extractNamedEntities(array $nlpData): array
    {
        // ... Logique pour extraire les entités nommées de $nlpData
    }
    
    private function extractSentiment(array $nlpData): float
    {
        // ... Logique pour extraire le score de sentiment de $nlpData
    }

    

    
    public function combineResults(array $classicResults, array $nlpResults, float $nlpWeight = 0.3): array
    {
        $combinedResults = $classicResults; // Commencez par copier les résultats classiques
    
        // Parcourez les résultats NLP et combinez-les avec les résultats classiques
        foreach ($nlpResults as $pageId => $nlpData) {
            if (isset($combinedResults[$pageId])) {
                // Combinez les scores de similarité en utilisant le poids NLP
                foreach ($combinedResults[$pageId]['similarities'] as $otherPageId => &$similarityData) {
                    if (isset($nlpResults[$otherPageId])) {
                        $classicSimilarity = $similarityData['score'];
                        $nlpSimilarity = $this->calculateNlpSimilarity($nlpData, $nlpResults[$otherPageId]); 
                        $similarityData['score'] = ((1 - $nlpWeight) * $classicSimilarity) + ($nlpWeight * $nlpSimilarity);
                        // Vous pouvez également mettre à jour d'autres champs de similarité si nécessaire
                    }
                }
            }
        }
    
        return $combinedResults;
    }
}