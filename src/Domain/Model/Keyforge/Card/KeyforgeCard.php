<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Card;

use AdnanMula\Cards\Domain\Model\Keyforge\Card\ValueObject\KeyforgeCardType;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeHouse;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Shared\LocalizedString;

final readonly class KeyforgeCard implements \JsonSerializable
{
    public function __construct(
        public int $id,
        public array $houses,
        public LocalizedString $name,
        public string $nameUrl,
        public ?LocalizedString $flavorText,
        public LocalizedString $text,
        public KeyforgeCardType $type,
        public array $traits,
        public int $amber,
        public ?int $power,
        public ?int $armor,
        public bool $isBig,
        public bool $isToken,
        public array $sets,
        public array $tags,
    ) {
    }

    public static function fromDokData(array $data): self
    {
        $flavorText = null === $data['flavorText']
            ? null
            : LocalizedString::fromLocale($data['flavorText'], Locale::en_GB);

        return new self(
            id: $data['id'],
            houses: \array_map(static fn (string $h) => KeyforgeHouse::fromDokName($h), $data['houses']),
            name: LocalizedString::fromLocale($data['cardTitle'], Locale::en_GB),
            nameUrl: $data['cardTitleUrl'],
            flavorText: $flavorText,
            text: LocalizedString::fromLocale($data['cardText'], Locale::en_GB),
            type: KeyforgeCardType::from(\strtoupper($data['cardType'])),
            traits: $data['traits'],
            amber: $data['amber'],
            power: $data['power'],
            armor: $data['armor'],
            isBig: $data['big'],
            isToken: $data['token'],
            sets: \array_map(static fn (array $e) => KeyforgeSet::fromDokName($e['expansion']), $data['expansions']),
            tags: \array_map(static fn (array $t) => $t['trait'], $data['extraCardInfo']['traits']),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'houses' => $this->houses,
            'name' => $this->name->jsonSerialize(),
            'nameUrl' => $this->nameUrl,
            'flavorText' => $this->flavorText->jsonSerialize(),
            'text' => $this->text->jsonSerialize(),
            'type' => $this->type->jsonSerialize(),
            'traits' => $this->traits,
            'amber' => $this->amber,
            'power' => $this->power,
            'armor' => $this->armor,
            'isBig' => $this->isBig,
            'isToken' => $this->isToken,
            'sets' => $this->sets,
            'tags' => $this->tags,
        ];
    }
}
