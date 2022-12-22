<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared\ValueObject;

enum CompetitionType
{
    case ROUND_ROBIN_1;
    case ROUND_ROBIN_2;
    case ELIMINATION;
    case ROUND_ROBIN_1_ELIMINATION;
    case ROUND_ROBIN_2_ELIMINATION;

    public static function from(string $value): static
    {
        switch ($value) {
            case self::ROUND_ROBIN_1->name:
                return self::ROUND_ROBIN_1;
            case self::ROUND_ROBIN_2->name:
                return self::ROUND_ROBIN_2;
            case self::ELIMINATION->name:
                return self::ELIMINATION;
            case self::ROUND_ROBIN_1_ELIMINATION->name:
                return self::ROUND_ROBIN_1_ELIMINATION;
            case self::ROUND_ROBIN_2_ELIMINATION->name:
                return self::ROUND_ROBIN_2_ELIMINATION;
            default:
                return self::ROUND_ROBIN_1;
        }
    }

    public static function allowedValues(): array
    {
        return [
            self::ROUND_ROBIN_1->name,
            self::ROUND_ROBIN_2->name,
            self::ELIMINATION->name,
            self::ROUND_ROBIN_1_ELIMINATION->name,
            self::ROUND_ROBIN_2_ELIMINATION->name,
        ];
    }
}
