<?php

namespace App\DTO\SentenceFinder;

class WordDTO
{
    private string $word;

    private array $meanings;

    public function __construct(string $word, array $meanings)
    {
        $this->word = $word;
        $this->meanings = $meanings;
    }

    public function getWord(): string
    {
        return $this->word;
    }

    public function getMeanings(): array
    {
        return $this->meanings;
    }
}