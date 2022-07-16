<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\ValueObject;

enum KeyforgeHouse: string implements \JsonSerializable
{
    case BROBNAR = 'BROBNAR';
    case DIS = 'DIS';
    case MARS = 'MARS';
    case SHADOWS = 'SHADOWS';
    case UNTAMED = 'UNTAMED';
    case SANCTUM = 'SANCTUM';
    case LOGOS = 'LOGOS';
    case SAURIAN = 'SAURIAN';
    case STAR_ALLIANCE = 'STAR_ALLIANCE';
    case UNFATHOMABLE = 'UNFATHOMABLE';

    public static function fromDokName(string $house): static
    {
        $houses = \array_map(static fn (KeyforgeHouse $case) => $case->name, self::cases());

        if (\in_array(\strtoupper($house), $houses, true)) {
            return self::from(\strtoupper($house));
        }

        if ($house === 'StarAlliance') {
            return self::STAR_ALLIANCE;
        }

        throw new \InvalidArgumentException($house);
    }

    public function jsonSerialize(): mixed
    {
        return $this->name;
    }
}
