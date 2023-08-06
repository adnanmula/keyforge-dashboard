<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Analyzer;

final readonly class DeckAnalyzeResult implements \JsonSerializable
{
    public function __construct(
        public string $category,
        public string $subcategory,
        public array $results,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            $this->category => [
                $this->subcategory => $this->results,
            ]
        ];
    }
}
