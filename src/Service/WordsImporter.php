<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\SentenceFinder\WordDTO;
use App\Exception\WordImporter\InvalidRowFormatException;
use App\Exception\WordImporter\UnrecognisedWordClassException;

class WordsImporter
{
    private string $wordsDelimiter;
    private string $meaningDelimiter;
    private bool $ignoreBracketsInSrcWords;
    private string $wordAndMeaningSeparator;

    public function __construct(
        string $wordsDelimiter = PHP_EOL,
        string $meaningDelimiter = ',',
        string $wordAndMeaningSeparator = "\t",
        bool $ignoreBracketsInSrcWords = true
    )
    {
        $this->wordsDelimiter = $wordsDelimiter;
        $this->meaningDelimiter = $meaningDelimiter;
        $this->wordAndMeaningSeparator = $wordAndMeaningSeparator;
        $this->ignoreBracketsInSrcWords = $ignoreBracketsInSrcWords;
    }

    public function transformTextToWords(string $text, string $wordClass = WordDTO::class): array
    {
        $rows = $this->explodeRowsWithSourceWordAndMeaning($text);

        switch ($wordClass) {
            case WordDTO::class:
                return $this->explodeWordDTOsFromRows($rows);

        }

        throw new UnrecognisedWordClassException(sprintf('Class %s not recognized', $wordClass));
    }

    private function explodeRowsWithSourceWordAndMeaning(string $text): array
    {
        return explode($this->wordsDelimiter, trim($text));
    }

    private function explodeWordDTOsFromRows(array $rows): array
    {
        return array_map(function (string $row) {
            $sourceWordAndMeaning = $this->explodeSourceWordAndMeaningsFromRow($row);

            $sourceWord = $this->clearSourceWordFromBrackets($sourceWordAndMeaning[0]);
            $meanings = $this->explodeMeaningsFromString($sourceWordAndMeaning[1]);
            return new WordDTO($sourceWord, $meanings);
        }, $rows);
    }

    private function clearSourceWordFromBrackets(string $word): string
    {
        do {
            $prevWord = $word;
            $word = preg_replace('/\([^\)\(]*\)/', '', $word);
        } while ($word != $prevWord);

        return trim($word);
    }

    private function explodeSourceWordAndMeaningsFromRow(string $row): array
    {
        $result = explode($this->wordAndMeaningSeparator, trim($row));

        if (count($result) !== 2) {
            throw new InvalidRowFormatException(sprintf('Invalid row format for "%s"', $row));
        }

        return $result;
    }

    private function explodeMeaningsFromString(string $meanings): array
    {
        return array_map('trim', explode($this->meaningDelimiter, $meanings));
    }
}