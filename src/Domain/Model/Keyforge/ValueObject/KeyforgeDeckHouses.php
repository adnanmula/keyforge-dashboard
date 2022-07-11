<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\ValueObject;

final class KeyforgeDeckHouses
{
    private array $value;

    final protected function __construct(array $houses)
    {
        $this->assert($houses);

        $this->value = $houses;
    }

    public static function from(KeyforgeHouse ...$houses)
    {
        return new self($houses);
    }

    public function value(): array
    {
        return $this->value;
    }

    private function assert($houses): void
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
}
