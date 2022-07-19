<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Service;

final class Json
{
    public static function decode(string $value): array
    {
        return \json_decode($value, true, 512, \JSON_THROW_ON_ERROR);
    }

    public static function encode(array|\JsonSerializable $value): string
    {
        return \json_encode($value, \JSON_THROW_ON_ERROR);
    }
}
