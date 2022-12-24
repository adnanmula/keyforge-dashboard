<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared\ValueObject;

enum TagVisibility: string
{
    case PRIVATE = 'PRIVATE';
    case PUBLIC = 'PUBLIC';

    public static function allowedValues(): array
    {
        return [
            self::PRIVATE->name,
            self::PUBLIC->name,
        ];
    }
}
