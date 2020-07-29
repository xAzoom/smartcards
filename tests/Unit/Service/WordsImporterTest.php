<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\DTO\SentenceFinder\WordDTO;
use App\Exception\WordImporter\InvalidRowFormatException;
use App\Exception\WordImporter\UnrecognisedWordClassException;
use App\Service\WordsImporter;
use PHPUnit\Framework\TestCase;

class WordsImporterTest extends TestCase
{
    /**
     * @param array[] $expectedWord
     * @dataProvider wordsProvider
     */
    public function testImportFromTextTOWordDTO(string $text, array $expectedWord): void
    {
        $wordsImporter = new WordsImporter();

        $word = $wordsImporter->transformTextToWords($text, WordDTO::class);

        $this->assertEqualsCanonicalizing($expectedWord, $word);
    }

    public function testImportFromTextToUnrecognisedWordClass(): void
    {
        $wordsImporter = new WordsImporter();
        $this->expectException(UnrecognisedWordClassException::class);

        $wordsImporter->transformTextToWords('text tekst', 'App\DTO\SentenceFinder\WordDTOUnrecognised');
    }

    /**
     * @dataProvider invalidTextProvider
     */
    public function testImportFromInvalidText(string $text): void
    {
        $wordsImporter = new WordsImporter();
        $this->expectException(InvalidRowFormatException::class);

        $wordsImporter->transformTextToWords($text, WordDTO::class);
    }

    /**
     * @return array[]
     */
    public function wordsProvider(): array
    {
        return [
            ['survey	ankieta', [new WordDTO('survey', ['ankieta'])]],
            ['eradication	likwidacja, wytępienie', [new WordDTO('eradication', ['likwidacja', 'wytępienie'])]],
            ['stand (stood stood)	stać, postawić, ustawić, znieść (coś)', [new WordDTO('stand', ['stać', 'postawić', 'ustawić', 'znieść (coś)'])]],
            [file_get_contents(dirname(__FILE__) . '/../../Resources/WordsImporter/words_to_import.txt'), [
                new WordDTO('survey', ['ankieta']),
                new WordDTO('eradication', ['likwidacja', 'wytępienie']),
                new WordDTO('stand', ['stać', 'postawić', 'ustawić', 'znieść (coś)']),
                new WordDTO('feel', ['czuć']),
            ]]
        ];
    }

    /**
     * @return array[]
     */
    public function invalidTextProvider(): array
    {
        return [
            ['text tekst'],
            ['lorem'],
            ['text1	text2	text3'],
        ];
    }
}