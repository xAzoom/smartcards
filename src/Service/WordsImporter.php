<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\SentenceFinder\WordDTO;
use App\Exception\ExplodeEmptyDelimiterException;
use App\Exception\PregReplaceException;
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

    /**
     * @return WordDTO[]
     * @throws UnrecognisedWordClassException
     */
    public function transformTextToWords(string $text, string $wordClass = WordDTO::class): array
    {
        $rows = $this->explodeRowsWithSourceWordAndMeaning($text);

        switch ($wordClass) {
            case WordDTO::class:
                return $this->explodeWordDTOsFromRows($rows);

        }

        throw new UnrecognisedWordClassException(sprintf('Class %s not recognized', $wordClass));
    }

    /**
     * @return string[]
     * @throws ExplodeEmptyDelimiterException
     */
    private function explodeRowsWithSourceWordAndMeaning(string $text): array
    {
        $result = explode($this->wordsDelimiter, trim($text));
        if (false === $result) {
            throw new ExplodeEmptyDelimiterException('Explode has empty delimiter param (wordsDelimiter).');
        }

        return $result;
    }

    /**
     * @param string[] $rows
     * @return WordDTO[]
     */
    private function explodeWordDTOsFromRows(array $rows): array
    {
        return array_map(function (string $row) {
            $sourceWordAndMeaning = $this->explodeSourceWordAndMeaningsFromRow($row);

            $sourceWord = $this->clearSourceWordFromBrackets($sourceWordAndMeaning[0]);
            $meanings = $this->explodeMeaningsFromString($sourceWordAndMeaning[1]);
            return new WordDTO($sourceWord, $meanings);
        }, $rows);
    }

    /**
     * @throws PregReplaceException
     */
    private function clearSourceWordFromBrackets(string $word): string
    {
        do {
            $prevWord = $word;

            $word = preg_replace('/\([^\)\(]*\)/', '', $word);
            if (is_null($word)) {
                throw new PregReplaceException(sprintf('Preg_replace error occurred: %d', preg_last_error()));
            }
        } while ($word != $prevWord);

        return trim($word);
    }

    /**
     * @return string[]
     * @throws InvalidRowFormatException|ExplodeEmptyDelimiterException
     */
    private function explodeSourceWordAndMeaningsFromRow(string $row): array
    {
        $result = explode($this->wordAndMeaningSeparator, trim($row));

        if (false === $result) {
            throw new ExplodeEmptyDelimiterException('Explode has empty delimiter param (wordAndMeaningSeparator).');
        }

        if (count($result) !== 2) {
            throw new InvalidRowFormatException(sprintf('Invalid row format for "%s"', $row));
        }

        return $result;
    }

    /**
     * @return string[]
     * @throws ExplodeEmptyDelimiterException
     */
    private function explodeMeaningsFromString(string $meanings): array
    {
        $result = explode($this->meaningDelimiter, $meanings);
        if (false === $result) {
            throw new ExplodeEmptyDelimiterException('Explode has empty delimiter param (meaningDelimiter).');
        }

        return array_map('trim', $result);
    }
}