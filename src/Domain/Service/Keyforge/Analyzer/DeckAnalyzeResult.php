<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Analyzer;

final readonly class DeckAnalyzeResult implements \JsonSerializable
{
    public function __construct(
        public string $key,
        public array $results,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            $this->key => $this->results,
        ];
    }
}
