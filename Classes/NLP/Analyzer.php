<?php
namespace TalanHdf\SemanticSuggestionNlp\NLP;

use NlpTools\Tokenizers\WhitespaceTokenizer;
use NlpTools\Stemmers\PorterStemmer;
use NlpTools\Similarity\CosineSimilarity;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Analyzer
{
    private $tokenizer;
    private $stemmer;
    private $similarity;
    private $stopWords;
    private $connectionPool;

    public function __construct(ConnectionPool $connectionPool = null)
    {
        $this->tokenizer = new WhitespaceTokenizer();
        $this->stemmer = new PorterStemmer();
        $this->similarity = new CosineSimilarity();
        $this->stopWords = $this->getFrenchStopWords();
        $this->connectionPool = $connectionPool ?? GeneralUtility::makeInstance(ConnectionPool::class);
    }

    public function analyze(string $text): array
    {
        $tokens = $this->tokenizer->tokenize($text);
        $tokensWithoutStopWords = $this->removeStopWords($tokens);
        $stems = array_map([$this->stemmer, 'stem'], $tokensWithoutStopWords);

        $wordCount = count($tokens);
        $uniqueWordCount = count(array_unique($stems));

        $topWords = $this->getTopWords($stems, 5);

        return [
            'wordCount' => $wordCount,
            'uniqueWordCount' => $uniqueWordCount,
            'topWords' => $topWords,
            'textComplexity' => $this->calculateTextComplexity($tokensWithoutStopWords),
            'sentiment' => $this->analyzeSentiment($text),
        ];
    }

    public function getPageNlpData($pageUid): array
    {
        $pageContent = $this->getPageContent($pageUid);
        return $this->analyze($pageContent);
    }

    public function calculateNlpSimilarity(array $nlp1, array $nlp2): float
    {
        $similarity = 0.0;
        $factorsCount = 0;

        if (isset($nlp1['topWords']) && isset($nlp2['topWords'])) {
            $commonTopWords = array_intersect_key($nlp1['topWords'], $nlp2['topWords']);
            $similarity += count($commonTopWords) / max(count($nlp1['topWords']), count($nlp2['topWords']));
            $factorsCount++;
        }

        if (isset($nlp1['sentiment']) && isset($nlp2['sentiment'])) {
            $similarity += 1 - abs($nlp1['sentiment'] - $nlp2['sentiment']);
            $factorsCount++;
        }

        if (isset($nlp1['textComplexity']) && isset($nlp2['textComplexity'])) {
            $maxComplexity = max($nlp1['textComplexity'], $nlp2['textComplexity']);
            $similarity += 1 - (abs($nlp1['textComplexity'] - $nlp2['textComplexity']) / $maxComplexity);
            $factorsCount++;
        }

        return $factorsCount > 0 ? $similarity / $factorsCount : 0.0;
    }

    private function getPageContent($pageUid): string
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
        $result = $queryBuilder
            ->select('bodytext')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pageUid, \PDO::PARAM_INT)),
                $queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT))
            )
            ->executeQuery();

        $content = '';
        while ($row = $result->fetchAssociative()) {
            $content .= $row['bodytext'] . ' ';
        }

        return trim($content);
    }

    private function analyzeSentiment(string $text): float
    {
        $positiveWords = ['bon', 'excellent', 'super', 'génial', 'heureux', 'content'];
        $negativeWords = ['mauvais', 'terrible', 'horrible', 'triste', 'mécontent'];

        $tokens = $this->tokenizer->tokenize(strtolower($text));
        $positiveCount = count(array_intersect($tokens, $positiveWords));
        $negativeCount = count(array_intersect($tokens, $negativeWords));

        $totalWords = count($tokens);
        if ($totalWords === 0) {
            return 0;
        }

        return ($positiveCount - $negativeCount) / $totalWords;
    }

    private function removeStopWords(array $tokens): array
    {
        return array_values(array_diff($tokens, $this->stopWords));
    }

    private function getTopWords(array $words, int $limit): array
    {
        $wordCounts = array_count_values($words);
        arsort($wordCounts);
        return array_slice($wordCounts, 0, $limit, true);
    }

    private function calculateTextComplexity(array $tokens): float
    {
        if (empty($tokens)) {
            return 0.0;
        }
        $totalLength = array_sum(array_map('strlen', $tokens));
        $averageWordLength = $totalLength / count($tokens);
        return $averageWordLength;
    }

    public function calculateSimilarity(string $text1, string $text2): float
    {
        $tokens1 = $this->removeStopWords($this->tokenizer->tokenize($text1));
        $tokens2 = $this->removeStopWords($this->tokenizer->tokenize($text2));

        return $this->similarity->similarity($tokens1, $tokens2);
    }

    private function getFrenchStopWords(): array
    {
        return [
            'le', 'la', 'les', 'un', 'une', 'des', 'du', 'de', 'et', 'est', 'il', 'elle', 'je', 'tu', 'nous', 'vous',
            'ils', 'elles', 'on', 'ce', 'cet', 'cette', 'ces', 'mon', 'ton', 'son', 'ma', 'ta', 'sa', 'mes', 'tes', 'ses',
            'notre', 'votre', 'leur', 'nos', 'vos', 'leurs', 'que', 'qui', 'quoi', 'dont', 'où', 'quand', 'comment',
            'pourquoi', 'quel', 'quelle', 'quels', 'quelles', 'au', 'aux', 'avec', 'sans', 'ne', 'pas', 'plus', 'moins',
            'aussi', 'très', 'trop', 'peu', 'beaucoup', 'assez', 'tout', 'tous', 'toute', 'toutes', 'autre', 'autres',
            'même', 'mêmes', 'tel', 'telle', 'tels', 'telles', 'tout', 'tous', 'toute', 'toutes', 'aucun', 'aucune',
            'aucuns', 'aucunes', 'pour', 'par', 'en', 'dans', 'sur', 'sous', 'entre', 'vers', 'chez', 'jusque', 'à',
            'au', 'aux', 'de', 'du', 'des', 'un', 'une', 'le', 'la', 'les'
        ];
    }

    
    public function calculateNlpStatistics(array $analysisResults): array
    {
        $totalWordCount = 0;
        $totalUniqueWordCount = 0;
        $totalComplexity = 0;
        $allTopWords = [];
        $sentimentScores = [];

        foreach ($analysisResults as $pageData) {
            if (isset($pageData['nlp'])) {
                $nlpData = $pageData['nlp'];
                $totalWordCount += $nlpData['wordCount'] ?? 0;
                $totalUniqueWordCount += $nlpData['uniqueWordCount'] ?? 0;
                $totalComplexity += $nlpData['textComplexity'] ?? 0;
                
                if (isset($nlpData['topWords'])) {
                    foreach ($nlpData['topWords'] as $word => $count) {
                        if (!isset($allTopWords[$word])) {
                            $allTopWords[$word] = 0;
                        }
                        $allTopWords[$word] += $count;
                    }
                }

                if (isset($nlpData['sentiment'])) {
                    $sentimentScores[] = $nlpData['sentiment'];
                }
            }
        }

        $pageCount = count($analysisResults);
        
        arsort($allTopWords);
        $allTopWords = array_slice($allTopWords, 0, 10, true);

        return [
            'averageWordCount' => $pageCount > 0 ? $totalWordCount / $pageCount : 0,
            'averageUniqueWordCount' => $pageCount > 0 ? $totalUniqueWordCount / $pageCount : 0,
            'averageComplexity' => $pageCount > 0 ? $totalComplexity / $pageCount : 0,
            'topWords' => $allTopWords,
            'averageSentiment' => !empty($sentimentScores) ? array_sum($sentimentScores) / count($sentimentScores) : null,
        ];
    }


}