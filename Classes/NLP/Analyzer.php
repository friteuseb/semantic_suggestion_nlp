<?php
namespace TalanHdf\SemanticSuggestionNlp\NLP;

use NlpTools\Tokenizers\WhitespaceTokenizer;
use NlpTools\Stemmers\PorterStemmer;
use NlpTools\Similarity\CosineSimilarity;

class Analyzer
{
    private $tokenizer;
    private $stemmer;
    private $similarity;
    private $stopWords;

    public function __construct()
    {
        $this->tokenizer = new WhitespaceTokenizer();
        $this->stemmer = new PorterStemmer();
        $this->similarity = new CosineSimilarity();
        $this->stopWords = $this->getFrenchStopWords();
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
        ];
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
}