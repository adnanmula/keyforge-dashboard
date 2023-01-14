<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared\ValueObject;

enum CompetitionType implements \JsonSerializable
{
    case ROUND_ROBIN_1;
    case ROUND_ROBIN_2;
    case ROUND_ROBIN_3;
    case ROUND_ROBIN_4;
    case ELIMINATION;
    case ROUND_ROBIN_1_ELIMINATION;
    case ROUND_ROBIN_2_ELIMINATION;

    public static function from(string $value): self
    {
        return match ($value) {
            self::ROUND_ROBIN_1->name => self::ROUND_ROBIN_1,
            self::ROUND_ROBIN_2->name => self::ROUND_ROBIN_2,
            self::ROUND_ROBIN_3->name => self::ROUND_ROBIN_3,
            self::ROUND_ROBIN_4->name => self::ROUND_ROBIN_4,
            self::ELIMINATION->name => self::ELIMINATION,
            self::ROUND_ROBIN_1_ELIMINATION->name => self::ROUND_ROBIN_1_ELIMINATION,
            self::ROUND_ROBIN_2_ELIMINATION->name => self::ROUND_ROBIN_2_ELIMINATION,
        };
    }

    public static function allowedValues(): array
    {
        return [
            self::ROUND_ROBIN_1->name,
            self::ROUND_ROBIN_2->name,
            self::ROUND_ROBIN_3->name,
            self::ROUND_ROBIN_4->name,
            self::ELIMINATION->name,
            self::ROUND_ROBIN_1_ELIMINATION->name,
            self::ROUND_ROBIN_2_ELIMINATION->name,
        ];
    }

    public function isRoundRobin(): bool
    {
        return $this === self::ROUND_ROBIN_1
            || $this === self::ROUND_ROBIN_2
            || $this === self::ROUND_ROBIN_3
            || $this === self::ROUND_ROBIN_4;
    }

    public function jsonSerialize(): string
    {
        return $this->name;
    }
}
