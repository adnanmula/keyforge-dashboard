<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject;

final class KeyforgeDeckHouses implements \JsonSerializable
{
    private array $value;

    final protected function __construct(array $houses)
    {
        $this->assert($houses);

        $this->value = $houses;
    }

    public static function from(KeyforgeHouse ...$houses): self
    {
        return new self($houses);
    }

    public static function fromDokData(array $data): self
    {
        $houses = \array_map(static fn (array $data) => $data['house'], $data['deck']['housesAndCards']);

        return self::from(
            KeyforgeHouse::fromDokName($houses[0]),
            KeyforgeHouse::fromDokName($houses[1]),
            KeyforgeHouse::fromDokName($houses[2]),
        );
    }

    /** @return array<KeyforgeHouse> */
    public function value(): array
    {
        return $this->value;
    }

    private function assert(array $houses): void
    {
        if (\count($houses) !== 3) {
            throw new \InvalidArgumentException('Invalid house configuration');
        }

        foreach ($houses as $house) {
            if (false === $house instanceof KeyforgeHouse) {
                throw new \InvalidArgumentException('Invalid house configuration');
            }
        }
    }

    public function jsonSerialize(): array
    {
        return [$this->value[0]->name, $this->value[1]->name, $this->value[2]->name];
    }
}
