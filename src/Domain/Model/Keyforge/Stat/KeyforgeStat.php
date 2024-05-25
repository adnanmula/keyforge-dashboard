<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Stat;

use AdnanMula\Cards\Domain\Model\Keyforge\Stat\ValueObject\KeyforgeStatCategory;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final readonly class KeyforgeStat implements \JsonSerializable
{
    public function __construct(
        public Uuid $id,
        public KeyforgeStatCategory $category,
        public ?Uuid $reference,
        public array $data,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->value(),
            'category' => $this->category->value,
            'reference' => $this->reference?->value(),
            'data' => $this->data,
        ];
    }
}
