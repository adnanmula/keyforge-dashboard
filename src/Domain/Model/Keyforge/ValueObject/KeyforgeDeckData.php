<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\ValueObject;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeDeckData implements \JsonSerializable
{
    private function __construct(
        public Uuid $id,
        public int $dokId,
        public string $name,
        public array $rawData,
        public KeyforgeSet $set,
        public KeyforgeDeckHouses $houses,
        public KeyforgeDeckStats $stats,
        public KeyforgeCards $cards,
    ) {}

    public static function fromDokData(array $data): self
    {
        return new self(
            Uuid::from($data['deck']['keyforgeId']),
            $data['deck']['id'],
            $data['deck']['name'],
            $data,
            KeyforgeSet::fromDokName($data['deck']['expansion']),
            KeyforgeDeckHouses::fromDokData($data),
            KeyforgeDeckStats::fromDokData($data),
            KeyforgeCards::fromDokData($data),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->value(),
            'dok_id' => $this->dokId,
            'name' => $this->name,
            'raw_data' => $this->rawData,
            'houses' => $this->houses->jsonSerialize(),
            'stats' => $this->stats->jsonSerialize(),
            'cards' => $this->cards->jsonSerialize(),
        ];
    }
}
