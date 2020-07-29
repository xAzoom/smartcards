<?php

namespace App\DTO\SentenceFinder;

class WordDTO
{
    private string $word;

    /** @var string[] */
    private array $meanings;

    /**
     * @param string[] $meanings
     */
    public function __construct(string $word, array $meanings)
    {
        $this->word = $word;
        $this->meanings = $meanings;
    }

    public function getWord(): string
    {
        return $this->word;
    }

    /**
     * @return string[]
     */
    public function getMeanings(): array
    {
        return $this->meanings;
    }
}