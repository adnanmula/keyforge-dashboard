<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckHouses;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UuidValueObject;

final class KeyforgeDeck
{
    private const MODEL_NAME = 'keyforge_deck';

    public function __construct(
        private UuidValueObject $id,
        private string $name,
        private KeyforgeSet $set,
        private KeyforgeDeckHouses $houses,
        private int $sas,
        private int $wins,
        private int $losses,
        private array $extraData
    ) {}

    public function id(): UuidValueObject
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function set(): KeyforgeSet
    {
        return $this->set;
    }

    public function houses(): KeyforgeDeckHouses
    {
        return $this->houses;
    }

    public function sas(): int
    {
        return $this->sas;
    }

    public function wins(): int
    {
        return $this->wins;
    }

    public function losses(): int
    {
        return $this->losses;
    }

    public function extraData(): array
    {
        return $this->extraData;
    }

    public static function modelName(): string
    {
        return self::MODEL_NAME;
    }
}
